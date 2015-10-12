<?php
/**
  * i-4Web LMS User Functions
  *
  * @package I4Web_LMS
  * @subpackage Classes/Profile Form
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If a user is logged in, get the user object and add a global $current_i4_user for use throughout the site and plugin
 *
 * @since 0.0.1
 */
 function i4_get_user(){
   global $current_i4_user;
   //Check if the user is logged in
   if( is_user_logged_in() ){
     global $current_i4_user;
     $current_i4_user = wp_get_current_user(); //load the current iQuue user for use throughout the site
   }

  }

  add_action( 'plugins_loaded', 'i4_get_user');
