<div class="wrap">
<h1><?php esc_html_e( 'Email Verification Settings' , 'email-verify' ) ?></h1>
<form method="post" action="options.php">
	<?php settings_fields( 'email-verify' ); ?>
	<?php do_settings_sections( 'email-verify' ); ?>

<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Simple Checks' , 'email-verify' ) ?></th>
		<td>
		<p><label><input type="hidden" name="email_verify_check_mx" value=""><input type="checkbox" name="email_verify_check_mx" value="1" <?php checked( get_option( 'email_verify_check_mx' ) ); ?>><?php esc_html_e( 'Check MX record', 'mailster' );?></label><br><span class="description"><?php esc_html_e( 'Check the domain for an existing MX record.', 'email-verify' );?></span>
		</p>
		<p><label><input type="hidden" name="email_verify_check_smtp" value=""><input type="checkbox" name="email_verify_check_smtp" value="1" <?php checked( get_option( 'email_verify_check_smtp' ) ); ?>><?php esc_html_e( 'Validate via SMTP', 'mailster' );?></label><br><span class="description"><?php esc_html_e( 'Connects the domain\'s SMTP server to check if the address really exists.', 'email-verify' );?></span></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'email-verify' ) ?>:</strong>
		<input type="text" name="email_verify_check_error" value="<?php echo esc_attr( get_option( 'email_verify_check_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Disposable Email Provider' , 'email-verify' ) ?></th>
		<td>
		<p><label><input type="hidden" name="email_verify_dep" value=""><input type="checkbox" name="email_verify_dep" value="1" <?php checked( get_option( 'email_verify_dep' ) ); ?>><?php esc_html_e( 'reject email addresses from disposable email providers (DEP).', 'mailster' );?></label></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'email-verify' ) ?>:</strong>
		<input type="text" name="email_verify_dep_error" value="<?php echo esc_attr( get_option( 'email_verify_dep_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Blacklisted Email Addresses' , 'email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted email addresses. One email each line.' , 'email-verify' ) ?><br>
		<textarea name="email_verify_emails" placeholder="<?php echo "john@blacklisted.com\njane@blacklisted.co.uk\nhans@blacklisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( get_option( 'email_verify_emails' ) ) ?></textarea></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'email-verify' ) ?>:</strong>
		<input type="text" name="email_verify_emails_error" value="<?php echo esc_attr( get_option( 'email_verify_emails_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Reject Domains' , 'email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List of blacklisted domains. One domain each line.' , 'email-verify' ) ?><br>
		<textarea name="email_verify_domains" placeholder="<?php echo "blacklisted.com\nblacklisted.co.uk\nblacklisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( get_option( 'email_verify_domains' ) ) ?></textarea></p>
		<p><strong><?php esc_html_e( 'Error Message' , 'email-verify' ) ?>:</strong>
		<input type="text" name="email_verify_domains_error" value="<?php echo esc_attr( get_option( 'email_verify_domains_error' ) ) ?>" class="large-text"></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'White listed Email Addresses' , 'email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List domains which bypass the above rules. One domain each line.' , 'email-verify' ) ?><br>
		<textarea name="email_verify_whitelist_emails" placeholder="<?php echo "whitelisted.com\nwhitelisted.co.uk\nwhitelisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( get_option( 'email_verify_whitelist_emails' ) ) ?></textarea></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'White listed Domains' , 'email-verify' ) ?></th>
		<td>
		<p><?php esc_html_e( 'List email addresses which bypass the above rules. One email each line.' , 'email-verify' ) ?><br>
		<textarea name="email_verify_whitelist" placeholder="<?php echo "john@whitelisted.com\njane@whitelisted.co.uk\nhans@whitelisted.de"?>" class="code large-text" rows="10"><?php echo esc_attr( get_option( 'email_verify_whitelist' ) ) ?></textarea></p>
		</td>
	</tr>
</table>

	<?php submit_button(); ?>

</form>
</div>
