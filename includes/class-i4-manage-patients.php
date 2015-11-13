<?php
/**
  * i-4Web Manage Patients Class. Handles the setup and functionality of the manage patients page.
  *
  * @package I4Web_LMS
  * @subpackage Classes/Manage Patients
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

 /**
  * I4Web_LMS_Manage_Patients Class
  *
  * @since 0.0.1
  */
  class I4Web_LMS_Manage_Patients{
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */

     public function __construct(){
       add_shortcode( 'i4_manage_patients', array( $this, 'i4_lms_manage_patients_shortcode' ) );
       add_action('wp_ajax_i4_lms_handle_update_patient_courses', array( $this, 'i4_update_patient_courses') );
       add_action('wp_ajax_i4_lms_handle_add_new_patient', array( $this, 'i4_ajax_add_new_patient') );
       add_action('wp_ajax_i4_lms_get_modify_courses_modal', array( $this, 'i4_modify_courses_modal') );

     }

    /**
     *
     * Setup the Manage Patients shortcode
     *
     * @since 0.0.1
     */
     function i4_lms_manage_patients_shortcode(){
       ob_start();
       $this->i4_manage_patients();
       return ob_get_clean();
     } // end i4_lms_profile_form_shortcode

    /**
     * The Manage Patients shortcode
     *
     * @since 0.0.1
     */
     function i4_manage_patients(){
       $patients =  $this->i4_get_patients();
       ?>
       <div class="page-title">
         <h3><?php echo get_the_title();?> <span><a href="#" data-reveal-id="new-patient-modal" class="button tiny blue">Add New Patient</a></h3>
       </div>

       <?php
         $this->i4_new_patient_modal( 'new-patient-modal' );
       ?>

       <table class="manage-patients-table">
         <thead>
           <tr>
             <th>Patient Username</th>
             <th>Patient Email</th>
             <th>Patient Courses</th>
             <th>Actions</th>
           </tr>
         </thead>
         <tbody>
           <?php foreach($patients as $patient){ //loop through each of the patients
              $patient_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient->ID); //Retrieve the assigned courses for the patient
             ?>
             <tr>
               <td><?php echo $patient->user_login;?></td>
               <td><?php echo $patient->user_email;?></td>
               <td>
                 <?php foreach($patient_courses as $patient_course){
                   echo $patient_course->course_title .'<br>';
                 }?>
               </td>
               <td>
                 <span class="manage-patient-action"><a href="#" title="Edit Patient"><i class="fa fa-pencil"></i></a></span>
                 <span class="manage-patient-action"><a href="#" title="Modify Courses" data-reveal-id="<?php echo 'modify-courses-' .$patient->ID;?>"><i class="fa fa-list"></i></a></span>
                 <span class="manage-patient-action"><a href="#" title="Remove Patient"><i class="fa fa-times"></i></a>
               </td>
             </tr>

           <?php } ?>
         </tbody>
       </table>

    <?php
     }

    /**
     * Return all Patients.
     *
     * @since 0.0.1
     * @return array of patients
     */
     function i4_get_patients(){
       global $wpcwdb, $wpdb;

       $wpdb->show_errors();

       $SQL = "SELECT u.ID, u.user_login, u.user_nicename, u.user_email FROM wp_users u INNER JOIN wp_usermeta m ON m.user_id = u.ID WHERE m.meta_key = 'wp_capabilities' AND m.meta_value LIKE '%patient%' ORDER BY u.user_registered";
       $patients = $wpdb->get_results($SQL, OBJECT_K);

       return $patients;
     }

    /**
     * Generate a New Patient Modal
     *
     * @since 0.0.1
     * @param string ID of the modal we want to generate. Should match the data-reveal-id of the element that we're using to trigger the modal
     */
     function i4_new_patient_modal( $modal_id ){

        $html =    '<div id="' .$modal_id. '" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                      <h3 id="modalTitle">Add New Patient</h3>
                      <a class="close-reveal-modal" aria-label="Close">&#215;</a>';

        $html .= $this->i4_add_new_patient_form();

        $html .= '</div>';

        echo $html;
     }

      /**
     * Generate the Add New Patient Form
     *
     * @since 0.0.1
     */
     function i4_add_new_patient_form(){

       $content =  '<div class="form-container">
                      <form action="" method="POST" id="add-new-patient-form" class="form-horizontal add-new-patient-form">
                        <div class="row">
                          <div class="large-12 columns">
                          <div class="row collapse">
                            <div class="small-10 columns">
                              <input type="email" class="patient-email" id="patient_email" name="patient_email" placeholder="Email" value="" required/>
                            </div> <!-- end .small-10 columns -->
                            <div class="small-2 columns">
                              <span id="i4_email_availability_status" class="postfix"></span>
                            </div> <!-- end .small-2 columns -->
                          </div> <!-- end .row collapse -->
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        <div class="row">
                          <div class="large-12 columns">
                          <div class="row collapse">
                            <div class="small-10 columns">
                              <input type="email" class="patient-username" id="patient_username" name="patient_username" placeholder="Username" value="" required/>
                            </div> <!-- end .small-10 columns -->
                            <div class="small-2 columns">
                              <span id="i4_username_availability_status" class="postfix"></span>
                            </div> <!-- end .small-2 columns -->
                          </div> <!-- end .row collapse -->
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->

                        <div class="row">
                          <div class="large-12 columns">
                            <label>First Name</label>
                            <input type="text" class="patient-fname" id="patient_fname" name="patient_fname" value="" required/>
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        <div class="row">
                          <div class="large-12 columns">
                            <label>Last Name</label>
                            <input type="text" class="patient-lname" id="patient_lname" name="patient_lname" value="" required/>
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        <div class="row">
                          <div class="large-12 columns">
                            <input type="hidden" name="action" value="add-new-patient"/>
                            <button class="button tiny blue" type="submit" id="add-new-patient-submit">Next</button>
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        </form>
                        <div class="row">
                          <div class="large-12 columns">
                            <div id="i4_new_patient_message"></div>
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                    </div>
       ';

         return $content;
     }

     /**
      * Generate Manage Courses Modal
      *
      * @since 0.0.1
      * @param string ID of the modal we want to generate. Should match the data-reveal-id of the element that we're using to trigger the modal
      */
     function i4_modify_courses_modal(){
         $patient_id = sanitize_text_field($_GET['patientId']);
         $patient_name = sanitize_text_field($_GET['patientName']);

         //retrieve the courses
         $all_courses =  I4Web_LMS()->i4_wpcw->i4_get_all_courses();
         $user_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient_id);
         $unassigned_courses = array_diff($all_courses, $user_courses);

         $html = '<div id="modify-courses-' .$patient_id. '" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                    <input id="patientId" type="hidden" name="patientId" value="'.$patient_id.'"/>
                    <h3 id="modalTitle">Manage Courses for <i>'.$patient_name .'</i> </h3>
                    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                    <ul id="available-courses" class="connectedSortable">
         ';

         $html .= $this->i4_courses_to_list($unassigned_courses);
         $html .=   '</ul>
                     <ul id="user-courses" class="connectedSortable">
         ';
         $html .= $this->i4_courses_to_list($user_courses);
         $html .=   '</ul>
                   <button class="button tiny blue" type="submit" id="update-patient-courses-submit">Done</button>
               </div>
         ';
         echo $html;
         die();
     }

    /**
     * Called when adding a new patient.
     *
     */
     function i4_ajax_add_new_patient(){
         global $current_i4_user;

         $response = array();
         // Security check
         $security_check = check_ajax_referer( 'add_new_patient_nonce', 'security', false );

         if ( !$security_check ) {
             die (__('Sorry, we are unable to perform this action. Contact support if you are receiving this in error!', 'i4'));
         }

         //Perform a permissions check just in case
         if ( !user_can( $current_i4_user, 'manage_patients' ) ){
             die (__('Sorry but you do not have the proper permissions to perform this action. Contact support if you are receiving this in error', 'i4'));
         }

         $first_name = $_POST['patient_fname'];
         $last_name = $_POST['patient_lname'];
         $patient_array = array(
             'user_login'   => sanitize_text_field($_POST['patient_username']),
             'user_email'   => sanitize_text_field($_POST['patient_email']),
             'first_name'   => sanitize_text_field($first_name),
             'last_name'    => sanitize_text_field($last_name),
             'role'         => 'patient'
         );

         $patient_id = I4Web_LMS()->i4_manage_patients->i4_insert_patient( $patient_array );

         if (!$patient_id){
             $response['status'] = 409;
         }
         else {
             $response['status'] = 200;
             $response['patient_id'] = $patient_id;
             $response['patient_name'] = $first_name . " " . $last_name;
         }
         echo json_encode($response);

         die();
     }
     /**
      * Generate the list elements from a list of courses
      */
     function i4_courses_to_list( $courses ) {
         $result = '';
         foreach ($courses as $index => $course_title){
             $result .= '<li id="'.$index.'">'.$course_title.'</li>';
         }
         return $result;
     }

     /**
      * @param $patient_id - The ID of the patient whose courses are being modified
      * @param $new_user_courses - The list of courses that the user is assigned after the modifications in the modal
      */
     function i4_update_patient_courses() {
         $response = array();
         $patient_id = sanitize_text_field($_POST['patientId']);
         $new_user_courses = $_POST['courses'];

         $current_user_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient_id);
         $added_courses = array_diff($new_user_courses, $current_user_courses);
         $removed_courses = array_diff($current_user_courses, $new_user_courses);

         $this->add_courses($patient_id, $added_courses);
         $this->remove_courses($patient_id, $removed_courses);

         $response['status'] = 200;
         echo json_encode($response);
         die();
     }

     function add_courses($patient_id, $courses) {
         global $wpdb;
         $enrollment_date = date('Y-m-d H:i:s');
         foreach($courses as $course) {
             $wpdb->query(
                 $wpdb->prepare(
                     "INSERT INTO wp_wpcw_user_courses(user_id, course_id, course_enrolment_date) VALUES(%d, %d, %s)",
                     $patient_id,
                     sanitize_text_field($course),
                     $enrollment_date
                 )
             );
         }
     }

     function remove_courses($patient_id, $courses) {
         global $wpdb;
         foreach($courses as $course) {
             $wpdb->query(
                 $wpdb->prepare(
                     "DELETE FROM wp_wpcw_user_courses WHERE user_id = %d AND course_id = %d",
                     $patient_id,
                     sanitize_text_field($course)
                 )
             );
         }
     }

     function i4_insert_patient( $patient_array ){

         $patient_id = wp_insert_user( $patient_array );

         if ( ! is_wp_error( $patient_id ) ) {
             //Send off a new user notification to the admin and the new patient
             wp_new_user_notification( $patient_id, $deprecated = null, $notify = 'both' );

             return $patient_id;
         }
         else{
             return false;
         }
     }
  }
