<?php
/**
 * Enqueue Admin scripts and styles for the i4Web LMS plugin
 *
 * @package I4Web_LMS
 * @subpackage Admin/Admin Scripts and Styles
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

/**
 * Enqueue scripts and styles.
 */
function i4_lms_admin_scripts($hook) {

    if ('toplevel_page_i4web-lms-settings' != $hook && 'i-4web-lms_page_coordinators' != $hook & 'i-4web-lms_page_course-docs' != $hook) // Retrieve the hook by echo'ing out the $hook variable. Only enqueue on the settings and coordinator pages
    {
        return;
    }

    //Enqueue the Plugin Admin Stylesheet
    wp_register_style('i4_lms_admin_css', I4_PLUGIN_URL . '/admin/css/i4-lms-admin-style.css', false, '0.0.1');
    wp_enqueue_style('i4_lms_admin_css');

    // Enqueu and Register the Media Uploader Script
    wp_enqueue_media();
    wp_register_script('i4_lms_uploader', I4_PLUGIN_URL . '/assets/js/custom-media-uploader.js', array('jquery'));
    wp_enqueue_script('i4_lms_uploader');

}

add_action('admin_enqueue_scripts', 'i4_lms_admin_scripts');
