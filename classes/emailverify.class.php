<?php

class EmailVerify {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( EMAILVERIFY_FILE );
		$this->plugin_url = plugin_dir_url( EMAILVERIFY_FILE );

		register_activation_hook( EMAILVERIFY_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( EMAILVERIFY_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'email-verify' );

		add_filter( 'registration_errors', array( &$this, 'registration_errors' ), 10, 3 );
		add_filter( 'user_profile_update_errors', array( &$this, 'user_profile_update_errors' ), 10, 3 );
		add_action( 'admin_menu', array( &$this, 'menu' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );

	}


	public function activate( $network_wide ) {

		$defaults = array(
			'email_verify_check_mx' => true,
			'email_verify_check_smtp' => false,
			'email_verify_check_error' => __( 'Sorry, your email address is not accepted!', 'email-verify' ),
			'email_verify_dep' => true,
			'email_verify_dep_error' => __( 'Sorry, your email address is not accepted!', 'email-verify' ),
			'email_verify_domains' => '',
			'email_verify_domains_error' => __( 'Sorry, your email address is not accepted!', 'email-verify' ),
			'email_verify_emails' => '',
			'email_verify_emails_error' => __( 'Sorry, your email address is not accepted!', 'email-verify' ),
			'email_verify_whitelist' => '',
			'email_verify_whitelist_emails' => '',
		);

		foreach ( $defaults as $key => $value ) {
			if ( false == get_option( $key ) ) {
				add_option( $key, $value );
			}
		}

	}


	public function deactivate( $network_wide ) {}


	public function menu() {
		add_options_page( __( 'Email Verification', 'email-verify' ), __( 'Email Verification', 'email-verify' ), 'manage_options', 'email_verify', array( &$this, 'settings' ) );
	}

	public function register_settings() {

		register_setting( 'email-verify', 'email_verify_check_mx', array( &$this, 'validate_mx' ) );
		register_setting( 'email-verify', 'email_verify_check_smtp', array( &$this, 'validate_smtp' ) );
		register_setting( 'email-verify', 'email_verify_check_error' );
		register_setting( 'email-verify', 'email_verify_dep' );
		register_setting( 'email-verify', 'email_verify_dep_error' );
		register_setting( 'email-verify', 'email_verify_emails', array( &$this, 'validate_textarea' ) );
		register_setting( 'email-verify', 'email_verify_emails_error' );
		register_setting( 'email-verify', 'email_verify_domains', array( &$this, 'validate_textarea' ) );
		register_setting( 'email-verify', 'email_verify_domains_error' );
		register_setting( 'email-verify', 'email_verify_whitelist', array( &$this, 'validate_textarea' ) );
		register_setting( 'email-verify', 'email_verify_whitelist_emails', array( &$this, 'validate_textarea' ) );

	}


	public function user_profile_update_errors( $errors, $update, $user ) {

		if ( ! $user ) {
			return $errors;
		}

		$user_email = $user->user_email;

		if ( is_wp_error( $result = $this->verify( $user_email ) ) ) {
			if ( $errors instanceof WP_Error ) {
				$errors->add( $result->get_error_code(), $result->get_error_message() );
			} elseif ( is_string( $errors ) ) {
				$errors .= '<br />' . $result->get_error_message();
			}
		}

		return $errors;
	}


	public function registration_errors( $errors, $sanitized_user_login, $user_email ) {

		if ( is_wp_error( $result = $this->verify( $user_email ) ) ) {
			if ( $errors instanceof WP_Error ) {
				$errors->add( $result->get_error_code(), $result->get_error_message() );
			} elseif ( is_string( $errors ) ) {
				$errors .= '<br />' . $result->get_error_message();
			}
		}

		return $errors;
	}

	public function verify( $email ) {

		list( $user, $domain ) = explode( '@', $email );

		// check for white listed email addresses
		$whitelisted_emails = explode( "\n", get_option( 'email_verify_whitelist_emails', '' ) );
		if ( in_array( $email, $whitelisted_emails ) ) {
			return true;
		}

		// check for email addresses
		$blacklisted_emails = explode( "\n", get_option( 'email_verify_emails', '' ) );
		if ( in_array( $email, $blacklisted_emails ) ) {
			return new WP_Error( 'email_verify_emails_error', get_option( 'email_verify_emails_error' ), 'email' );
		}

		// check for white listed
		$whitelisted_domains = explode( "\n", get_option( 'email_verify_whitelist', '' ) );
		if ( in_array( $domain, $whitelisted_domains ) ) {
			return true;
		}

		// check for domains
		$blacklisted_domains = explode( "\n", get_option( 'email_verify_domains', '' ) );
		if ( in_array( $domain, $blacklisted_domains ) ) {
			return new WP_Error( 'email_verify_domains_error', get_option( 'email_verify_domains_error' ), 'email' );
		}

		// check DEP
		if ( $dep_domains = $this->get_dep_domains( false ) ) {
			if ( in_array( $domain, $dep_domains ) ) {
				return new WP_Error( 'email_verify_dep_error', get_option( 'email_verify_dep_error' ), 'email' );
			}
		}

		// check MX record
		if ( get_option( 'email_verify_check_mx' ) && function_exists( 'checkdnsrr' ) ) {
			if ( ! checkdnsrr( $domain, 'MX' ) ) {
				return new WP_Error( 'email_verify_mx_error', get_option( 'email_verify_check_error' ), 'email' );
			}
		}

		// check via SMTP server
		if ( get_option( 'email_verify_check_smtp' ) ) {

			$valid = $this->smtp_check( $email );
			if ( ! $valid ) {
				return new WP_Error( 'email_verify_smtp_error', get_option( 'email_verify_check_error' ), 'email' );
			}
		}

		return true;

	}


	public function get_dep_domains() {

		if ( ! get_option( 'email_verify_dep' ) ) {
			return array();
		}

		$file = $this->plugin_path . '/dep.txt';
		if ( ! file_exists( $file )  ) {
			update_option( 'email_verify_dep', false );
			return array();
		}
		$raw = @file_get_contents( $file );
		$domains = explode( "\n", $raw );
		return $domains;

	}


	public function smtp_check( $email, $from = null ) {
		if ( is_null( $from ) ) {
			$from = get_option( 'admin_email' );
		}
		list( $user, $domain ) = explode( '@', $email );

		require_once $this->plugin_path . '/classes/smtp-validate-email.php';

		$validator = new SMTP_Validate_Email( $email, $from );
		$smtp_results = $validator->validate();
		$valid = (isset( $smtp_results[ $email ] ) && 1 == $smtp_results[ $email ]) || ! ! array_sum( $smtp_results['domains'][ $domain ]['mxs'] );

		return $valid;

	}


	public function settings() {

		include $this->plugin_path . '/views/settings.php';

	}


	public function validate_mx( $input ) {
		if ( $input ) {
			if ( ! ($input = checkdnsrr( 'google.com', 'MX' )) ) {
				add_settings_error( 'email_verify_check_mx', 'no_mx', __( 'Not able to use MX record check on your server!', 'email-verify' ) );
			}
		}
		return $input;
	}

	public function validate_smtp( $input ) {
		if ( $input ) {
			if ( ! ($input = $this->smtp_check( get_option( 'admin_email' ) )) ) {
				add_settings_error( 'email_verify_check_mx', 'no_mx', __( 'Not able to use SMTP check record check on your server!', 'email-verify' ) );
			}
		}
		return $input;
	}


	public function validate_textarea( $input ) {

		$input = trim( preg_replace( '/(?:(?:\r\n|\r|\n|\s)\s*){2}/s', "\n", $input ) );
		$input = explode( "\n", $input );
		$input = array_unique( $input );
		sort( $input );
		$input = implode( "\n", $input );

		return $input;
	}

}
