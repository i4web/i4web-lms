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
  }

  /**
   * Display the new Coordinators Form for the Coordinators admin page
   *
   * @since 0.0.1
   */
  function new_coordinator_form(){

    $courses = $this->i4_lms_get_courses();

    var_dump( $courses );

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
            <th scope="row">Assigned Course</th>
            <td>
              <select>
               <option value="courseID1">Course 1</option>
               <option value="courseID2">Course 2</option>
               <option value="courseID3">Course 3</option>
               <option value="courseID3">Course 4</option>
             </select>
            </td>
          </tr>
          <tr>
            <th scope="row">Upload Image</th>
            <td>
              <input type="text" name="coordinator_name" value="">
            </td>
          </tr>
        </tbody>
      </table>
      <?php submit_button(); ?>
    </form>
  <?php
  }

  /**
   * Display all Coordinators (Used in the Coordinators Admin area)
   *
   * @since 0.0.1
   */
  function display_coordinators(){
    ?>
    <h3>Current Course Coordinators</h3>
  <?php
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

  }
