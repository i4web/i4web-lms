<?php
/**
  * I4Web_LMS Coordinators Class.
  *
  * @package I4Web_LMS
  * @subpackage Classes/Coordinators
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

  /**
    * I4Web_LMS_Coordinators Class
    *
    * This handles the Coordinators admin page, and any functions pertaining to Coordinators
    *
    * @since 0.0.1
    */
  class I4Web_LMS_Coordinators{
  /**
   * Class Construct to get started
   *
   * @since 0.0.1
   */
  public function __construct(){
    global $wpdb, $wpcwdb;

    add_action( 'init', array( $this, 'i4_lms_add_coordinator' ) );

  }

  /**
   * Display the new Coordinators Form for the Coordinators admin page
   *
   * @since 0.0.1
   */
  function new_coordinator_form(){

    //Retrieve Courses information
    $courses = $this->i4_lms_get_courses();

    if( isset( $_GET['add_coordinator'] ) && $_GET['add_coordinator'] == 'true'){
      $this->i4_lms_coordinator_success_msg();
    }
    elseif( isset( $_GET['add_coordinator'] ) && $_GET['add_coordinator'] == 'false'){
      $this->i4_lms_coordinator_error_msg();
    }
    elseif( isset( $_GET['action'] ) && $_GET['action'] = 'delete'){
      $this->i4_lms_coordinator_delete( $_GET['coordinator_id'] );
    }
    ?>
    <form action="" method="POST">
      <h3>Add a New Coordinator</h3>
      <p>Please enter in the Coordinator details below</p>

      <table class="form-table">
        <tbody>
          <tr>
            <th scope="row">Full Name</th>
            <td>
              <input type="text" name="coordinator_name" value="">
            </td>
          </tr>
          <tr>
            <th scope="row">Email</th>
            <td>
              <input type="email" name="coordinator_email" value="">
            </td>
          </tr>
          <tr>
            <th scope="row">Assign a Course</th>
            <td>
              <select name="coordinator_course">
              <?php foreach($courses as $course){
                echo '<option value="'.$course->course_id .'">'.$course->course_title .'</option>';
              } ?>
             </select>
            </td>
          </tr>
          <tr>
            <th scope="row">Upload Image</th>
            <td>
              <div class="section-nav-logo section-upload">
                <input type="text" name="coordinator_image" class="login-logo-url" value="" />
                <input id="nav-logo" class="upload-button button button-primary" type="button" value="Upload Image" /> <br />
                <span class='description'>Required dimensions: 75px X 75px.</span>
            </td>
          </tr>
        </tbody>
      </table>
      <input type="hidden" name="action" value="add_coordinator"/>
      <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
      <input type="hidden" name="new_coordinator_nonce" value="<?php echo wp_create_nonce('new-coordinator-nonce'); ?>"/>

      <?php submit_button(); ?>
    </form>
  <?php
  }

  /**
   * Listens for the add coordinator action and processes the data
   *
   * @since 0.0.1
   */
  function i4_lms_add_coordinator(){
    global $wpdb;

    if(isset($_POST['action']) && $_POST['action'] == 'add_coordinator' && wp_verify_nonce($_POST['new_coordinator_nonce'], 'new-coordinator-nonce')) {

      //Sanitize the Coordinator Name field
      $coordinator_name = sanitize_text_field( $_POST[ 'coordinator_name' ] );

      //Sanitize the Coordinator email field
      $coordinator_email = sanitize_email( $_POST[ 'coordinator_email' ] );

      //Sanitize the intval to only submit integers
      $course_id = intval( $_POST['coordinator_course'] );

      if( !$course_id ){
        $course_id = ''; //if the value submitted is not an integer, we blank that out
      }

      $coordinator_img  = sanitize_text_field( $_POST['coordinator_image'] );


      //Add the Coordinator to our database
      $insert_coordinator_result = $this->i4_lms_insert_coordinator( $coordinator_name, $coordinator_email, $course_id, $coordinator_img );

      if($insert_coordinator_result == false ){
        // redirect on success
        $redirect = add_query_arg( array(
                                    'add_coordinator' => 'false',
                                    'coordinator' => $course_id,
                                    'coordinator_name' => $coordinator_name,
                                    'coordinator_image' => $coordinator_img,
                                    'coordinator_email' => $coordinator_email
                       ),$_POST['redirect']
                       );
      }
      else{
      // redirect on success
      $redirect = add_query_arg( array(
                                  'add_coordinator' => 'true',
                                  'coordinator' => $course_id,
                                  'coordinator_name' => $coordinator_name,
                                  'coordinator_image' => $coordinator_img,
                                  'coordinator_email' => $coordinator_email
                     ),$_POST['redirect']
                     );
      }
      // redirect back to our previous page with the added query variable
		  wp_redirect($redirect); exit;
    }
  }

  /**
   * Display all Coordinators (Used in the Coordinators Admin area)
   *
   * @since 0.0.1
   */
  function i4_lms_display_coordinators(){
    ?>
    <h3>Current Course Coordinators</h3>
  <?php
    $this->i4_lms_get_coordinators();
  }

  /**
   * Display all Coordinators (Used in the Coordinators Admin area)
   *
   * @since 0.0.1
   */
  function i4_lms_get_coordinators(){
    global $wpdb;

    $table_name = $wpdb->prefix . 'i4_lms_coordinators';
    $SQL = "SELECT *
			FROM $table_name
			ORDER BY coordinator_name ASC
			";

  	$coordinators = $wpdb->get_results($SQL);

    echo '<div class="coordinators-wrapper">';
    foreach( $coordinators as $coordinator ){
      echo '<div class="coordinator">';
      echo '<img src="' . $coordinator->coordinator_img .'" alt="'.$coordinator->coordinator_name . '"/>';
      echo '<p>' . $coordinator->coordinator_name . '</p>';
      echo '<p>' . $coordinator->coordinator_email . '</p>';
      echo '<p>Course ID - ' . $coordinator->course_id . '</p>';
      echo '<span><a href="?page=coordinators&action=delete&coordinator_id='.$coordinator->id.'">Delete</a>';
      echo '</div>';
    }
    echo '</div>';
    //var_dump( $coordinators );
  }

  /**
   * Get a an array containing the course id and course title for all courses
   *
   * @since 0.0.1
   */
  function i4_lms_get_courses(){
    global $wpdb, $wpcwdb;

    $SQL = "SELECT course_id, course_title
			FROM $wpcwdb->courses
			ORDER BY course_title ASC
			";

  	$courses = $wpdb->get_results($SQL);

    return $courses;
  }

  /**
   * Inserts a Coordinator into the database
   *
   * @since 0.0.1
   * @param string $name - The Coordinator's name
   * @param string $email - The Coordinator's email address
   * @param string $course_id - The Coordinator's assigned Course ID
   * @param string $img_url - The URL of the Coordinator's image
   * @return Returns the number of rows affected or false if the query did not execute
   */
  function i4_lms_insert_coordinator( $name, $email, $course_id, $img_url ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'i4_lms_coordinators';

    $result = $wpdb->query( $wpdb->prepare(
      	"
      		INSERT INTO $table_name
      		( coordinator_name, coordinator_email, course_id, coordinator_img )
      		VALUES ( %s, %s, %s, %s )
      	",
        $name,
      	$email,
      	$course_id,
        $img_url
      ) );

    return $result;
  }

  /**
   * Deletes a Coordinator from the database
   *
   * @since 0.0.1
   * @param string $name - The Coordinator's ID
   * @return Returns the number of rows affected or false if the query did not execute
   */
  function i4_lms_coordinator_delete( $coordinator_id ){
    global $wpdb;
    $wpdb->show_errors();
    $table_name = $wpdb->prefix . 'i4_lms_coordinators';

    $result = $wpdb->query( $wpdb->prepare(
      	"
        DELETE FROM $table_name WHERE `id` = %d
      	",
        $coordinator_id
      ) );

    return $result;
  }

  /**
   * Displays the Error message when a Coordinator is not successfully added to the DB
   *
   * @since 0.0.1
   */
  function i4_lms_coordinator_success_msg(){
    $class = "updated";
	  $message = "Nice! A new Coordinator was successfully added.";
    echo"<div class=\"$class\"> <p>$message</p></div>";
  }

  /**
   * Displays the Error message when a Coordinator is not successfully added to the DB
   *
   * @since 0.0.1
   */
  function i4_lms_coordinator_error_msg(){
    $class = "error";
	  $message = "An error occurred when adding a new Coordinator. Please check your information and try again";
    echo"<div class=\"$class\"> <p>$message</p></div>";
  }

  /**
   * Displays the Error message when a Coordinator is not successfully added to the DB
   *
   * @since 0.0.1
   */
  function i4_lms_get_coordinator( $course_id ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'i4_lms_coordinators';
	   $wpdb->show_errors();

  	$SQL = $wpdb->prepare("
  		SELECT *
  		FROM $table_name
  		WHERE course_id = %d
  	", $course_id);

	return $wpdb->get_row($SQL);

  }

  }
