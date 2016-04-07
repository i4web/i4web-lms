<?php
/**
 * Plugin Name: i-4Web LMS
 * Plugin URI: http://www.i-4web.com
 * Description: i-4Web LMS provides an Online Education System for our clients.
 * Author: i-4Web
 * Author URI: http://www.i-4web.com
 * Version: 0.0.1
 * Text Domain: i4web
 * Domain Path: N/A
 *
 * i-4Web LMS is not meant for public distribution and is built to be compatible with WPCourseware
 * This plugin will use the singleton pattern
 *
 *
 *
 * @package I4Web_LMS
 * @category Core
 * @author Jonathan Rivera
 * @version 0.0.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('I4Web_LMS')) :

    /**
     * Main I4Web_LMS class_exists
     *
     * @since 1.0.0
     */
    final class I4Web_LMS {

        /**
         * @var I4Web_LMS. The one and only.
         * @since 1.0.0
         */
        private static $instance;

        /**
         * I4Web_LMS Roles Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_roles;

        /**
         * I4Web_LMS Login Restrict Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_force_login;

        /**
         * I4Web_LMS Custom Login Page Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_custom_login_page;

        /**
         * I4Web_LMS Admin Menu Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_admin_menu;

        /**
         * I4Web_LMS Database Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_db;

        /**
         * I4Web_LMS Emails Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_emails;

        /**
         * I4Web_LMS WPCW Shortcodes Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_wpcw;

        /**
         * I4Web_LMS WPCW Front End Unit Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_wpcw_front_end_unit;

        /**
         * I4Web_LMS Coordinators Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_coordinators;

        /**
         * I4Web_LMS_Course_Docs Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_course_docs;

        /**
         * I4Web_LMS Profile Form Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_profile;

        /**
         * I4Web_LMS Vimeo API Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_vimeo;

        /**
         * I4Web_LMS Manage Patients Object
         *
         * @var object
         * @since 1.0.0
         */
        public $i4_manage_patients;


        /**
         * Main I4Web_LMS instance
         *
         * Uses the singleton OOP approach to ensure that only one instance I4Web_LMS exists in memory at any one time.
         * this prevents the need to define a lot of Globals
         *
         * @since 0.0.1
         * @static
         * @static var array $instance
         * @uses I4Web_LMS::includes() Include the required files for the I4Web_LMS plugin
         * @see  I4Web_LMS()
         * @return The one and only I4Web_LMS
         */
        public static function instance() {

            if (!isset(self::$instance) && !(self::$instance instanceof I4Web_LMS)) {
                self::$instance = new I4Web_LMS;
                self::$instance->i4_constants();
                self::$instance->includes();
                self::$instance->i4_roles = new I4Web_LMS_Roles();
                self::$instance->i4_custom_login_page = new I4Web_LMS_Login();
                self::$instance->i4_force_login = new I4Web_LMS_Force_Login();
                self::$instance->i4_admin_menu = new I4Web_LMS_Admin_Menu();
                self::$instance->i4_coordinators = new I4Web_LMS_Coordinators();
                self::$instance->i4_course_docs = new I4Web_LMS_Course_Docs();
                self::$instance->i4_db = new I4Web_LMS_DB();
                self::$instance->i4_emails = new I4_LMS_EMAILS();
                self::$instance->i4_profile = new I4Web_LMS_Profile_Form();
                self::$instance->i4_wpcw = new I4_LMS_WPCW();
                self::$instance->i4_wpcw_front_end_unit = new I4Web_LMS_Front_End_Unit($post);
                self::$instance->i4_vimeo = new I4Web_LMS_Vimeo();
                self::$instance->i4_manage_patients = new I4Web_LMS_Manage_Patients();

            }
            return self::$instance;
        } // end instance()

        /**
         * Throw and error when the object is cloned
         *
         * Since we're using the singleton design pattern there should only be one single object...So no objects are to be cloned
         *
         * @since 0.0.1
         * @access protected
         * @return void
         */
        public function __clone() {
            //Cloned instances of the Candidate_Space class is not allowed
            _doing_it_wrong(__FUNCTION__, __('Word? That aint allowed son!', 'i4'), '0.0.1');
        }

        /**
         * Disable unserializing of the class
         *
         * @since 0.0.1
         * @access protected
         * @return void
         */
        public function __wakeup() {
            //Unserializing instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Word? That aint allowed son!', 'i4'), '0.0.1');
        }

        /**
         * Setup the I4Web_LMS plugin constants
         *
         * @access private
         * @since 0.0.1
         * @return void
         */
        private function i4_constants() {
            //Plugin Version
            if (!defined('I4_VERSION')) {
                define('I4_VERSION', '0.0.1');
            }

            //Path to the i4Web LMS Plugin
            if (!defined('I4_PLUGIN_DIR')) {
                define('I4_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }

            //Plugin Folder URL
            if (!defined('I4_PLUGIN_URL')) {
                define('I4_PLUGIN_URL', plugin_dir_url(__FILE__));
            }

            //Plugin Root File
            if (!defined('I4_PLUGIN_FILE')) {
                define('I4_PLUGIN_FILE', __FILE__);
            }

            // WPCourseware Plugin Folder URL
            if (!defined('I4_WPCW_PLUGIN_URL')) {
                define('I4_WPCW_PLUGIN_URL', plugins_url('wp-courseware', ''));
            }

            //Make sure that CAL_GREGORIAN is defined
            if (!defined('CAL_GREGORIAN')) {
                define('CAL_GREGORIAN', 1);
            }

        } //end i4_constants

        /**
         *
         * @access private
         * @since 0.0.1
         * @return void
         */
        private function includes() {
            require_once I4_PLUGIN_DIR . 'includes/install.php';

            //Class Files
            require_once I4_PLUGIN_DIR . 'includes/class-i4-roles.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-db.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-custom-login.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-force-login.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-admin-menu.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-coordinators.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-course-docs.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-announcements-widget.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-manage-patients.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-profile-form.php';
            require_once I4_PLUGIN_DIR . 'includes/emails/class-i4-emails.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-vimeo.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-wpcw.php';
            require_once I4_PLUGIN_DIR . 'includes/class-i4-wpcw-front-end-unit.php';
            require_once I4_PLUGIN_DIR . 'includes/widgets.php';

            //Front End Scripts
            require_once I4_PLUGIN_DIR . 'includes/front-end-scripts.php';

            //Admin Scripts
            require_once I4_PLUGIN_DIR . 'admin/admin-scripts.php';

            //Admin Files
            require_once I4_PLUGIN_DIR . 'admin/admin-theme.php';
            require_once I4_PLUGIN_DIR . 'admin/units/metabox.php';

            //Misc Functions
            require_once I4_PLUGIN_DIR . 'includes/functions.php';

            //Front End Ajax functions
            require_once I4_PLUGIN_DIR . 'includes/ajax/ajax-front-end.php';

            //Template Functions
            require_once I4_PLUGIN_DIR . 'includes/template-functions.php';

            //Emails
            require_once I4_PLUGIN_DIR . 'includes/emails.php';

            //Post Meta Functions
            require_once I4_PLUGIN_DIR . 'includes/post-meta-functions.php';

            //Vimeo API Library
            require_once I4_PLUGIN_DIR . 'lib/vimeo.php-1.2.3/autoload.php';


        } //end includes

    } //end Main I4Web_LMS class

endif; // End if class_exists check

/**
 * Responsible for returning the one and only I4Web_LMS instance to our functions
 *
 * This function gets used like a Global variable, except you don't have to declare the Global
 * Example: <?php $i4 = I4Web_LMS(); ?>
 *
 * @since 0.0.1
 * @return object The one and only I4Web_LMS instance
 */
function I4Web_LMS() {
    return I4Web_LMS::instance();
}

//Start I4Web_LMS...  Previously was just I4Web_LMS(); but we added a priority so our plugin will load after the WPCourseware has loaded
// add_action('plugins_loaded', 'I4Web_LMS', 11);

I4Web_LMS();
