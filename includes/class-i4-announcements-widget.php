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

      $args['id']        = ( isset( $args['id'] ) ) ? $args['id'] : 'I4Web_LMS_Announcements_Widget';
      $instance['title'] = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
      $title = $instance['title'];

      echo $args['before_widget'];

      if( $title ){
        echo $args['before_title'] . $title . $args['after_title'];
      }

      echo '<ul class="announcements-list no-bullet">
              <li><span class="announcement-feat-img"><img src="https://www.floridahospital.com/sites/default/files/styles/what-is-happening/public/little_magic_baby_onesie_sm.jpg?itok=T9yBG6L7"></span><a href="#">A Culture of Caffeine</a>
              <p class="announcement-posted-date">November 1, 2015</p>
              </li>
              <li><span class="announcement-feat-img"><img src="https://www.floridahospital.com/sites/default/files/styles/what-is-happening/public/body-weight-workout.jpg?itok=kEvxu4Pt"></span><a href="#">10 Minute Workout to Jumpstart Your Day</a>
              <p class="announcement-posted-date">November 1, 2015</p>
              </li>
            </ul>';
      echo '<a href="#" class="button expand">Read All</a>';

      echo $args['after_widget'];

    }

    /** @see WP_Widget::update */
  	function update( $new_instance, $old_instance ) {
  		$instance = $old_instance;

  		$instance['title']            = strip_tags( $new_instance['title'] );
  		$instance['hide_on_checkout'] = isset( $new_instance['hide_on_checkout'] );

  		return $instance;
  	}

    /** @see WP_Widget::form */
    function form ( $instance ){

      $defaults = array(
  			'title'            => 'Announcements'
  		);

      $instance = wp_parse_args( (array) $instance, $defaults ); ?>
      <p>
  			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'i4web' ); ?></label>
  			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $instance['title']; ?>"/>
  		</p>

      <?php
    }
  }
