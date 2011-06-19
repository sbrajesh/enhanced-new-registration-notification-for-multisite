<?php
/**
 * Plugin Name:Enhanced User registration Notification for WordPress Multisite Site Admin
 * Version: 1.0
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh
 * Plugin URI: http://buddydev.com/plugins/enhanced-new-user-notification-for-wp-multisite/
 * Last Updated: March 06, 2011
 * Network: true
 * License: GPL
 */
remove_action( 'wpmu_new_user', 'newuser_notify_siteadmin' );
add_action( 'wpmu_new_user', 'newuser_notify_siteadmin_enhanced' );

function newuser_notify_siteadmin_enhanced( $user_id ) {
	if ( get_site_option( 'registrationnotification' ) != 'yes' )
		return false;

	$email = get_site_option( 'admin_email' );

	if ( is_email($email) == false )
		return false;

	$user = new WP_User($user_id);
        if(function_exists("bp_core_get_user_domain"))//just make sure to not cause trouble when bp is disables
            $user_link= bp_core_get_user_domain($user_id);
        else
            $user_link=network_admin_url ("user-edit.php?user_id=".$user_id);//just making sure it works on normal wpms installs too
	$options_site_url = esc_url(network_admin_url('ms-options.php'));
	$msg = sprintf(__('New User: %1s
Remote IP: %2s
User email: %3s
View Profile: %4s

Disable these notifications: %5s'), $user->user_login, $_SERVER['REMOTE_ADDR'],$user->user_email, $user_link,  $options_site_url);

	$msg = apply_filters( 'newuser_notify_siteadmin_enhanced', $msg,$user );
	wp_mail( $email, sprintf(apply_filters("new_user_registration_message_subject",__('New User Registration: %s')), $user->user_login), $msg );
	return true;
}

?>