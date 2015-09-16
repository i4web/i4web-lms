<?php
/**
  * I4Web_LMS Announcements Widget. Helps display announcements and informational
  *
  * @package I4Web_LMS
  * @subpackage Classes/Announcements Widget
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

/**
  * Announcements Widget
  *
  * Announcements Widget Class
  *
  * @since 0.0.1
  * @return void
  */
  class I4Web_LMS_Announcements_Widget extends WP_Widget {

    /** Constructor */
    function __construct() {
      parent::__construct( 'I4Web_LMS_Announcements_Widget', __( 'Announcements', 'i4web' ), array( 'description' => __( 'Display the Website Announcements', 'i4web' ) ) );
    }

    /** @see WP_Widget::widget */
    function widget( $args, $instance ) {

    }
  }
