<?php

/**
 * Customize the WordPress New User Notification
 *
 * @since 0.0.1
 */

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @since 2.0.0
 * @since 4.3.0 The `$plaintext_pass` parameter was changed to `$notify`.
 * @since 4.3.1 The $plaintext_pass parameter was deprecated. $notify added as a third parameter.
 *
 * @param int    $user_id User ID.
 * @param string $notify  Whether admin and user should be notified ('both') or
 *                        only the admin ('admin' or empty).
 */
function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
	if ( $deprecated !== null ) {
		_deprecated_argument( __FUNCTION__, '4.3.1' );
	}

	global $wpdb, $wp_hasher;
	$user = get_userdata( $user_id );

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  $domain_name =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']); //retrieve the domain name without www so we can add it to the email header

  //Setup the Header for the Admin Notification
  $headers = sprintf(__('From: %s <no-reply@%s>'), $blogname, $domain_name) . "\r\n";

	$message  = '<p>' . sprintf(__('A new patient has been registered for %s:'), $blogname) . "</p>";
	$message .= '<p>' . sprintf(__('Username: %s'), $user->user_login) . "</p>";
	$message .= '<p>' . sprintf(__('E-mail: %s'), $user->user_email) . "</p>";

  I4Web_LMS()->i4_emails->send( get_option('admin_email'), sprintf(__('[%s] New Patient Registration'),  $blogname), $message );

	//@wp_mail(get_option('admin_email'), sprintf(__('[%s] New Patient Registration'), $blogname), $message, $headers);

	if ( 'admin' === $notify || empty( $notify ) ) {
		return;
	}

	// Generate something random for a password reset key.
	$key = wp_generate_password( 20, false );

	/** This action is documented in wp-login.php */
	do_action( 'retrieve_password_key', $user->user_login, $key );

	// Now insert the key, hashed, into the DB.
	if ( empty( $wp_hasher ) ) {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		$wp_hasher = new PasswordHash( 8, true );
	}
	$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	$message = sprintf(__('Hi %s,'), $user->first_name) . '<br>';
	$message .= 'You have been registered for Celebration Health Education online courses.<br><br>';
	$message .= sprintf(__('Your Username is - <strong> %s </strong>'), $user->user_login) . "\r\n\r\n";
	$message .= 'All you have to do next is set a password for your account. <br><br>';
	$message .= 'To set your password, visit the following address: <br><br>';
	$message .= '<a href="' . site_url() . '/wp-login.php?action=rp&key='.$key.'&login=' . rawurlencode($user->user_login) .'">Set Your Password</a><br><br>';
//$message .= '<' . site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . "> <br>";

	$message .= 'Once registered, you can access your online courses 24/7 at '.site_url() . "<br>";

	//wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);

  I4Web_LMS()->i4_emails->send( $user->user_email, sprintf(__('Welcome to %s'), $blogname), $message );
}
endif;

if ( !function_exists('wp_password_change_notification') ) :
/**
 * Notify the blog admin of a user changing password, normally via email.
 *
 * @since 1.0.0
 *
 * @param object $user User Object
 */
function wp_password_change_notification(&$user) {
	// send a copy of password change notification to the admin
	// but check to see if it's the admin whose password we're changing, and skip this
	if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {
		$message = 'Hi,<br>';
		$message .= sprintf(__('A password was recently changed for: <br><br>Username: <strong>%s</strong>'), $user->user_login) . "\r\n";
		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		I4Web_LMS()->i4_emails->send( get_option('admin_email'), sprintf(__('User Password Lost/Changed'),  $blogname), $message );

	//	wp_mail(get_option('admin_email'), sprintf(__('[%s] Password Lost/Changed'), $blogname), $message);
	}
}
endif;

/**
 * Customize the default From email address in WordPress that aren't sent from our custom email class
 *
 * @since 1.0.0
 */

add_filter( 'wp_mail_from', 'i4_default_mail_from' );
function i4_default_mail_from( $original_email_address ){

	$domain_name =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']); //retrieve the domain name without www so we can add it to the email header

	$from_address = 'no-reply@'.$domain_name;

	return $from_address;
}

/**
 * Customize the default "from" name for e-mails that aren't sent from our custom email class
 *
 * @since 1.0.0
 */
add_filter( 'wp_mail_from_name', 'i4_default_mail_from_name' );

function i4_default_mail_from_name( $original_email_from ) {
	$from_name =  wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	return $from_name;
}
