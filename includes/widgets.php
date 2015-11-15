<?php
/**
 * Register Widgets
 *
 * Registers the Widgets for the i-4Web LMS Plugin.
 *
 * @since 0.0.1
 * @return void
 */
function i4_lms_register_widgets() {
    register_widget('I4Web_LMS_Announcements_Widget');

}

add_action('widgets_init', 'i4_lms_register_widgets');
