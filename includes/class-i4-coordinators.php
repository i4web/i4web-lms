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
if (!defined('ABSPATH')) {
    exit;
}

/**
 * I4Web_LMS_Coordinators Class
 *
 * This handles the Coordinators admin page, and any functions pertaining to Coordinators
 *
 * @since 0.0.1
 */
class I4Web_LMS_Coordinators {
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
    public function __construct() {
        global $wpdb, $wpcwdb;

        add_action( 'init', array( $this, 'i4_lms_add_coordinator' ));
        add_action( 'init', array( $this, 'i4_lms_edit_coordinator' ));
        add_action( 'show_user_profile', array( $this, 'i4_coordinator_user_profile_fields' ));
        add_action( 'edit_user_profile', array( $this, 'i4_coordinator_user_profile_fields' ));
        add_action( 'personal_options_update', array( $this, 'i4_save_user_profile_fields' ));
        add_action( 'edit_user_profile_update', array( $this, 'i4_save_user_profile_fields' ));

    }

    /**
     * Display the new Coordinators Form for the Coordinators admin page
     *
     * @since 0.0.1
     */
    function new_coordinator_form() {

        //Retrieve Courses information
        $courses = $this->i4_lms_get_courses();

        if (isset($_GET['add_coordinator']) && $_GET['add_coordinator'] == 'true') {
            $this->i4_lms_coordinator_success_msg();
        }
        elseif (isset($_GET['add_coordinator']) && $_GET['add_coordinator'] == 'false') {
            $this->i4_lms_coordinator_error_msg();
        }
        elseif (isset($_GET['action']) && $_GET['action'] = 'delete') {
            $this->i4_lms_coordinator_delete($_GET['coordinator_id']);
        }
        elseif (isset($_GET['edited_coordinator']) && $_GET['edited_coordinator'] == 'true' ){
            $this->i4_lms_edited_coordinator_success_msg();
        }
        ?>
        <form action="" method="POST">
            <h3>Add a New Coordinator</h3>

            <p>Please enter in the Coordinator details below</p>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Email</th>
                        <td>
                            <input type="email" name="coordinator_email" value="">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Username</th>
                        <td>
                            <input type="text" name="coordinator_username" value="">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">First Name</th>
                        <td>
                            <input type="text" name="coordinator_fname" value="">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Last Name</th>
                        <td>
                            <input type="text" name="coordinator_lname" value="">
                        </td>
                    </tr>
                <tr>
                    <th scope="row">Assign a Course</th>
                    <td>
                        <select name="coordinator_course_id">
                            <?php foreach ($courses as $course) {
                                echo '<option value="' . $course->course_id . '">' . $course->course_title . '</option>';
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Upload Image</th>
                    <td>
                        <div class="section-nav-logo section-upload">
                            <input type="text" name="coordinator_img" class="login-logo-url" value=""/>
                            <input id="nav-logo" class="upload-button button button-primary" type="button"
                                   value="Upload Image"/> <br/>
                            <span class='description'>Required dimensions: 100px X 100px.</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="add_coordinator"/>
            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
            <input type="hidden" name="new_coordinator_nonce"
                   value="<?php echo wp_create_nonce('new-coordinator-nonce'); ?>"/>

            <?php submit_button(); ?>
        </form>
        <?php
    }

    /**
     * Display the Edit Coordinators Form for the Coordinators admin page
     *
     * @since 0.0.1
     */
    function edit_coordinator_form() {
        //Retrieve Courses information
        $courses = $this->i4_lms_get_courses();
        $coordinator_id = $_GET['coordinator_id'];

        $coordinator = get_userdata( $coordinator_id );
        $coordinator_course_id = get_user_meta($coordinator_id, "coordinator_course_id", true);

        if (isset($_GET['edited_coordinator']) && $_GET['edited_coordinator'] == 'false' ){
            $this->i4_lms_coordinator_error_msg();
        }
        ?>
        <form action="" method="POST">
            <h3>Edit Coordinator</h3>

            <p>Enter the Coordinator information that needs to be changed below</p>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Email</th>
                        <td>
                            <input type="email" name="coordinator_email" value="<?php echo $coordinator->user_email;?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Username</th>
                        <td>
                            <p><?php echo $coordinator->user_nicename;?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">First Name</th>
                        <td>
                            <input type="text" name="coordinator_fname" value="<?php echo $coordinator->first_name;?>">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Last Name</th>
                        <td>
                            <input type="text" name="coordinator_lname" value="<?php echo $coordinator->last_name;?>">
                        </td>
                    </tr>
                <tr>
                    <th scope="row">Assign a Course</th>
                    <td>
                        <select name="coordinator_course_id">
                            <?php foreach ($courses as $course) {
                                if($course->course_id == $coordinator_course_id){
                                    echo '<option value="' . $course->course_id . '" selected>' . $course->course_title . '</option>';
                                }
                                else{
                                    echo '<option value="' . $course->course_id . '">' . $course->course_title . '</option>';
                                }
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Upload Image</th>
                    <td>
                        <div class="section-nav-logo section-upload">
                            <input type="text" name="coordinator_img" class="login-logo-url" value="<?php echo get_user_meta($coordinator->ID, "coordinator_img", true); ?>"/>
                            <input id="nav-logo" class="upload-button button button-primary" type="button"
                                   value="Upload Image"/> <br/>
                            <span class='description'>Required dimensions: 75px X 75px.</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="edit_coordinator"/>
            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
            <input type="hidden" name="edit_coordinator_nonce" value="<?php echo wp_create_nonce('edit-coordinator-nonce'); ?>"/>

            <?php submit_button(); ?>
        </form>
    <?php
    }

    /**
     * Listens for the add coordinator action and processes the data
     *
     * @since 0.0.1
     */
    function i4_lms_add_coordinator() {
        global $wpdb;

        if (isset($_POST['action']) && $_POST['action'] == 'add_coordinator' && wp_verify_nonce($_POST['new_coordinator_nonce'], 'new-coordinator-nonce')) {

            //Sanitize the Username field
            $coordinator_username = sanitize_user($_POST['coordinator_username']);

            //Sanitize the Coordinator email field
            $coordinator_email = sanitize_email($_POST['coordinator_email']);

            //Sanitize the Coordinator Name fields
            $coordinator_fname = sanitize_text_field($_POST['coordinator_fname']);
            $coordinator_lname = sanitize_text_field($_POST['coordinator_lname']);

            //Sanitize the intval to only submit integers
            $course_id = intval($_POST['coordinator_course_id']);

            if (!$course_id) {
                $course_id = ''; //if the value submitted is not an integer, we blank that out
            }

            $coordinator_img = sanitize_text_field($_POST['coordinator_img']);

            $user_data = array(
              'user_login'    =>  $coordinator_username,
              'user_email'    =>  $coordinator_email,
              'first_name'    =>  $coordinator_fname,
              'last_name'     =>  $coordinator_lname,
              'role'          =>  'coordinator'  //explicitly define this in case the default settings get changed in WP Admin

            );

            //Create a new Coordinator
            $new_coordinator_id = $this->i4_lms_insert_coordinator($user_data, $course_id, $coordinator_img );

            if ($new_coordinator_id == false) {
                // redirect on error
                $redirect = add_query_arg(array(
                    'add_coordinator'   => 'false',
                    'coordinator'       => $course_id,
                    'coordinator_name'  => $coordinator_name,
                    'coordinator_img'   => $coordinator_img,
                    'coordinator_email' => $coordinator_email
                ), $_POST['redirect']
                );
            }
            else {
                //Send an email to the new coordinator
                wp_new_user_notification( $new_coordinator_id, $deprecated = null, $notify = 'both' );

                // redirect on success
                $redirect = add_query_arg(array(
                    'add_coordinator'   => 'true',
                    'id'                => $new_coordinator_id,
                    'coordinator'       => $course_id,
                    'coordinator_name'  => $coordinator_name,
                    'coordinator_img'   => $coordinator_img,
                    'coordinator_email' => $coordinator_email
                ), $_POST['redirect']
                );
            }
            // redirect back to our previous page with the added query variable
            wp_redirect($redirect);
            exit;
        }
    }

    /**
     * Listens for the edit coordinator action and processes the data
     *
     * @since 0.0.1
     */
    function i4_lms_edit_coordinator() {
        global $wpdb;

        if (isset($_POST['action']) && $_POST['action'] == 'edit_coordinator' && wp_verify_nonce($_POST['edit_coordinator_nonce'], 'edit-coordinator-nonce')) {

            $coordinator_id = $_GET['coordinator_id'];

            //Sanitize the Coordinator email field
            $coordinator_email = sanitize_email($_POST['coordinator_email']);

            //Sanitize the Coordinator Name fields
            $coordinator_fname = sanitize_text_field($_POST['coordinator_fname']);
            $coordinator_lname = sanitize_text_field($_POST['coordinator_lname']);

            //Sanitize the intval to only submit integers
            $course_id = intval($_POST['coordinator_course_id']);

            if (!$course_id) {
                $course_id = ''; //if the value submitted is not an integer, we blank that out
            }

            $coordinator_img = sanitize_text_field($_POST['coordinator_img']);

            $coordinator_data = array(
                'ID'            =>  $coordinator_id,
                'user_email'    =>  $coordinator_email,
                'first_name'    =>  $coordinator_fname,
                'last_name'     =>  $coordinator_lname,
                'display_name'  =>  $coordinator_fname . ' ' . $coordinator_lname
            );

            $coordinator_id = wp_update_user( $coordinator_data );

            //Update the Course ID and the Coordinator IMG values
            update_user_meta( $coordinator_id, 'coordinator_course_id', $course_id );
            update_user_meta( $coordinator_id, 'coordinator_img', $coordinator_img );

            if ( is_wp_error( $coordinator_id ) ) {
	            // There was an error
                $redirect = add_query_arg(array(
                    'edited_coordinator'            => 'false'
                ), $_POST['redirect']
                );

            }
            else {
            	// Success!
                $redirect = add_query_arg(array(
                    'edited_coordinator'            => 'true',
                    'coordinator_id'                => $coordinator_id
                ), 'admin.php?page=coordinators'
                );
            }



            // redirect back to our previous page with the added query variable
            wp_redirect($redirect);
            exit;

        }
    }

    /**
     * Display all Coordinators (Used in the Coordinators Admin area)
     *
     * @since 0.0.1
     */
    function i4_lms_display_coordinators() {
        ?>
        <h3>Current Course Coordinators</h3>
        <?php

        $coordinators = $this->i4_get_coordinators();

        echo '<div class="coordinators-wrapper">';
        foreach ($coordinators as $coordinator) {
            //The coordinators assigned course ID
            $coordinator_course_id = get_user_meta($coordinator->ID, "coordinator_course_id", true);

            //The coordinators assigned course title
            $coordinator_course_title = $this->i4_get_course_title($coordinator_course_id);

            echo '<div class="coordinator">';
            echo '<img src="' . get_user_meta($coordinator->ID, "coordinator_img", true) . '" alt="' . $coordinator->coordinator_name . '"/>';
            echo '<p>' . $coordinator->display_name . '</p>';
            echo '<p>' . $coordinator->user_email . '</p>';
            echo '<p> Course - ' . $coordinator_course_title . '</p>';
            echo '<span><a href="?page=coordinators&action=edit&coordinator_id=' . $coordinator->ID . '&course_id='. $coordinator_course_id .'">Edit</a></span> ';
            echo '<span><a href="?page=coordinators&action=delete&coordinator_id=' . $coordinator->ID . '&course_id='. $coordinator_course_id .'">Delete</a>';
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * Return all Coordinators.
     *
     * @since 0.0.1
     * @return array of coordinators
     */
    function i4_get_coordinators() {
        global $wpcwdb, $wpdb;

        $wpdb->show_errors();

        $SQL = "SELECT u.ID, u.user_login, u.display_name, u.user_email, m.meta_key, m.meta_value FROM wp_users u
                INNER JOIN wp_usermeta m ON m.user_id = u.ID
                WHERE m.meta_key = 'wp_capabilities'
                  AND m.meta_value LIKE '%coordinator%'
                ORDER BY LOWER(u.display_name)";
        $coordinators = $wpdb->get_results($SQL, OBJECT_K);

        return $coordinators;
    }

    function i4_get_course_title($course_id){
        global $wpcwdb, $wpdb;

        $wpdb->show_errors();

        $table_name = $wpdb->prefix . 'wpcw_courses';

        $course_title = $wpdb->get_var($wpdb->prepare(
            "
        SELECT `course_title` FROM `wp_wpcw_courses` WHERE `course_id`=  %d
      	",
            $course_id
        ));

        return $course_title;

    }

    /**
     * Get a an array containing the course id and course title for all courses
     *
     * @since 0.0.1
     */
    function i4_lms_get_courses() {
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
     * @param array $user_data - The Coordinator's information: username, email, first name, last name, course ID and image
     * @return Returns the new user ID on success, false otherwise
     */
    function i4_lms_insert_coordinator( $user_data, $course_id, $coordinator_img ) {
        $user_id = wp_insert_user( $user_data ) ;

        if( !is_wp_error( $user_id )) {
             //If the user was successfully created, update their meta with their community, apartment number, and their stripe customer ID
              update_user_meta( $user_id, 'coordinator_course_id', $course_id );
              update_user_meta( $user_id, 'coordinator_img', $coordinator_img );
        }

        return $user_id;
    }

    /**
     * Deletes a Coordinator from the database
     *
     * @since 0.0.1
     * @param string $name - The Coordinator's ID
     * @return Returns the number of rows affected or false if the query did not execute
     */
    function i4_lms_coordinator_delete($coordinator_id) {
        //Delete the user completely from the Database. All meta_data is also deleted
        $delete_status = wp_delete_user( $coordinator_id );

    }

    /**
     * Displays the Error message when a Coordinator is not successfully added to the DB
     *
     * @since 0.0.1
     */
    function i4_lms_coordinator_success_msg() {
        $class = "updated";
        $message = "Nice! A new Coordinator was successfully added.";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * Displays the Error message when a Coordinator is not successfully added to the DB
     *
     * @since 0.0.1
     */
    function i4_lms_edited_coordinator_success_msg() {
        $class = "updated";
        $message = "Your Coordinator was successfully edited.";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * Displays the Error message when a Coordinator is not successfully added to the DB
     *
     * @since 0.0.1
     */
    function i4_lms_coordinator_error_msg() {
        $class = "error";
        $message = "An error occurred. Please check your information and try again";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * Retrieves the Coordinator based on the Course ID
     *
     * @since 0.0.1
     */
    function i4_lms_get_coordinator($course_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'i4_lms_coordinators';
        $wpdb->show_errors();

        $SQL = $wpdb->prepare("
  		SELECT u.ID, u.user_login, u.display_name, u.user_email FROM wp_users u
                INNER JOIN wp_usermeta m ON m.user_id = u.ID
                WHERE m.meta_key = 'coordinator_course_id'
                  AND m.meta_value = %d
                ORDER BY LOWER(u.display_name)
  	", $course_id);

        return $wpdb->get_row($SQL);

    }

    /**
     * Adds Fields for the Custom Coordinator Metadata
     *
     * @since 0.0.1
     */
    function i4_coordinator_user_profile_fields( $user ) {
        ?>
    	<h3><?php _e('Coordinator Information', 'i4'); ?></h3>
    	<table class="form-table">
    		<tr>
    			<th>
    				<label for="coordinator_course_id"><?php _e('Coordinator Course ID', 'i4'); ?>
    			</label></th>
    			<td>
    				<input type="text" name="coordinator_course_id" id="coordinator_course_id" value="<?php echo esc_attr( get_the_author_meta( 'coordinator_course_id', $user->ID ) ); ?>" class="regular-text" /><br />
    				<span class="description"><?php _e('Please enter the Course ID for this Coordinator.', 'i4'); ?></span>
    			</td>
    		</tr>
        <tr>
          <th>
            <label for="coordinator_img"><?php _e('Coordinator Image', 'i4'); ?>
          </label></th>
          <td>
            <input type="text" name="coordinator_img" id="coordinator_img" value="<?php echo esc_attr( get_the_author_meta( 'coordinator_img', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e('Please type the URL of the image for this Coordinator', 'i4'); ?></span>
          </td>
        </tr>
    	</table>
<?php }

    /**
     * Save the Custom Coordinator User Profile Fields
     *
     * @since 0.0.1
     */
    function i4_save_user_profile_fields( $user_id ) {
    	if ( !current_user_can( 'edit_user', $user_id ) )
    		return FALSE;

        update_usermeta( $user_id, 'coordinator_course_id', $_POST['coordinator_course_id'] );
    	update_usermeta( $user_id, 'coordinator_img', $_POST['coordinator_img'] );

    }

    function i4_lms_get_coordinator_course(){
      echo 'Hello World';
    }
}
