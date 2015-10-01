<?php
/**
  * I4Web_LMS Force Login Class. Helps restrict the website to just registered users
  *
  * @package I4Web_LMS
  * @subpackage Classes/Force Login
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

  /**
   * I4Web_LMS_Roles Classes
   * This handles creating roles for the i4Web LMS Plugin
   *
   * @since 0.0.1
   */
  class I4Web_LMS_Force_Login{

    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
     public function __construct(){

       //Insert the Login Check into the wp_head of the website
       add_action('wp_head', array($this, 'i4_login_redirect') );

       //Restrict the Admin area
       add_action( 'admin_init', array($this, 'i4_restrict_admin' ) );

     }

    /**
     * Forces Login by redirecting to the login page by using the pluggable core function that redirects to
     * the page trying to be accessed after the user has logged in
     *
     */
     public function i4_login_redirect(){

       if( !is_user_logged_in() ){ //Redirect the user if they are not authenticated
		    auth_redirect();
	     }
     }

     /**
      * Once logged in, if the user is attempting to access the WP admin dashboard and is not an Editor or above
      * they are redirected to the front page of the site
      *
      */
      public function i4_restrict_admin(){

        if ( ! current_user_can( 'edit_pages' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX )) {
            wp_safe_redirect( site_url() );
            exit;
      	}
      }


  }
