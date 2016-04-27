<?php
/**
 * I4Web_LMS Custom Login Class.
 *
 * @package I4Web_LMS
 * @subpackage Classes/Custom Login
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * I4Web_LMS_Login Class
 *
 * This handles setting up the custom login page styling
 *
 * @since 0.0.1
 */
class I4Web_LMS_Login {
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
    public function __construct() {
        add_filter('login_headerurl', array($this, 'i4_login_logo_url'));
        add_action('login_enqueue_scripts', array($this, 'i4_login_logo_img'));
        add_action('login_footer', array($this, 'i4_login_footer_text'));
        add_action('login_head', array($this, 'display_login_site_icon'));
        add_action('login_message', array($this, 'i4_change_register_message'));
        add_action('wp_authenticate', array($this, 'i4_email_address_login' ));
        add_filter( 'gettext', array($this, 'remove_lostpassword_text' ));



    }

    /**
     * Filters the default link to WordPress on the Login Page to return the Home URL of the website.
     *
     * @since 0.0.1
     * @see login_headerurl via WordPress
     * @return home_url()
     */
    function i4_login_logo_url() {
        return home_url();
    }

    /**
     * Filters the default link to WordPress on the Login Page to return the Home URL of the website.
     *
     * @since 0.0.1
     * @return $i4_settings (array)
     */
    function retrieve_custom_settings() {

        $i4_settings = (array)get_option('i4-lms-settings');
        return $i4_settings;
    }

    /**
     * Displays the Site Icon on the Login page.
     *
     *
     * @access public
     * @since 0.0.1
     * @see login_head via WordPress Actions
     * @return void
     */
    function display_login_site_icon() {
        echo '<link rel="icon" href="' . esc_url(get_site_icon_url(null, 32)) . '" sizes="32x32" />';

    }


    function i4_login_logo_img() {
        //Retrieve the Branding Settings
        $brand_settings = $this->retrieve_custom_settings();
        $brand_logo = esc_attr($brand_settings['i4-lms-login-logo']);
        $brand_primary = esc_attr($brand_settings['i4-lms-primary-color']);
        $brand_secondary = esc_attr($brand_settings['i4-lms-secondary-color']);
        ?>
        <style type="text/css">
            html,body {
                background-color: <?php echo $brand_secondary; ?>;
            }

            body.login div#login h1 a {
                background-image: url(<?php echo $brand_logo; ?>);
                background-size: 250px 50px;
                background-position: center top;
                background-repeat: no-repeat;
                color: #999;
                height: 50px;
                font-size: 20px;
                font-weight: normal;
                line-height: 1.3em;
                margin: 0 auto 25px;
                padding: 0;
                text-decoration: none;
                width: 250px;
                text-indent: -9999px;
                outline: none;
                overflow: hidden;
                display: block;
            }
            .login form {
                border-radius: 3px;
                margin-top: 40px;
            }
            #login {
                padding: 2% 0 0;
            }
            #reg_passmail {
                padding: 20px 0 10px 0px;
                text-align: center;
                font-style: italic;
                color: #a7a7a7;
            }

            .wp-core-ui .button-group.button-large .button, .wp-core-ui .button.button-large {
                margin-top: 15px;
                width: 100%;
            }

            .wp-core-ui .button-primary {
                background-color: <?php echo $brand_primary; ?>;
                border-color: <?php echo $brand_primary; ?>;
                box-shadow: none;
                text-shadow: none;
            }

            .wp-core-ui .button-primary.focus, .wp-core-ui .button-primary.hover,
            .wp-core-ui .button-primary:focus, .wp-core-ui .button-primary:hover {
                background-color: <?php echo $brand_primary; ?>;
                border-color: <?php echo $brand_primary; ?>;
                box-shadow: none;
            }

            .login #backtoblog a, .login #nav a {
                color: <?php echo $brand_primary; ?>;
            }

            .login #backtoblog a:hover, .login #nav a:hover, .login h1 a:hover {
                color: <?php echo $brand_primary; ?>;
            }

            #backtoblog {
                display: none;
            }

            input[type=checkbox]:focus, input[type=color]:focus, input[type=date]:focus, input[type=datetime-local]:focus, input[type=datetime]:focus, input[type=email]:focus, input[type=month]:focus, input[type=number]:focus, input[type=password]:focus, input[type=radio]:focus, input[type=search]:focus, input[type=tel]:focus, input[type=text]:focus, input[type=time]:focus, input[type=url]:focus, input[type=week]:focus, select:focus, textarea:focus {
                border-color: <?php echo $brand_primary; ?>;
                -webkit-box-shadow: 0 0 0 rgba(124, 53, 32, 0.8);
                box-shadow: 0 0 0 rgba(124, 53, 32, 0.8);
            }

            select {
                width: 100%;
                height: 33px;
                margin-top: 10px;
            }

            .login form .input, .login form input[type=checkbox], .login input[type=text] {
                background: #F6F6F6;
            }

            .login-footer {
                color: <?php echo $brand_primary; ?>;
                height: 100%;
                margin-top: 25px;
                max-height: 50px;
                padding-top: 25px;
                text-align: center;
            }

            .i4lms-tagline-login {
                text-align: center;
                font-size: 15px;
                font-style: italic;
                padding-bottom: 16px;
            }

            .login #login_error {
                border-left: 4px solid <?php echo $brand_primary; ?>;
                background: <?php echo $brand_secondary; ?>;
                color: <?php echo $brand_primary; ?>;
                border-radius: 3px;
            }
            .login #login_error {
                color: <?php echo $brand_primary; ?>;
                border: 0;
                font-size: 20px;
                text-align: center;
                color: #EA0202;
                background: transparent;
                box-shadow: none;
            }
            .login .message {
                border: 0;
                background: transparent;
                box-shadow: none;
                color: <?php echo $brand_primary; ?>;
                font-size: 20px;
                text-align: center;
                margin-bottom: -20px;
            }

            /* Large Screens */
            @media only screen and (min-width: 64.063em) {
                #login{
                    width: 30%;
                }
            }
        </style> <?php
    }

    /**
     * Adds footer text to login page
     *
     * @since 0.0.1
     * @return void
     */
    public function i4_login_footer_text() {
        echo '<div class="login-footer"><p class="i4lms-tagline-login">' . get_bloginfo('description') . '</p><p>&copy; ' . date("Y") . ' ' . get_bloginfo('name') . '. All Rights Reserved.</p></div>';
    }

    /**
     * Customize the Registration text
     *
     * @since 0.0.1
     * @return void
     */
    function i4_change_register_message($message)
    {
        // change messages that contain 'Register'
        if (strpos($message, 'Register') !== FALSE) {
            $newMessage = "Register for your online course below";
            return '<p class="message register" style="text-align:center;">' . $newMessage . '</p>';
        }
        else {
            return $message;
        }
    }

    /**
     * Allows the user to login with their email address
     *
     * @since 0.0.1
     * @return void
     */
    function i4_email_address_login(&$username) {
        $user = get_user_by_email($username);

        if(!empty($user->user_login))
        $username = $user->user_login;
    }

    function remove_lostpassword_text ( $text ) {
     if ($text == 'Username'){
         $text = 'Username or Email';
     }
        return $text;
     }

}
