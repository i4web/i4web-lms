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

       <?php $this->i4_new_patient_modal( 'new-patient-modal' );?>

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
                 <span class="manage-patient-action"><a href="#" title="Modify Courses" data-reveal-id="<?php echo 'modify-courses-' .$patient->user_login;?>"><i class="fa fa-list"></i></a></span>
                 <span class="manage-patient-action"><a href="#" title="Remove Patient"><i class="fa fa-times"></i></a>
               </td>
             </tr>

             <?php $this->i4_modify_courses_modal( $patient->user_login  ); ?>

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
                      <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                    </div>';

        echo $html;
     }

    /**
     * Generate Manage Courses Modal
     *
     * @since 0.0.1
     * @param string ID of the modal we want to generate. Should match the data-reveal-id of the element that we're using to trigger the modal
     */
     function i4_modify_courses_modal( $patient_login ){

       $html =    '<div id="modify-courses-' .$patient_login. '" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                     <h3 id="modalTitle">Manage Courses for <i>'.$patient_login .'</i> </h3>
                     <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                   </div>';
       echo $html;
     }




  }
