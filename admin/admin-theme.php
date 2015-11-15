<?php
/**
 * Customizes the Admin area of WordPress
 *
 * @package I4Web_LMS
 * @subpackage Functions/Admin Theme
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

/**
 * Changes the footer text within the Wordpress Dashboard
 *
 * @see admin_footer_text filter in wp-admin/admin-footer.php of WordPress
 * @since 0.0.1
 */
add_filter('admin_footer_text', 'i4_admin_footer_text', 10, 2);

function i4_admin_footer_text($text) {
    $text = 'Website Designed and Developed by <a href="https://www.i-4web.com" target="_blank">i-4Web</a>. For Support or to report any issues - <a href="https://www.i-4web.com/support" target="_blank">Click Here</a>';

    return $text;
}

/**
 * Removes the Nodes from the WP Toolbar
 *
 * @since 0.0.1
 */
function remove_wp_toolbar_nodes() {
    global $wp_admin_bar;

    $wp_admin_bar->remove_node('wp-logo'); //Remove the WP Logo
    $wp_admin_bar->remove_node('comments'); //Remove the comments node

}

add_action('wp_before_admin_bar_render', 'remove_wp_toolbar_nodes', 999);

/**
 * Removes the Toolbar for the Front-End of the site for the Student Role
 *
 * @since 0.0.1
 */
function remove_wp_toolbar() {
    if (current_user_can('student')) {
        show_admin_bar(false);
    }
}

add_action('after_setup_theme', 'remove_wp_toolbar');


/**
 * Removes the WordPress version from the WordPress Dashboard
 *
 * WordPress prints the current version and update information,
 * using core_update_footer() at priority 10.
 *
 * @see core_update_footer()
 * @since 0.0.1
 */
function i4_remove_version() {
    remove_filter('update_footer', 'core_update_footer');
}

add_action('in_admin_footer', 'i4_remove_version');

/**
 * Replaces the "Howdy" text in the admin tool bar
 *
 * @since 0.0.1
 */
function i4_replace_wp_howdy() {
    global $wp_admin_bar;

    $i4_account = $wp_admin_bar->get_node('my-account');
    $new_title = str_replace('Howdy,', 'Welcome,', $i4_account->title);

    $wp_admin_bar->add_node(array(
            'id' => 'my-account',
            'title' => $new_title
        )
    );
}

add_filter('admin_bar_menu', 'i4_replace_wp_howdy', 25);

/**
 * Removes the Tools menu from the Site Manager role.
 *
 * @since 1.0.0
 */
add_action('admin_menu', 'i4_remove_color_scheme_picker');

function i4_remove_color_scheme_picker() {

    if (!current_user_can('install_plugins')) {  //remove the theme picker for everyone except the Administrator
        remove_action("admin_color_scheme_picker", "admin_color_scheme_picker");
    }
}
