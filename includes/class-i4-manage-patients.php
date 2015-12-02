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
if (!defined('ABSPATH')) {
    exit;
}

/**
 * I4Web_LMS_Manage_Patients Class
 *
 * @since 0.0.1
 */
class I4Web_LMS_Manage_Patients {
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */

    public function __construct() {
        add_shortcode('i4_manage_patients', array($this, 'i4_lms_manage_patients_shortcode'));
        add_action('wp_ajax_i4_lms_handle_update_patient_courses', array($this, 'i4_update_patient_courses'));
        add_action('wp_ajax_i4_lms_handle_add_new_patient', array($this, 'i4_ajax_add_new_patient'));
        add_action('wp_ajax_i4_lms_get_user_courses', array($this, 'i4_get_user_courses'));
        add_action('wp_ajax_i4_lms_remove_patient', array($this, 'i4_remove_patient'));
        add_action('wp_ajax_i4_lms_update_patient', array($this, 'i4_update_patient'));
        add_action('wp_ajax_i4_lms_get_patient_info', array($this, 'i4_get_patient_info'));

    }

    /**
     *
     * Setup the Manage Patients shortcode
     *
     * @since 0.0.1
     */
    function i4_lms_manage_patients_shortcode() {
        ob_start();
        $this->i4_manage_patients();
        return ob_get_clean();
    } // end i4_lms_profile_form_shortcode

    /**
     * The Manage Patients shortcode
     *
     * @since 0.0.1
     */
    function i4_manage_patients() {
        $patients = $this->i4_get_patients();
        ?>
        <div class="page-title">
            <h3><?php echo get_the_title(); ?> <span><a id="add-patients" href="#" class="button tiny blue">Add New Patient</a></h3>
        </div>

        <?php
        $this->i4_new_patient_modal();
        $this->i4_modify_courses_modal();
        ?>
        <div class="manage-patients-table-wrapper">
            <table id="manage-patients-table" class="manage-patients-table tablesorter">
                <thead>
                <tr>
                    <th data-sorter="text">Patient Name</th>
                    <th data-sorter="text">Patient Email</th>
                    <th data-sorter="false">Patient Courses</th>
                    <th data-sorter="false">Actions</th>
                </tr>
                </thead>
                <tbody id="patients-list">
                <?php foreach ($patients as $patient) { //loop through each of the patients
                    $patient_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient->ID); //Retrieve the assigned courses for the patient
                    ?>
                    <tr id="<?php echo $patient->ID ?>">
                        <td class="patient-name"><?php echo $patient->display_name; ?></td>
                        <td class="patient-email"><?php echo $patient->user_email; ?></td>
                        <td class="patient-courses">
                            <?php foreach ($patient_courses as $course_id => $course_title) {
                                echo $course_title . '<br>';
                            } ?>
                        </td>
                        <td class="patient-actions">
                            <span class="manage-patient-action">
                                <a href="#" title="Edit Patient">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            </span>
                            <span class="manage-patient-action">
                                <a href="#" title="Modify Courses">
                                    <i class="fa fa-list"></i>
                                </a>
                            </span>
                            <span class="manage-patient-action">
                                <a href="#" data-confirm class="remove-patient" title="Remove Patient">
                                    <i class="fa fa-times"></i>
                                </a>
                            </span>
                        </td>
                    </tr>

                <?php } ?>
                </tbody>
            </table>

            <div id="pager" class="pager">
                <form>
                    Page: <select class="gotoPage" title="Select page number"></select>
                    <i class="first fa fa-step-backward text-blue"/></i>
                    <i class="prev fa fa-backward text-blue"/></i>
                    <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
                    <i class="next fa fa-forward text-blue"/></i>
                    <i class="last fa fa-step-forward text-blue"/></i>
                    <div class="right">
                    Show
                    <select class="pagesize">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    Patients
                    </div>
                </form>
            </div>

        </div> <!-- end manage-patients-table-wrapper -->

        <?php
    }

    /**
     * Return all Patients.
     *
     * @since 0.0.1
     * @return array of patients
     */
    function i4_get_patients() {
        global $wpcwdb, $wpdb;

        $wpdb->show_errors();

        $SQL = "SELECT u.ID, u.user_login, u.display_name, u.user_email FROM wp_users u
                INNER JOIN wp_usermeta m ON m.user_id = u.ID
                WHERE m.meta_key = 'wp_capabilities'
                  AND m.meta_value LIKE '%patient%'
                ORDER BY LOWER(u.display_name)";
        $patients = $wpdb->get_results($SQL, OBJECT_K);

        return $patients;
    }

    /**
     * Generate a New Patient Modal
     *
     * @since 0.0.1
     * @param string ID of the modal we want to generate. Should match the data-reveal-id of the element that we're using to trigger the modal
     */
    function i4_new_patient_modal() {

        $html = '<div id="edit-patient-modal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
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
    function i4_add_new_patient_form() {

        $content = '<div class="form-container">
                      <div id="edit-patient-spinner" class="spinner"></div>
                      <form action="" method="POST" id="edit-patient-form" class="form-horizontal edit-patient-form">
                        <input id="patientId" name="patientId" type="hidden" value=""/>
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
                                <div class="row collapse">
                                    <div class="small-10 columns">
                                        <input type="text" class="patient-fname" id="patient_fname" name="patient_fname" placeholder="First Name" value="" required/>
                                    </div> <!-- end .small-10 columns -->
                                    <div class="small-2 columns">
                                    </div> <!-- end .small-2 columns -->
                                </div> <!-- end .row collapse -->
                          </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        <div class="row">
                            <div class="large-12 columns">
                                <div class="row collapse">
                                    <div class="small-10 columns">
                                        <input type="text" class="patient-lname" id="patient_lname" name="patient_lname" placeholder="Last Name" value="" required/>
                                    </div> <!-- end .small-10 columns -->
                                    <div class="small-2 columns">
                                    </div> <!-- end .small-2 columns -->
                                </div> <!-- end .row collapse -->
                            </div> <!-- end large-12 -->
                        </div> <!-- end row -->
                        <div class="row">
                          <div class="large-12 columns">
                            <input type="hidden" name="action" value="edit-patient"/>
                            <button class="button tiny blue" type="submit" id="edit-patient-submit">Next</button>
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

    function i4_modify_courses_modal() {
        $html = '<div id="modify-courses-modal" class="reveal-modal small" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
                    <div id="modify-courses-spinner" class="spinner"></div>
                    <form action="" method="POST" id="modify-courses-form">
                        <input id="coursesPatientId" type="hidden" name="patientId" value=""/>
                        <h3 id="modifyCoursesTitle">Manage Courses for <i></i></h3>
                        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
                        <div class="courses-table">
                            <div class="courses-row">
                                <div class="courses-header">Available Courses</div>
                                <div class="courses-header">Assigned Courses</div>
                            </div>
                            <div class="courses-row">
                                <ul id="available-courses" class="connectedSortable"></ul>
                                <ul id="user-courses" class="connectedSortable"></ul>
                            </div>
                        </div>
                        <button class="button tiny blue" type="submit" id="update-patient-courses-submit">Done</button>
                    </form>
                  </div>
         ';
        echo $html;
    }

    /**
     * Get user course information
     */
    function i4_get_user_courses() {
        $result = array();
        $patient_id = sanitize_text_field($_GET['patientId']);

        //retrieve the courses
        $all_courses = I4Web_LMS()->i4_wpcw->i4_get_all_courses();
        $user_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient_id);
        $unassigned_courses = array_diff($all_courses, $user_courses);

        $result['assigned_courses'] = $user_courses;
        $result['unassigned_courses'] = $unassigned_courses;

        echo json_encode($result);
        die();
    }

    /**
     * Called when adding a new patient.
     *
     */
    function i4_ajax_add_new_patient() {
        global $current_i4_user;

        $response = array();
        // Security check
        $security_check = check_ajax_referer('add_new_patient_nonce', 'security', false);

        if (!$security_check) {
            die (__('Sorry, we are unable to perform this action. Contact support if you are receiving this in error!', 'i4'));
        }

        //Perform a permissions check just in case
        if (!user_can($current_i4_user, 'manage_patients')) {
            die (__('Sorry but you do not have the proper permissions to perform this action. Contact support if you are receiving this in error', 'i4'));
        }

        $first_name = sanitize_text_field($_POST['patient_fname']);
        $last_name = sanitize_text_field($_POST['patient_lname']);
        $patient_array = array(
            'user_login' => sanitize_text_field($_POST['patient_username']),
            'user_email' => sanitize_text_field($_POST['patient_email']),
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'role' => 'patient'
        );

        $patient_id = I4Web_LMS()->i4_manage_patients->i4_insert_patient($patient_array);

        if (!$patient_id) {
            $response['status'] = 409;
        }
        else {
            $display_name = $first_name . " " . $last_name;
            $response['status'] = 200;
            $response['patient_id'] = $patient_id;
            $response['patient_name'] = $display_name;
        }
        echo json_encode($response);

        die();
    }

    function i4_get_patient_info() {
        $result = array();
        $patient = get_user_by('id', $_GET['patient_id']);

        $result['email'] = $patient->user_email;
        $result['first_name'] = $patient->first_name;
        $result['last_name'] = $patient->last_name;
        $result['user_login'] = $patient->user_login;

        echo json_encode($result);
        die();
    }

    /**
     * Called when updating a patient.
     *
     */
    function i4_update_patient() {
        global $current_i4_user, $wpdb;

        $response = array();

        //Perform a permissions check just in case
        if (!user_can($current_i4_user, 'manage_patients')) {
            die (__('Sorry but you do not have the proper permissions to perform this action. Contact support if you are receiving this in error', 'i4'));
        }

        $patient_id = $_POST['patient_id'];
        $first_name = sanitize_text_field($_POST['patient_fname']);
        $last_name = sanitize_text_field($_POST['patient_lname']);
        $patient_array = array(
            'ID' => $patient_id,
            'user_email' => sanitize_text_field($_POST['patient_email']),
            'first_name'   => $first_name,
            'last_name'    => $last_name
        );

        wp_update_user($patient_array);
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE wp_users SET display_name=%s WHERE id=%d",
                $first_name . " " . $last_name,
                $patient_id
            )
        );

        $response['status'] = 200;
        $response['patient_id'] = $patient_id;
        echo json_encode($response);
        die();
    }

    /**
     * @param $patient_id - The ID of the patient whose courses are being modified
     * @param $new_user_courses - The list of courses that the user is assigned after the modifications in the modal
     */
    function i4_update_patient_courses() {
        $patient_id = sanitize_text_field($_POST['patientId']);
        $new_user_courses = array_flip($_POST['courses']);

        $current_user_courses = I4Web_LMS()->i4_wpcw->i4_get_assigned_courses($patient_id);
        if (count($new_user_courses) > 0) {
            $added_courses = array_diff_key($new_user_courses, $current_user_courses);
            $removed_courses = array_diff_key($current_user_courses, $new_user_courses);

            $this->add_courses($patient_id, $added_courses);
            $this->remove_courses($patient_id, $removed_courses);
        }
        else {
            $this->remove_courses($patient_id, $current_user_courses);
        }

        die();
    }

    function add_courses($patient_id, $courses) {
        global $wpdb;
        $enrollment_date = date('Y-m-d H:i:s');
        foreach ($courses as $id => $course) {
            $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO wp_wpcw_user_courses(user_id, course_id, course_enrolment_date) VALUES(%d, %d, %s)",
                    $patient_id,
                    sanitize_text_field($id),
                    $enrollment_date
                )
            );
        }
    }

    function remove_courses($patient_id, $courses) {
        global $wpdb;
        foreach ($courses as $id => $course) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM wp_wpcw_user_courses WHERE user_id = %d AND course_id = %d",
                    $patient_id,
                    sanitize_text_field($id)
                )
            );
        }
    }

    function i4_insert_patient($patient_array) {
        $patient_id = wp_insert_user($patient_array);

        if (!is_wp_error($patient_id)) {
            //Send off a new user notification to the admin and the new patient
            wp_new_user_notification($patient_id, $deprecated = null, $notify = 'both');

            return $patient_id;
        }
        else {
            return false;
        }
    }

    function i4_remove_patient() {
        global $wpdb;
        // I'd prefer to use DELETE, but JQuery doesn't support it natively
        $patient_id = $_POST['patientId'];
        echo $patient_id;

        // Remove user data from the wp_courseware tables
        $remove_from_tables = array("wp_wpcw_user_progress", "wp_wpcw_user_progress_quizzes", "wp_wpcw_user_courses");
        foreach ($remove_from_tables as $table) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM %s WHERE user_id = %d",
                    $table,
                    $patient_id
                )
            );
        }

        // The WP delete user function handles removing metadata for the user
        wp_delete_user($patient_id);
        die();
    }
}
