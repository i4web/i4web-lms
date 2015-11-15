<?php
/**
 * i-4Web LMS Functions
 *
 * @package I4Web_LMS
 * @subpackage Classes/Profile Form
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * If a user is logged in, get the user object and add a global $current_i4_user for use throughout the site and plugin
 *
 * @since 0.0.1
 */
function i4_get_user() {
    global $current_i4_user;
    //Check if the user is logged in
    if (is_user_logged_in()) {
        global $current_i4_user;
        $current_i4_user = wp_get_current_user(); //load the current iQuue user for use throughout the site
    }

}

add_action('plugins_loaded', 'i4_get_user');

/**
 * Grab the site settings and store as a global
 *
 * @since 0.0.1
 */
function i4_get_site_settings() {
    global $i4_settings;
    //Check if the user is logged in
    if (is_user_logged_in()) {
        global $i4_settings;
        $i4_settings = get_option('i4-lms-settings'); //Retrieve the i4 LMS Settings
    }

}

add_action('plugins_loaded', 'i4_get_site_settings');

/**
 * Return a list of courses assigned to the user
 *
 * @param Integer $user_id The ID of the current user.
 * @return Array of courses.
 */
function remove_permalink_notice() {
    remove_action('admin_notices', 'WPCW_plugin_permalinkCheck');
}

add_action('admin_notices', 'remove_permalink_notice', 0);
