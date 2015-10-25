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

    add_action( 'admin_menu', array( $this, 'i4_admin_menu' ) );
    add_action( 'admin_init', array( $this,'i4_settings_init' ));

  }

  /**
   * Adds the menu
   *
   * @since 0.0.1
   */
  function i4_admin_menu(){

  add_menu_page( 'i-4Web LMS', 'i-4Web LMS', 'manage_options', 'i4web-lms-settings', array( $this, 'i4_admin_page' ), 'dashicons-admin-generic', 76);

  add_submenu_page( 'i4web-lms-settings', 'i-4Web LMS Settings', 'Settings', 'manage_options', 'i4web-lms-settings');

  add_submenu_page( 'i4web-lms-settings', 'Coordinators', 'Coordinators', 'manage_options', 'coordinators', array($this, 'i4_coordinators_page' ) );

  }

 /**
  * Sets up our Site Settings Page
  *
  * @since 0.0.1
  */
  function i4_admin_page(){
    //Deny access unless the user is an Administrator ( activate_plugins capability )
    if ( ! current_user_can( 'manage_options' ) ){
      wp_die( __( 'Sorry! You do not have sufficient permissions to access this page' ) );
    }

    echo '<div class="wrap">';
    echo '<h2><span class="dashicons dashicons-admin-generic"></span> i-4Web LMS - General Settings</h2>';

    //Display the errors/success messages when settings are saved.
    settings_errors(); ?>

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
     register_setting( 'i4-lms-settings-group', 'i4-lms-settings', array( $this, 'i4_validate_settings' ) );

     //add a Section
     add_settings_section( 'i4-lms-branding-section', 'Branding', array( $this, 'branding_section_callback'), 'i4web-lms-settings');
     add_settings_field( 'i4-lms-primary-color', 'Primary Branding Color', array($this, 'primary_branding_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-secondary-color', 'Secondary Branding Color', array($this, 'secondary_branding_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-login-logo', 'Upload the Login Page Logo', array($this, 'login_logo_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );
     add_settings_field( 'i4-lms-nav-logo', 'Upload the Navigation Menu Logo', array($this, 'nav_logo_callback'), 'i4web-lms-settings', 'i4-lms-branding-section' );

     add_settings_section( 'i4-lms-course-settings', 'Course Settings', array( $this, 'course_section_callback'), 'i4web-lms-settings');
     add_settings_field( 'i4-lms-course-min-view-pct', 'Minimum Video Viewing %', array($this, 'minimum_viewing_pct_callback'), 'i4web-lms-settings', 'i4-lms-course-settings' );


     add_settings_section( 'i4-lms-vimeo-api-settings', 'Vimeo Settings', array( $this, 'vimeo_section_callback'), 'i4web-lms-settings');
     add_settings_field( 'i4-lms-vimeo-access-token', 'Access Token', array($this, 'access_token_callback'), 'i4web-lms-settings', 'i4-lms-vimeo-api-settings' );
     add_settings_field( 'i4-lms-vimeo-access-token-url', 'Access Token URL', array($this, 'access_token_url_callback'), 'i4web-lms-settings', 'i4-lms-vimeo-api-settings' );
     add_settings_field( 'i4-lms-vimeo-client-identifier', 'Client Identifier', array($this, 'client_identifier_callback'), 'i4web-lms-settings', 'i4-lms-vimeo-api-settings' );
     add_settings_field( 'i4-lms-vimeo-client-secrets', 'Client Secrets', array($this, 'client_secrets_callback'), 'i4web-lms-settings', 'i4-lms-vimeo-api-settings' );
     add_settings_field( 'i4-lms-vimeo-authorize-url', 'Authorize URL', array($this, 'authorize_url_callback'), 'i4web-lms-settings', 'i4-lms-vimeo-api-settings' );


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
    * Call back to our Navigation Menu logo URL
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
    * Call back to our Course Settings Section verbiage
    *
    * @since 0.0.1
    */
   public function course_settings_section_callback(){
     echo 'Customize your course settings below';
   }

   /**
    * Call back to our minimum viewing pct setting
    *
    * @since 0.0.1
    */
    public function minimum_viewing_pct_callback(){
      $settings = (array) get_option( 'i4-lms-settings' );
      $min_viewing_pct = esc_attr( $settings[ 'i4-lms-course-min-view-pct'] );

      echo "<input type='text' name='i4-lms-settings[i4-lms-course-min-view-pct]' value='$min_viewing_pct' /> <br />";
      echo "<span class='description'>Set the minimum % of each video that the patient must watch before they can manually advance. <br> Setting this to 0 will disable the forced viewing of videos</span>";


    }


   /**
    * Call back to our Access Token Field
    *
    * @since 0.0.1
    */
    public function access_token_callback(){
      $settings = (array) get_option( 'i4-lms-settings');
      $vimeo_access_token = esc_attr( $settings['i4-lms-vimeo-access-token'] );

      echo "<input type='text' class='regular-text ltr' name='i4-lms-settings[i4-lms-vimeo-access-token]' value='$vimeo_access_token' />";
    }

    /**
     * Call back to our Access Token URL Field
     *
     * @since 0.0.1
     */
     public function access_token_url_callback(){
       $settings = (array) get_option( 'i4-lms-settings');
       $vimeo_access_token_url = esc_attr( $settings['i4-lms-vimeo-access-token-url'] );

       echo "<input type='text' class='regular-text ltr' name='i4-lms-settings[i4-lms-vimeo-access-token-url]' value='$vimeo_access_token_url' />";
     }

    /**
     * Call back to our Client Identifier Field
     *
     * @since 0.0.1
     */
     public function client_identifier_callback(){
       $settings = (array) get_option( 'i4-lms-settings');
       $vimeo_client_identifier = esc_attr( $settings['i4-lms-vimeo-client-identifier'] );

       echo "<input type='text' class='regular-text ltr' name='i4-lms-settings[i4-lms-vimeo-client-identifier]' value='$vimeo_client_identifier' />";
     }

     /**
      * Call back to our Client Secrets Field
      *
      * @since 0.0.1
      */
      public function client_secrets_callback(){
        $settings = (array) get_option( 'i4-lms-settings');
        $vimeo_client_secrets = esc_attr( $settings['i4-lms-vimeo-client-secrets'] );

        echo "<input type='text' class='regular-text ltr' name='i4-lms-settings[i4-lms-vimeo-client-secrets]' value='$vimeo_client_secrets' />";
      }

      /**
       * Call back to our Authorize URL field
       *
       * @since 0.0.1
       */
       public function authorize_url_callback(){
         $settings = (array) get_option( 'i4-lms-settings');
         $vimeo_authorize_url = esc_attr( $settings['i4-lms-vimeo-authorize-url'] );

         echo "<input type='text' class='regular-text ltr' name='i4-lms-settings[i4-lms-vimeo-authorize-url]' value='$vimeo_authorize_url' />";
       }



  /**
   * Sets up our Site Settings Page
   *
   * @since 0.0.1
   */
   function i4_coordinators_page(){
     //Deny access unless the user is an Administrator ( activate_plugins capability )
     if ( ! current_user_can( 'manage_options' ) ){
       wp_die( __( 'Sorry! You do not have sufficient permissions to access this page' ) );
     }

     echo '<div class="wrap">';
     echo '<h2>Manage Coordinators</h2>';

     //add the New Coordinator Form
     I4Web_LMS()->i4_coordinators->new_coordinator_form();
     ?>


     <hr>

     <?php
     I4Web_LMS()->i4_coordinators->i4_lms_display_coordinators(); //Display all coordinators
     echo '</div> <!-- end .wrap -->';
   }


  /**
   * Validates the settings input. Reference https://kovshenin.com/2012/the-wordpress-settings-api/
   *
   * @since 0.0.1
   */
   function i4_validate_settings( $input ){

     $output = get_option( 'i4-lms-settings'); //store the options

     $min_viewing_pct_bool = is_numeric( $input['i4-lms-course-min-view-pct']); //Check if the input is a numeric value. Returns false if it is is not
     $min_viewing_pct = $input['i4-lms-course-min-view-pct']; //store the input

     if( $min_viewing_pct_bool && ($min_viewing_pct >= 0 && $min_viewing_pct <= 100) ){ //only set the input for saving if it is numeric and is within a 0 to 100 range
       $output[ 'i4-lms-course-min-view-pct' ] = $min_viewing_pct;
     }
     else{ //if the input is not numeric and is not within the 0 to 100 range. Save the setting as 0 and output an error to the user
       $output[ 'i4-lms-course-min-view-pct' ] = 0;
       add_settings_error( 'i4-lms-settings[i4-lms-course-min-view-pct]', 'invalid-min-viewing-pct', 'You have entered an invalid value for the minimum video viewing percentage and we were unable to update the setting. Please make sure you are setting a value between 0 and 100.' );

     }

     return $output; //return the settings to be saved
   }

}
