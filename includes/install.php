<?php
/**
 * Installs the i4Web LMS plugin
 *
 * @package I4Web_LMS
 * @subpackage Functions/Install
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

 // Exit if accessed directly
 if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Installation functions
 *
 * This runs when the plugin is activated
 * @since 0.0.1
 */
 function i4_installation(){

   //Create the I4Web_LMS Roles Object
   $i4_roles = new I4Web_LMS_Roles;

   $i4_roles->i4_add_roles();

   $i4_roles->i4_remove_roles();

   $i4_roles->i4_add_capabilities();

   I4Web_LMS()->i4_db->create_i4_settings_table();



 }

 /**
  * Uninstall functions
  *
  * This runs when the plugin is activated
  * @since 0.0.1
  */
 function i4_uninstall(){

  remove_role( 'student' );

}

 register_activation_hook( I4_PLUGIN_FILE, 'i4_installation' );

 register_deactivation_hook( I4_PLUGIN_FILE, 'i4_uninstall'); //For debugging purposes only. We don't ever want to delete roles in production
