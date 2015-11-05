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

       <div id="new-patient-modal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
         <h2 id="modalTitle">Add New Patient</h2>

         <a class="close-reveal-modal" aria-label="Close">&#215;</a>
       </div>

       <table>
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
                 <span class="manage-patient-action"><a href="#" title="Modify Courses" data-reveal-id="<?php echo $patient->user_login;?>"><i class="fa fa-list"></i></a></span>
                 <span class="manage-patient-action"><a href="#" title="Remove Patient"><i class="fa fa-times"></i></a>
               </td>
             </tr>

             <div id="<?php echo $patient->user_login;?>" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
               <h2 id="modalTitle">Awesome. I have it.</h2>
               <p class="lead">Your couch.  It is mine.</p>
               <p>I'm a cool paragraph that lives inside of an even cooler modal. Wins!</p>
               <a class="close-reveal-modal" aria-label="Close">&#215;</a>
             </div>

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




  }
