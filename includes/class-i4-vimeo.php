<?php
/**
  * i-4Web Vimeo API Class
  *
  * @package I4Web_LMS
  * @subpackage Classes/Vimeo
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

  /**
   * I4Web_LMS_Profile_Form Class
   *
   * @since 0.0.1
   */
   class I4Web_LMS_Vimeo{

     /**
      * Class Construct to get started
      *
      * @since 0.0.1
      */
      public function __construct(){
        global $current_i4_user; //global user object


      } //end construct

      /**
       * Retrieve the vimeo settings
       *
       * @since 0.0.1
       */
      function get_vimeo_settings(){

        //Retrieve the Vimeo Settings from the options table
        $i4_settings = get_option( 'i4-lms-settings' );
        $access_token = esc_attr( $i4_settings['i4-lms-vimeo-access-token'] );
        $client_id = esc_attr( $i4_settings['i4-lms-vimeo-client-identifier'] );
        $client_secret = esc_attr( $i4_settings['i4-lms-vimeo-client-secrets'] );

        $vimeo_settings = array(
            "access_token"    => $access_token,
            "client_id"       => $client_id,
            "client_secret"   => $client_secret
        );

        //return array of vimeo settings
        return $vimeo_settings;
      }
      /**
       * Setup the Account Settings form shortcode
       *
       * @since 0.0.1
       */
      function demo_get_vimeo_response_body(){

        $vimeo_settings = $this->get_vimeo_settings();


        //Vimeo API Call
        //Retrieve Vimeo API Settings
        $i4_settings = get_option( 'i4-lms-settings' ); //Retrieve the i4 LMS Settings
        $access_token = esc_attr( $i4_settings['i4-lms-vimeo-access-token'] );
        $client_id = esc_attr( $i4_settings['i4-lms-vimeo-client-identifier'] );
        $client_secret = esc_attr( $i4_settings['i4-lms-vimeo-client-secrets'] );

        $lib = new \Vimeo\Vimeo($client_id, $client_secret);
        $scope = array('public', 'private' );

        $token = $lib->clientCredentials($scope);

        $lib->setToken($access_token);
        $response = $lib->request('/users/44252338/videos', array('per_page' => 2), 'GET');

        return var_dump($response['body']); //Dump the respondse body
      }

   }
