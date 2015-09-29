<?php
/**
 * i-4Web WPCW Shortcodes
 *
 * This class handles creating shortcodes and functions that utilize WPCW data
 *
 * @package     I4Web_LMS
 * @subpackage  Classes/WPCW
 * @since       1.0.0
 */

 // Exit if accessed directly
 if ( ! defined( 'ABSPATH' ) ) exit;

 /**
  * I4_LMS_EMAILS Class
  *
  * @since 1.0
  */
 class I4_LMS_WPCW {

   /**
 	 * Class Construct to get started
 	 *
 	 * @since 1.0
 	 */
 	 public function __construct() {
     global $wpdb, $wpdb;

     add_shortcode( 'i4_assigned_courses', array( $this, 'i4_assigned_courses_shortcode' ) );


   }

  /**
   * Setup the Assigned Courses Shortcode (To be used on the main course dashboard page)
   *
   * @since 1.0.0
   */
  function i4_assigned_courses_shortcode(){

     if ( is_user_logged_in() ) { //Simple check to see if the user is logged in or not
       ob_start();
       $this->i4_assigned_courses();
       return ob_get_clean();
     }
   }

  /**
   * Displays the Assigned courses for the i4_assigned_courses shortcode
   *
   * @since 1.0.0
   */
  function i4_assigned_courses(){
    global $wpcwdb, $wpdb;
    $wpdb->show_errors();

    $user_id = get_current_user_id();

    $SQL = "SELECT *
			FROM $wpcwdb->courses
			ORDER BY course_title ASC
			";

  	$courseCount = 0;
  	$courses = $wpdb->get_results($SQL);


    if ($courses){
      foreach ($courses as $course){

        $up = new UserProgress($course->course_id, $user_id);


        // Break out if the user doesn't have access to this course
			  if (!$this->I4_LMS_User_Can_Access($course->course_id, $user_id)) {
				continue;
			  }

        //Retrieve the modules for the users course
        $modules = WPCW_courses_getModuleDetailsList($course->course_id);

        //Determine the % of course completion
        $i4_percent_completed = $this->i4_lms_percent_course_completed( $course->course_id, $modules, $user_id );

        //Get a list of the completed units. We'll search the completed units array
        $i4_completed_units = $this->i4_get_completed_units($course->course_id, $user_id );

        printf(__('<div class="my-course-wrapper">'));
        printf(__('<div class="my-course-meta">'));
        printf(__('<div class="my-course-title" >'));
        printf(__('<h3 class="wpcw_tbl_progress_course">Course - %s</h3>'), $course->course_title);
        printf(__('</div> <!-- end my-course-title -->'));
        printf(__('<div class="my-course-pct-complete">%s%% Complete</div>'), $i4_percent_completed);
        printf(__('</div><!-- end course-meta -->'));

        printf('<div class="my-course-outline-wrapper">');

        //Let's get the modules for the course
        if( $modules){
          foreach ($modules as $module){

            //get the units for the module
            $units = WPCW_units_getListOfUnits($module->module_id);

            //display the module title
            printf('<div class="my-course-module-title">');
            printf(__( '<p>%s</p>'), $module->module_title);
            printf('</div> <!-- end my-course-module-title -->');

            //create a table for each of the units in the module
            printf('<table class="my-course-units-table">');

            //iterate through the units for the module
            if( $units ){
              foreach ( $units as $unit ){
                printf('<tr>');
                printf('<td class="large-12 columns">');
                printf(('<a href="%s" title="%s"><i class="fa fa-play-circle-o"></i> %s</a>'), get_the_permalink($unit->ID), $unit->post_title, $unit->post_title);
              //  printf('</td>');
                printf('<span class="right">');

                //If the unit is in the completed units array, display the completed checkmark.
                if (in_array ( $unit->ID, $i4_completed_units )){
                  printf('<i class="fa fa-check font-success completed-icon"></i>');
                }
                else{
                  printf(__('<a class="button round tiny blue" title="Begin %s" href="%s">Begin</a>'), $unit->post_title, get_the_permalink($unit->ID) ) ;
                }
                printf('</span></td>');
                printf('</tr>');
              }
            }

            printf('</table> <!-- my-course-units-table -->');

          }
        }

      /*  printf('<table class="widefat wpcw_tbl wpcw_tbl_progress">');

        printf('<thead>');
        printf('<th>%s</th>', 															__('Unit', 'wp_courseware'));
        printf('<th class="wpcw_center">%s</th>', 								__('Completed', 'wp_courseware'));
        printf('<th class="wpcw_center wpcw_tbl_progress_quiz_name">%s</th>', 	__('Quiz Name', 'wp_courseware'));
        printf('<th class="wpcw_center">%s</th>', 								__('Quiz Status', 'wp_courseware'));
        printf('<th class="wpcw_center">%s</th>', 								__('Actions', 'wp_courseware'));
        printf('</thead><tbody>');

      // #### 2 - Fetch all associated modules

   if ($modules)
      {
        foreach ($modules as $module)
        {
          // #### 3 - Render Modules as a heading.
          printf('<tr class="wpcw_tbl_progress_module">');
            printf('<td colspan="3">%s %d - %s</td>',
              __('Module', 'wp_courseware'),
              $module->module_number,
              $module->module_title
            );

            // Blanks for Quiz Name and Actions.
            printf('<td>&nbsp;</td>');
            printf('<td>&nbsp;</td>');
          printf('</tr>');

          // #### 4. - Render the units for this module
          $units = WPCW_units_getListOfUnits($module->module_id);
          if ($units)
          {
            foreach ($units as $unit)
            {
              $showDetailLink = false;

              printf('<tr class="wpcw_tbl_progress_unit">');

              printf('<td class="wpcw_tbl_progress_unit_name">%s %d - %s</td>',
                __('Unit', 'wp_courseware'),
                $unit->unit_meta->unit_number,
                $unit->post_title
              );

              // Has the unit been completed yet?
              printf('<td class="wpcw_tbl_progress_completed">%s</td>', $up->isUnitCompleted($unit->ID) ? __('Completed', 'wp_courseware') : '');

              // See if there's a quiz for this unit?
              $quizDetails = WPCW_quizzes_getAssociatedQuizForUnit($unit->ID, false, $userID);

              // Render the quiz details.
              if ($quizDetails)
              {
                // Title of quiz
                printf('<td class="wpcw_tbl_progress_quiz_name">%s</td>', $quizDetails->quiz_title);

                // No correct answers, so mark as complete.
                if ('survey' == $quizDetails->quiz_type)
                {
                  $quizResults = WPCW_quizzes_getUserResultsForQuiz($userID, $unit->ID, $quizDetails->quiz_id);

                  if ($quizResults)
                  {
                    printf('<td class="wpcw_tbl_progress_completed">%s</td>', __('Completed', 'wp_courseware'));

                    // Showing a link to view details
                    $showDetailLink = true;
                    printf('<td><a href="%s&user_id=%d&quiz_id=%d&unit_id=%d" class="button-secondary">%s</a></td>',
                      admin_url('users.php?page=WPCW_showPage_UserProgess_quizAnswers'),
                      $userID, $quizDetails->quiz_id, $unit->ID,
                      __('View Survey Details', 'wp_courseware')
                    );
                  }

                  // Survey not taken yet
                  else {
                    printf('<td class="wpcw_center">%s</td>', __('Pending', 'wp_courseware'));
                  }
                }

                // Quiz - show correct answers.
                else
                {
                  $quizResults = WPCW_quizzes_getUserResultsForQuiz($userID, $unit->ID, $quizDetails->quiz_id);

                  // Show the admin how many questions were right.
                  if ($quizResults)
                  {
                    // -1% means that the quiz is needing grading.
                    if ($quizResults->quiz_grade < 0) {
                      printf('<td class="wpcw_center">%s</td>', __('Awaiting Final Grading', 'wp_courseware'));
                    }
                    else {
                      printf('<td class="wpcw_tbl_progress_completed">%d%%</td>', number_format($quizResults->quiz_grade, 1));
                    }


                    // Showing a link to view details
                    $showDetailLink = true;

                    printf('<td><a href="%s&user_id=%d&quiz_id=%d&unit_id=%d" class="button-secondary">%s</a></td>',
                      admin_url('users.php?page=WPCW_showPage_UserProgess_quizAnswers'),
                      $userID, $quizDetails->quiz_id, $unit->ID,
                      __('View Quiz Details', 'wp_courseware')
                    );

                  } // end of if  printf('<td class="wpcw_tbl_progress_completed">%s</td>'


                  // Quiz not taken yet
                  else {
                    printf('<td class="wpcw_center">%s</td>', __('Pending', 'wp_courseware'));
                  }

                } // end of if survey
              } // end of if $quizDetails


              // No quiz for this unit
              else {
                printf('<td class="wpcw_center">-</td>');
                printf('<td class="wpcw_center">-</td>');
              }

              // Quiz detail link
              if (!$showDetailLink) {
                printf('<td>&nbsp;</td>');
              }

              printf('</tr>');
            }

          }

        }
      } */

  //    printf('</tbody></table>');
      if($i4_percent_completed == 100){
        printf('<div class="my-courses-congrats">Congrats, Course Complete!</div>');
      }
      printf('</div> <!--end my-course-outline-wrapper -->');
      printf('</div> <!-- end my-course-wrapper -->');

      // Track number of courses user can actually access
      $courseCount++;

      } //end foreach courses as course

      // Course is not allowed to access any courses. So show a meaningful message.
      if ($courseCount == 0) {
        printf('You are not currently enrolled in a course. Please contact your Care Coordinator for assistance.', 'wp_courseware');
      }
    }

  }

  /**
   * Check if a user can access the specified training course.
   *
   * taken from WPCW. Doing this in case the plugin author decides to change the function during an update.
   *
   * @param Integer $courseID The ID of the course to check.
   * @param Integer $userID The ID of the user to check.
   * @return Boolean True if the user can access this course, false otherwise.
   */
  function I4_LMS_User_Can_Access($courseID, $userID){
  	global $wpcwdb, $wpdb;
  	$wpdb->show_errors();

  	$SQL = $wpdb->prepare("
  		SELECT *
  		FROM $wpcwdb->user_courses
  		WHERE user_id = %d AND course_id = %d
  	", $userID, $courseID);

  	return ($wpdb->get_row($SQL) != false);
  }

  /**
   * Display the percentage of the course that the user has completed
   *
   * @param INT $course_id for the users course
   * @param Object $modules the users modules for their course
   * @param INT $user_id the users ID
   */
   function i4_lms_percent_course_completed( $course_id, $modules, $user_id ){
     global $wpcwdb, $wpdb;
     $wpdb->show_errors();

     $num_units = 0;
     $num_units_completed = 0;

     //Get the number of completed units for the course by the user
     $num_units_completed = $this->i4_get_num_completed_units($course_id, $user_id);


     if ($modules){
       foreach ($modules as $module){

         //Get the units for the module
         $units = WPCW_units_getListOfUnits($module->module_id);

         foreach ($units as $unit){
           $num_units++;
         }
       }
     }

     //Do some Math here to get the percentage of the course completion.
     $percent_completed = ( $num_units_completed / $num_units ) * 100;

     //Let's keep the percentage to 2 decimal points.
     $percent_completed = number_format($percent_completed, 2, '.', '');

     return $percent_completed;
   }

 /**
  * Display the percentage of the course that the user has completed
  *
  * @param Integer $course_id The ID of the course that's being checked.
  * @param Integer $user_id The ID of the user.
  */
 function i4_get_num_completed_units( $course_id, $user_id ){
   global $wpcwdb, $wpdb;
   $wpdb->show_errors();

   $completed_status = "complete";

   //Here we grab the unit info for units that are completed for the users course.
   $SQL = $wpdb->prepare("
     SELECT * FROM $wpcwdb->units_meta LEFT JOIN $wpcwdb->user_progress
     ON $wpcwdb->units_meta.unit_id=$wpcwdb->user_progress.unit_id
     WHERE parent_course_id = %d AND unit_completed_status = %s AND user_id = %d
   ", $course_id, $completed_status, $user_id);

    //Store the Completed units Array
    $completed_units = $wpdb->get_results($SQL);

    //Count the number of elements in the Array to get the number of units in the course marked complete for the user.
    $num_completed = count($completed_units);

   return $num_completed;
 }

 /**
  * Display the percentage of the course that the user has completed
  *
  * @param Integer $course_id The ID of the course that's being checked.
  * @param Integer $user_id The ID of the user.
  */
 function i4_get_completed_units( $course_id, $user_id ){
   global $wpcwdb, $wpdb;
   $wpdb->show_errors();

    $completed_status = "complete";

    //Here we grab the unit info for units that are completed for the users course.
    $SQL = $wpdb->prepare("
      SELECT * FROM $wpcwdb->units_meta LEFT JOIN $wpcwdb->user_progress
      ON $wpcwdb->units_meta.unit_id=$wpcwdb->user_progress.unit_id
      WHERE parent_course_id = %d AND unit_completed_status = %s AND user_id = %d
    ", $course_id, $completed_status, $user_id);

    //Store the Completed units Array
    $completed_units = $wpdb->get_results($SQL);

    $completed_unit_ids = array();

    //set the counter
    $i = 0;

    //Store the Unit ID's into the completed unit id's array
    foreach ($completed_units as $completed_unit){
        $completed_unit_ids[$i] =  $completed_unit->unit_id;
        $i++;
    }

    return $completed_unit_ids;
  }

  /**
   * Checks if the Unit has been completed
   *
   * @param Integer $course_id The ID of the course that's being checked.
   * @param Integer $user_id The ID of the user.
   * @param Integer $unit_id The ID of the unit.
   */
  function i4_is_unit_complete( $course_id, $user_id, $unit_id ){
    global $wpcwdb, $wpdb;
    $wpdb->show_errors();

     $completed_status = "complete";

     //Here we grab the unit info for units that are completed for the users course.
     $SQL = $wpdb->prepare("
       SELECT * FROM $wpcwdb->units_meta LEFT JOIN $wpcwdb->user_progress
       ON $wpcwdb->units_meta.unit_id=$wpcwdb->user_progress.unit_id
       WHERE parent_course_id = %d AND unit_completed_status = %s AND user_id = %d
     ", $course_id, $completed_status, $user_id);

     //Store the Completed units Array
     $completed_units = $wpdb->get_results($SQL);

     $completed_unit_ids = array();

     //set the counter
     $i = 0;

     //Store the Unit ID's into the completed unit id's array
     foreach ($completed_units as $completed_unit){
         $completed_unit_ids[$i] =  $completed_unit->unit_id;
         $i++;
     }
     //If the unit is in the completed units array, display the completed checkmark.
     if (in_array ( $unit_id, $completed_unit_ids )){
        return true;
     }
     else {
       return false;
     }
   }

   /**
    * Return a list of courses assigned to the user
    *
    * @param Integer $user_id The ID of the current user.
    * @return Array of courses.
    */
    function i4_get_assigned_courses( $user_id ){
      global $wpcwdb, $wpdb;

      $wpdb->show_errors();
      //SELECT `course_title` FROM `wp_wpcw_user_courses` LEFT JOIN wp_wpcw_courses ON wp_wpcw_user_courses.course_id=wp_wpcw_courses.course_id WHERE `user_id`= 3

      $SQL = $wpdb->prepare( "SELECT `course_title` FROM $wpcwdb->user_courses LEFT JOIN $wpcwdb->courses ON $wpcwdb->user_courses.course_id=$wpcwdb->courses.course_id WHERE user_id = %d", $user_id );

      $user_courses = $wpdb->get_results($SQL);

      return $user_courses;
    }

 }
