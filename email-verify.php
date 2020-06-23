<?php
/*
Plugin Name: Email Verify
Description: Verifies your Users email addresses and blocks them from register to your site.
Version: 1.1.4
Author: EverPress
Author URI: https://about.me/xaver
Text Domain: email-verify
License: GPLv2 or later
*/


define( 'EMAILVERIFY_VERSION', '1.1.4' );
define( 'EMAILVERIFY_FILE', __FILE__ );

require_once dirname( __FILE__ ) . '/classes/emailverify.class.php';
new EmailVerify();
