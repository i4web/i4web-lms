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
   class I4Web_LMS_Vimeo {

    private $vimeo_lib;

     /**
      * Class Construct to get started
      *
      * @since 0.0.1
      */
      public function __construct() {
        global $current_i4_user; //global user object
        $this->init();

      } //end construct

      /**
       * Retrieve the vimeo settings
       *
       * @since 0.0.1
       */
      function get_vimeo_settings() {
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

      private function init() {
        //Vimeo API Call
        //Retrieve Vimeo API Settings
        $vimeo_settings = $this->get_vimeo_settings();

        $this->vimeo_lib = new \Vimeo\Vimeo($vimeo_settings['client_id'], $vimeo_settings['client_secret']);
        $scope = array('public', 'private' );

        $token = $this->vimeo_lib->clientCredentials($scope);

        $this->vimeo_lib->setToken($vimeo_settings['access_token']);
      }

      function get_duration($video_id) {
        $transient_name = $video_id . '-duration';
        $duration = get_transient($transient_name);
        if ($duration === false) {
          $response = $this->vimeo_lib->request('/me/videos/'.$video_id);
          $duration = $this->format_duration($response['body']['duration']);
          set_transient($transient_name, $duration, DAY_IN_SECONDS);
        }
        return $duration;
      }

      private function format_duration($duration) {
        $duration_minutes = (int) ($duration / 60);
        $duration_hours = (int) ($duration_minutes / 60);

        $hours = $this->convert_time_to_two_digits($duration_hours);
        $minutes = $this->convert_time_to_two_digits($duration_minutes - ($duration_hours * 60));
        $seconds = $this->convert_time_to_two_digits($duration % 60);

        $formatted_duration = $minutes . ":" . $seconds;
        if ($hours != "00") {
          $formatted_duration = $hours . ":" . $formatted_duration;
        }
        return $formatted_duration;
      }

      private function convert_time_to_two_digits($time) {
        $result = strval(intval($time));
        if ($time < 10) {
          $result = "0" . $time;
        }
        return $result;
      }
   }
