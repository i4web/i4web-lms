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
class I4Web_LMS_Course_Docs{
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

        if (isset($_GET['add_course_doc']) && $_GET['add_course_doc'] == 'true') {
            $this->i4_lms_doc_success_msg();
        }
        elseif (isset($_GET['add_course_doc']) && $_GET['add_course_doc'] == 'false') {
            $this->i4_lms_doc_error_msg();
        }

    ?>
        <form action="" method="POST">
            <h3>Add a New Course Document or Form</h3>

            <p>Please enter in the Course Document details below</p>

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">Title</th>
                        <td>
                            <input type="text" name="course_doc_title" value="">
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
                                <input type="text" name="doc_file_url" class="login-logo-url" value=""/>
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
        global $wpdb;

        if (isset($_POST['action']) && $_POST['action'] == 'add_course_doc' && wp_verify_nonce($_POST['new_course_doc_nonce'], 'new-course-doc-nonce')) {

            //Sanitize the Doc title field
            $course_doc_title = sanitize_text_field($_POST['course_doc_title']);

            //Sanitize the intval to only submit integers for the course id
            $doc_course_id = intval($_POST['doc_course_id']);

            if (!$doc_course_id) {
                $doc_course_id = ''; //if the value submitted is not an integer, we blank that out
            }

            $doc_file_url = sanitize_text_field($_POST['doc_file_url']);

            $file_data = array(
              'title'           =>  $course_doc_title,
              'course'          =>  $doc_course_id,
              'url'             =>  $doc_file_url
            );

            //Insert Doc File here
            $file_id =  $this->i4_lms_insert_course_doc($file_data);

            if( $file_id == false ){
                //redirect on error
                $redirect = add_query_arg(array(
                    'add_course_doc'    =>  'false',
                    'title'             =>  $course_doc_title,
                    'course'            =>  $doc_course_id,
                    'url'               =>  $doc_file_url
                ), $_POST['redirect']
                );
            }
            else{
                // redirect on success
                $redirect = add_query_arg(array(
                    'add_course_doc'    =>  'true',
                    'title'           =>  $course_doc_title,
                    'course'          =>  $doc_course_id,
                    'url'             =>  $doc_file_url
                ), $_POST['redirect']
                );
            }

            // redirect back to our previous page with the added query variable
            wp_redirect($redirect);
            exit;

        }
    }

    /**
     * Listens for the edit course doc action and processes the data
     *
     * @since 0.0.1
     */
    function i4_lms_edit_course_doc() {

    }

    /**
     * Inserts Course Doc File into the DB
     *
     * @since 0.0.1
     */
    function i4_lms_insert_course_doc( $file_data ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare(
            "
                INSERT INTO wp_i4_lms_course_docs
                (  course_doc_title, course_id, course_doc_url )
                VALUES ( %s, %d, %s )
            ",
                array(
                $file_data['title'],
                $file_data['course'],
                $file_data['url']
            )
        ) );

        $wpdb->show_errors();

        return $wpdb->insert_id;
    }

    /**
     * Displays all current docs in the admin page
     *
     * @since 0.0.1
     */
    function view_course_docs() {
        echo '<h2>Course Documents</h2>';
    ?>
        <div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
                            I4Web_LMS()->i4_course_docs_list->prepare_items();
							I4Web_LMS()->i4_course_docs_list->display(); ?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
    <?php
    }

    /**
     * Retrieves the Documents based on the Course ID
     *
     * @since 0.0.1
     */
    function i4_lms_get_docs($course_id) {
        global $wpdb;
        $wpdb->show_errors();

        $SQL = $wpdb->get_results('
  		    SELECT * FROM wp_i4_lms_course_docs WHERE course_id = '.$course_id .'
  	    ', OBJECT);

        return $SQL;

    }

    /**
     * Displays the Success message when a Document is successfully added to the DB
     *
     * @since 0.0.1
     */
    function i4_lms_doc_success_msg() {
        $class = "updated";
        $message = "A new Document was successfully added.";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

    /**
     * Displays the Error message when a Coordinator is not successfully added to the DB
     *
     * @since 0.0.1
     */
    function i4_lms_doc_error_msg() {
        $class = "error";
        $message = "An error occurred. Please check your information and try again";
        echo "<div class=\"$class\"> <p>$message</p></div>";
    }

}
