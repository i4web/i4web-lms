<?php
/**
 * I4Web_LMS Course Docs Class.
 *
 * @package I4Web_LMS
 * @subpackage Classes/Course Docs
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * I4Web_LMS_Course_Docs Class
 *
 * This handles the Course Docs admin page, and any functions pertaining to Course Docs
 *
 * @since 0.0.1
 */
class I4Web_LMS_Course_Docs {
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
    public function __construct() {
        global $wpdb, $wpcwdb;

        add_action( 'init', array( $this, 'i4_lms_add_course_doc' ));
        add_action( 'init', array( $this, 'i4_lms_edit_course_doc' ));

    }

    /**
     * Display the new Course Doc Form for the Course Docs admin page
     *
     * @since 0.0.1
     */
    function new_course_doc_form() {

        //Retrieve Courses information using a pre-built query for the coordinators admin page
        $courses = I4Web_LMS()->i4_coordinators->i4_lms_get_courses();
    ?>
        <form action="" method="POST">
            <h3>Add a New Course Document or Form</h3>

            <p>Please enter in the Course Document details below</p>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Title</th>
                        <td>
                            <input type="email" name="course_doc_title" value="">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Assign a Course</th>
                        <td>
                            <select name="doc_course_id">
                                <?php foreach ($courses as $course) {
                                    echo '<option value="' . $course->course_id . '">' . $course->course_title . '</option>';
                                } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Upload Document</th>
                        <td>
                            <div class="section-nav-logo section-upload">
                                <input type="text" name="course_doc_file" class="login-logo-url" value=""/>
                                <input id="nav-logo" class="upload-button button button-primary" type="button"
                                       value="Upload Document"/> <br/>
                                <span class='description'>You may upload PDF, .docx, .doc</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="action" value="add_course_doc"/>
            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
            <input type="hidden" name="new_course_doc_nonce"
                   value="<?php echo wp_create_nonce('new-course-doc-nonce'); ?>"/>

            <?php submit_button(); ?>
        </form>
    <?php
    }

    /**
     * Display the Edit Course Doc Form for the Course Docs admin page
     *
     * @since 0.0.1
     */
    function edit_course_doc_form() {

    }

    /**
     * Listens for the add course doc action and processes the data
     *
     * @since 0.0.1
     */
    function i4_lms_add_course_doc() {

    }

    /**
     * Listens for the edit course doc action and processes the data
     *
     * @since 0.0.1
     */
    function i4_lms_edit_course_doc() {

    }

}
