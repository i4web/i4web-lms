<?php
/**
  * I4Web_LMS Admin Menu Class. Helps restrict the website to just registered users
  *
  * @package I4Web_LMS
  * @subpackage Classes/Force Login
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

  /**
    * I4Web_LMS_Admin_Menu Class
    *
    * This handles creating the plugin menu for us.
    *
    * @since 0.0.1
    */
  class I4Web_LMS_Admin_Menu{
  /**
   * Class Construct to get started
   *
   * @since 0.0.1
   */
  public function __construct(){
    global $wpdb;

    //Set the table name and other table variables for our settings table
    $this->i4_settings_table_name  = $wpdb->base_prefix . 'i4_lms_settings';
    $this->i4_settings_primary_key = 'id';
    $this->i4_settings_version     = '1.0';

    add_action( 'admin_enqueue_scripts', array( $this, 'mediaUploader' ) );
    add_action( 'admin_menu', array( $this, 'i4_admin_menu' ) );
    add_action( 'admin_init', array( $this,'i4_settings_init' ));


  }

  /**
   * Adds the menu for CandidateSpace Site Managers
   *
   * @since 0.0.1
   */
  function i4_admin_menu(){

  add_menu_page( 'i-4Web LMS', 'i-4Web LMS', 'activate_plugins', 'i4web-lms-settings', array( $this, 'i4_admin_page' ), 'dashicons-admin-generic', 76);
  }

 /**
  * Sets up our Site Settings Page
  *
  * @since 0.0.1
  */
  function i4_admin_page(){
    //Deny access unless the user is an Administrator ( activate_plugins capability )
    if ( ! current_user_can( 'activate_plugins' ) ){
      wp_die( __( 'Sorry! You do not have sufficient permissions to access this page' ) );
    }

    echo '<div class="wrap">';
    echo '<h2><span class="dashicons dashicons-admin-generic"></span> i-4Web LMS - General Settings</h2>';

    ?>
    <form action="options.php" method="POST">
      <?php settings_fields( 'i4-lms-settings-group' ); ?>
      <?php do_settings_sections( 'i4web-lms-settings' ); ?>
      <?php submit_button(); ?>
    </form>
    <?php

    echo '</div> <!-- end .wrap -->';
  }

  /**
   * Sets up our i-4Web LMS Settings Sections and fields
   *
   * @since 0.0.1
   */
   function i4_settings_init(){
     //Register our settings
     register_setting( 'i4-lms-settings-group', 'i4-lms-settings' );

     //add a Section
     add_settings_section( 'i4-lms-branding-section', 'Branding', array( $this, 'branding_section_callback'), 'i4web-lms-settings');
     add_settings_field( 'i4-lms-primary-color', 'Primary Branding Color', array($this, 'primary_branding_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-secondary-color', 'Secondary Branding Color', array($this, 'secondary_branding_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-login-logo', 'Upload the Login Page Logo', array($this, 'login_logo_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-nav-logo', 'Upload the Navigation Menu Logo', array($this, 'nav_logo_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );

   }

  /**
   * Call back to our Branding Section verbiage
   *
   * @since 0.0.1
   */
  public function branding_section_callback(){
    echo 'Please enter in all Branding Settings to customize the login page';
  }

  /**
   * Call back to our Primary Branding Hex Code
   *
   * @since 0.0.1
   */
   public function primary_branding_callback(){
     $settings = (array) get_option( 'i4-lms-settings');
     $primary_brand = esc_attr( $settings['i4-lms-primary-color'] );

     echo "<input type='text' name='i4-lms-settings[i4-lms-primary-color]' value='$primary_brand' />";
   }

   /**
    * Call back to our Secondary Branding Hex Code
    *
    * @since 0.0.1
    */
    public function secondary_branding_callback(){
      $settings = (array) get_option( 'i4-lms-settings');
      $secondary_brand = esc_attr( $settings['i4-lms-secondary-color'] );

      echo "<input type='text' name='i4-lms-settings[i4-lms-secondary-color]' value='$secondary_brand' />";
    }

  /**
   * Call back to our Login page Logo URL
   *
   * @since 0.0.1
   */
   public function login_logo_callback(){
     $settings = (array) get_option( 'i4-lms-settings');
     $login_logo = esc_attr( $settings['i4-lms-login-logo'] );

     echo '<div class="section-login-logo section-upload">';
     echo "<input type='text' name='i4-lms-settings[i4-lms-login-logo]' class='login-logo-url' value='$login_logo' />";
     echo '<input id="login-logo" class="upload-button button button-primary" type="button" value="Upload Image" /> <br />';
     echo "<span class='description'>Don't make it too tall. Max 250px x 50px resolution.</span>";

     echo '</div> <!-- end section-login-logo -->';
   }

   /**
    * Call back to our Login page Logo URL
    *
    * @since 0.0.1
    */
   public function nav_logo_callback(){
     $settings = (array) get_option( 'i4-lms-settings');
     $nav_logo = esc_attr( $settings['i4-lms-nav-logo'] );

     echo '<div class="section-nav-logo section-upload">';
     echo "<input type='text' name='i4-lms-settings[i4-lms-nav-logo]' class='login-logo-url' value='$nav_logo' />";
     echo '<input id="nav-logo" class="upload-button button button-primary" type="button" value="Upload Image" /> <br />';
     echo "<span class='description'>Don't make it too wide. Max 150px width.</span>";
   }

  /**
   * Setup the custom media uploader script only on the the i-4Web LMS Settings Page to avoid conflicts with any other scripts
   *
   * @since 0.0.1
   */
  public function mediaUploader($hook){

    if( 'toplevel_page_i4web-lms-settings' != $hook ) // Retrieve the hook by echo'ing out the $hook variable
  				return;

  	wp_enqueue_media();
  	wp_register_script('i4_lms_uploader', I4_PLUGIN_URL . '/assets/js/custom-media-uploader.js', array('jquery'));
  	wp_enqueue_script('i4_lms_uploader');
  }

}
