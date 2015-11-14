<?php
/**
 * Enqueue scripts and styles for the i4Web LMS plugin
 *
 * @package I4Web_LMS
 * @subpackage Functions/Scripts and Styles
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

/**
 * Enqueue scripts and styles.
 */
function i4_lms_scripts() {
    global $current_i4_user, $i4_settings; //pass in the globals

    //Load the scripts that we'll need to use for the theme
    wp_enqueue_script( 'i4-ajax-front-end', I4_PLUGIN_URL . 'assets/js/ajax-front-end.js', array('jquery', 'wpcw-jquery-form', 'wpcw-countdown'), '0.0.1', true );
    wp_enqueue_script( 'vimeo-froogaloop', I4_PLUGIN_URL . 'assets/js/froogaloop.min.js', array( ), '2.0', false );
    wp_enqueue_script( 'i4-main-js', I4_PLUGIN_URL . 'assets/js/main.js', array('jquery'), '0.0.1', true );
    wp_enqueue_script( 'i4-course-js', I4_PLUGIN_URL . 'assets/js/course.js', array('jquery', 'vimeo-froogaloop', 'i4-ajax-front-end'), '0.0.1', true );
    wp_enqueue_script( 'i4-manage-patients-js', I4_PLUGIN_URL . 'assets/js/manage-patients.js', array('jquery', 'jquery-ui-sortable'), '0.0.1', true );
    wp_enqueue_script( 'password-strength-meter' );
    wp_dequeue_script( 'wpcw-frontend' );

    // Variable declarations
    wp_localize_script(
       'i4-ajax-front-end',     // What we're attaching too
       'wpcw_js_consts_fe',        // Handle for this code
        array(
            'ajaxurl'               => admin_url('admin-ajax.php'), // URL for admin AJAX
            'progress_nonce'        => wp_create_nonce('wpcw-progress-nonce'),     // Nonce security token
            'str_uploading'         => __('Uploading:', 'wp_courseware'),        // Uploading message.
            'str_quiz_all_fields'   => __('Please provide an answer for all of the questions on this page.', 'wp_courseware'),
            'new_patient_nonce'     => wp_create_nonce('add_new_patient_nonce'),

            // Timer units
            'timer_units_hrs'       => __('hrs', 'wp_courseware'),
            'timer_units_mins'      => __('mins', 'wp_courseware'),
            'timer_units_secs'      => __('secs', 'wp_courseware'),

        )
    );

    // Normalize to a value between 0 and 1
    $minimum_viewing_pct = esc_attr( $i4_settings['i4-lms-course-min-view-pct'] ) / 100;
    if ($minimum_viewing_pct < 0 || $minimum_viewing_pct > 1) {
       $minimum_viewing_pct = 1;
    }

    //Retrieve the status of the unit
    $unit_status = I4Web_LMS()->i4_wpcw->i4_get_unit_status();

    //pass in the i-4Web settings to the main.js file
    wp_localize_script(
       'i4-main-js',
       'i4_site_settings',
        array(
            'min_viewing_pct'    => $minimum_viewing_pct,
            'unit_status'        => $unit_status
        )
    );
}

function i4_lms_styles() {
    wp_enqueue_style( 'i4-manage-patients-css', I4_PLUGIN_URL . 'assets/css/manage-patients.css', false, '0.0.1');
}

add_action( 'wp_enqueue_scripts', 'i4_lms_scripts' );
add_action( 'wp_enqueue_scripts', 'i4_lms_styles' );