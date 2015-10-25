<?php
/**
  * i-4Web Profile Settings Form. Handles the setup and functionality of the profile form.
  *
  * @package I4Web_LMS
  * @subpackage Classes/Profile Form
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
  class I4Web_LMS_Profile_Form{
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
     public function __construct(){
       global $current_i4_user;

       add_shortcode( 'i4_profile_form', array( $this, 'i4_lms_profile_form_shortcode' ) );
       add_action( 'plugins_loaded', array( $this, 'i4_lms_process_profile_form' ) );
     } //end construct

     /**
      * Setup the Account Settings form shortcode
      *
      * @since 0.0.1
      */
     function i4_lms_profile_form_shortcode(){
        ob_start();
        $this->i4_profile_form();
        return ob_get_clean();
      } // end i4_lms_profile_form_shortcode

    /**
     * Displays the account settings form for the i4_profile_form shortcode
     *
     * @since 0.0.1
     */
     function i4_profile_form(){
        global $current_i4_user;

        ?>

        <?php $this->i4_lms_profile_form_alerts(); //display the form alerts depending on the query param returned ?>

           <div class="form-container">
             <form action="" method="POST" id="profile-form-form" class="form-horizontal profile-form-form">
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('Username', 'i4' );?></label>
                       <p class="form-control-static"><strong><?php echo $current_i4_user->user_login;?></strong></p>
                 </div> <!-- end large-12 -->
               </div> <!-- end row -->
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('Email *', 'i4' );?></label>
                       <input type="text" class="patient-email" name="patient_email" value="<?php echo $current_i4_user->user_email;?>"/>
                 </div> <!-- end large-12 -->
               </div> <!-- end row -->
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('First Name *', 'i4' );?></label>
                       <input type="text" class="patient-fname" name="patient_fname" value="<?php echo $current_i4_user->user_firstname;?>"/>
                 </div> <!-- end large-12 -->
              </div> <!-- end row -->
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('Last Name *', 'i4' );?></label>
                       <input type="text" class="patient-lname" name="patient_lname" value="<?php echo $current_i4_user->user_lastname;?>"/>
                 </div> <!-- end large-12 -->
               </div> <!-- end row -->
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('Password', 'i4' );?></label>
                       <input type="password" class="patient-password" name="patient_password" value=""/>
                 </div> <!-- end large-12 -->
               </div> <!-- end row -->
               <div class="row">
                 <div class="large-12 columns">
                   <label><?php _e('Re-Type Password', 'i4' );?></label>
                       <input type="password" class="patient-password" name="patient_password_retyped" value=""/>
                       <div class="password-strength-wrapper">
                         <div id="password-strength"></div>
                       </div>
                 </div> <!-- end large-12 -->
               </div> <!-- end row -->

               <input type="hidden" name="action" value="profile-form"/>
               <input type="hidden" name="redirect" value="<?php echo get_permalink(); ?>"/>
               <input type="hidden" name="profile_form_nonce" value="<?php echo wp_create_nonce('profile-form-nonce'); ?>"/>
               <hr>
               <button type="submit" class="button small" id="profile-form-submit"><?php _e('Save', 'i4'); ?></button>
             </form>
           </div>


<?php }

    /**
     * Listens for the account settings action and update the user settings
     *
     * @since 0.0.1
     */
     function i4_lms_process_profile_form(){
       global $current_i4_user;

       if(isset($_POST['action']) && $_POST['action'] == 'profile-form' && wp_verify_nonce($_POST['profile_form_nonce'], 'profile-form-nonce')) {

         //Store the Password if patient has entered one in
         if( isset( $_POST['patient_password'] ) && $_POST['patient_password'] != '' ){
           $patient_password = $_POST['patient_password'];
         }

        //Do an email address check
        $email_check = email_exists( $_POST['patient_email'] );   //Returns false if no email matches in the DB and returns the user_id of the user if there is a match


        //Proceed if there is no email matching in the database. Also check to make sure the user_id being returned doesn't match the customer. This prevents a false query param when
        // the user is not changing their email address and submits changes to other information
        if( !$email_check && $email_check != $current_i4_user->ID ){

          if($patient_password != ''){  //update the user info including the password
            //Update the Customer user information including their email
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'user_email' => $_POST['patient_email'], 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'], 'user_pass' => $patient_password ) );
          }
          else{
            //Update the Customer user information including their email but not their password
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'user_email' => $_POST['patient_email'], 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'] ) );
          }


            //if for some reason wp_update_user fails
             if ( is_wp_error( $updated_user_id ) ){
               $redirect = add_query_arg( 'update-settings', 'failed', $_POST['redirect']);
               wp_redirect($redirect);
               exit;
             }

          // redirect on successful updating of settings
          $redirect = add_query_arg( array(
                                     'update-settings' => 'success',
                                     'user' => $current_i4_user->ID
                        ),$_POST['redirect']
                        );
        }
        elseif( $email_check && $email_check == $current_i4_user->ID ){  //The customer is submitting changes to their information but their email is the same

          if($patient_password != ''){  //update the password along with the other information
            //Update the Customer user information including their email
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'user_email' => $_POST['patient_email'], 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'], 'user_pass' => $patient_password ) );
          }
          else{
            //Update the Customer user information including their email
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'user_email' => $_POST['patient_email'], 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'] ) );
          }



            //if for some reason wp_update_user fails
             if ( is_wp_error( $updated_user_id ) ){
               $redirect = add_query_arg( 'update-settings', 'failed', $_POST['redirect']);
               wp_redirect($redirect);
               exit;
             }

          // redirect on successful updating of settings
          $redirect = add_query_arg( array(
                                     'update-settings' => 'success',
                                     'user' => $current_i4_user->ID
                        ),$_POST['redirect']
                        );
        }
        else{  //The email address is already taken. Update the rest of the information but not the email.

          if($patient_password != ''){  //update the password along with the other information
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'], 'user_pass' => $patient_password ) );
          }
          else{
            $updated_user_id = wp_update_user( array( 'ID' => $current_i4_user->ID, 'first_name' => $_POST['patient_fname'],
                                   'last_name' => $_POST['patient_lname'], 'display_name' => $_POST['patient_fname'] . ' ' . $_POST['patient_lname'] ) );
          }


          //if for some reason wp_update_user fails
          if ( is_wp_error( $updated_user_id ) ){
              $redirect = add_query_arg( 'update-settings', 'failed', $_POST['redirect']);
              wp_redirect($redirect);
              exit;
          }

          $redirect = add_query_arg( 'update-settings', 'email', $_POST['redirect']);
          wp_redirect($redirect);
          exit;

        }

        //Redirect with the proper parameters
        wp_redirect($redirect);
        exit;

       }
   }

   /**
    * Displays the profile form alerts
    *
    * @since 0.0.1
    */
    function i4_lms_profile_form_alerts(){

      //Display a Success Message if the new settings were updated
      if(isset( $_GET['update-settings'] ) ) {

        $update_status = $_GET['update-settings'];

        //Display different Messages depending on the query param
        switch ( $update_status ) {
            case "email":
                echo '<div data-alert class="alert-box warning radius">' .  __('Sorry! It looks like that e-mail is already in use. Please choose another email and try again.', 'i4') . '<a href="#" class="close">&times;</a></div>';
                break;
            case "failed":
                echo '<div data-alert class="alert-box alert radius">' .  __('Sorry! It looks like something went wrong while changing your information. Please check your information and try again.', 'i4') . '<a href="#" class="close">&times;</a></div>';
                break;
            default:
                echo '<div data-alert class="alert-box success radius">' .  __('Great! Your Account has been updated.', 'i4') . '<a href="#" class="close">&times;</a></div>';
        }
      }

    }

  }
