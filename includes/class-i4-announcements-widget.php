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

    // Start the query for the latest 3 posts
    $args = array(
    	'post_type' => array( 'post' ),
    	'posts_per_page' => 3
    );
    $the_query = new WP_Query( $args );

     if ( $the_query->have_posts() ) :
       while ( $the_query->have_posts() ) : $the_query->the_post();
      echo '<ul class="announcements-list no-bullet">';
      if ( has_post_thumbnail() ){
        echo '<li><span class="announcement-feat-img">'. get_the_post_thumbnail( $post_id, array( 100, 100)) .'</span><a href="'. get_the_permalink(). '">'. get_the_title() .'</a>';

      }
      else{
        echo '<li><a href="'. get_the_permalink(). '">'. get_the_title() .'</a>';
      }
      echo '<p>'. i4_lms_posted_on() .'</p>
              </li>
            </ul>';
      endwhile;
        $posts_page = get_option( 'page_for_posts' ); //Get the ID of the page set as the Posts Page
        echo '<a href="'. get_permalink( $posts_page ).'" class="button expand">Read All</a>';
        wp_reset_postdata();
    else :
      echo '<p>Sorry, there are no announcements available at this time</p>';
    endif;

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
