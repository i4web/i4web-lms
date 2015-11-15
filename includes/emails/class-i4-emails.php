<?php
/**
 * i-4Web LMS Emails
 *
 * This class handles all emails sent
 *
 * @package     I4Web_LMS
 * @subpackage  Classes/Emails
 * @since       1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * I4_LMS_EMAILS Class
 *
 * @since 1.0
 */
class I4_LMS_EMAILS {

    /**
     * Holds the from address
     *
     * @since 1.0
     */
    private $from_address;

    /**
     * Holds the from name
     *
     * @since 1.0
     */
    private $from_name;

    /**
     * Holds the email content type
     *
     * @since 1.0
     */
    private $content_type;

    /**
     * Holds the email headers
     *
     * @since 1.0
     */
    private $headers;

    /**
     * Whether to send email in HTML
     *
     * @since 1.0
     */
    private $html = true;

    /**
     * The email template to use
     *
     * @since 1.0
     */
    private $template;

    /**
     * The header text for the email
     *
     * @since  1.0
     */
    private $heading = '';

    /**
     * Class Construct to get started
     *
     * @since 1.0
     */
    public function __construct() {

        if ('none' === $this->get_template()) {
            $this->html = false;
        }

        add_action('i4_lms_email_send_before', array($this, 'send_before'));
        add_action('i4_lms_email_send_after', array($this, 'send_after'));

    }

    /**
     * Set a property
     *
     * @since 1.0
     */
    public function __set($key, $value) {
        $this->$key = $value;
    }

    /**
     * Get the email from name
     *
     * @since 1.0
     */
    public function get_from_name() {
        if (!$this->from_name) {
            $this->from_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        }

        return apply_filters('i4_lms_email_from_name', wp_specialchars_decode($this->from_name), $this);
    }

    /**
     * Get the email from address
     *
     * @since 1.0
     */
    public function get_from_address() {
        if (!$this->from_address) {
            $domain_name = preg_replace('/^www\./', '', $_SERVER['SERVER_NAME']); //retrieve the domain name without www so we can add it to the email header

            $this->from_address = 'no-reply@' . $domain_name;
        }

        return apply_filters('i4_lms_email_from_address', $this->from_address, $this);
    }

    /**
     * Get the email content type
     *
     * @since 1.0
     */
    public function get_content_type() {
        if (!$this->content_type && $this->html) {
            $this->content_type = apply_filters('i4_lms_email_default_content_type', 'text/html', $this);
        }
        else {
            if (!$this->html) {
                $this->content_type = 'text/plain';
            }
        }

        return apply_filters('i4_lms_email_content_type', $this->content_type, $this);
    }

    /**
     * Get the email headers
     *
     * @since 1.0
     */
    public function get_headers() {
        if (!$this->headers) {
            $this->headers = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
            $this->headers .= "Reply-To: {$this->get_from_address()}\r\n";
            $this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
        }

        return apply_filters('i4_lms_email_headers', $this->headers, $this);
    }

    /**
     * Retrieve email templates
     *
     * @since 1.0
     */
    public function get_templates() {
        $templates = array(
            'default' => __('Default Template', 'i4web'),
            'none' => __('No template, plain text only', 'i4web')
        );

        return apply_filters('i4_lms_email_templates', $templates);
    }

    /**
     * Get the enabled email template
     *
     * @since 1.0
     */
    public function get_template() {
        if (!$this->template) {
            $this->template = 'default';
        }

        return apply_filters('i4_lms_email_template', $this->template);
    }

    /**
     * Get the header text for the email
     *
     * @since 1.0
     */
    public function get_heading() {
        return apply_filters('i4_lms_email_heading', $this->heading);
    }

    /**
     * Build the final email
     *
     * @since 2.1
     */
    public function build_email($message) {

        if (false === $this->html) {
            return apply_filters('i4_lms_email_message', wp_strip_all_tags($message), $this);
        }

        $message = $this->text_to_html($message);

        ob_start();

        i4_lms_get_template_part('emails/header', $this->get_template(), true);

        do_action('i4_lms_email_header', $this);

        if (has_action('i4_lms_email_template_' . $this->get_template())) {
            do_action('i4_lms_email_template_' . $this->get_template());
        }
        else {
            i4_lms_get_template_part('emails/body', $this->get_template(), true);
        }

        do_action('i4_lms_email_body', $this);

        i4_lms_get_template_part('emails/footer', $this->get_template(), true);

        do_action('i4_lms_email_footer', $this);

        $body = ob_get_clean();
        $message = str_replace('{email}', $message, $body);

        return apply_filters('i4_lms_email_message', $message, $this);
    }

    /**
     * Send the email
     * @param  string $to The To address to send to.
     * @param  string $subject The subject line of the email to send.
     * @param  string $message The body of the email to send.
     * @param  string|array $attachments Attachments to the email in a format supported by wp_mail()
     * @since 2.1
     */
    public function send($to, $subject, $message, $attachments = '') {

        /*	if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
                _doing_it_wrong( __FUNCTION__, __( 'You cannot send email with I4_LMS_EMAILS until init/admin_init has been reached', 'i4web' ), null );
                return false;
            } */

        do_action('i4_lms_email_send_before', $this);

        //	$subject = $this->parse_tags( $subject );
        //	$message = $this->parse_tags( $message );

        $message = $this->build_email($message);

        //	$attachments = apply_filters( 'i4_lms_email_attachments', $attachments, $this );

        $sent = wp_mail($to, $subject, $message, $this->get_headers(), $attachments);

        do_action('i4_lms_email_send_after', $this);

        return $sent;

    }

    /**
     * Add filters / actions before the email is sent
     *
     * @since 2.1
     */
    public function send_before() {
        add_filter('wp_mail_from', array($this, 'get_from_address'));
        add_filter('wp_mail_from_name', array($this, 'get_from_name'));
        add_filter('wp_mail_content_type', array($this, 'get_content_type'));
    }

    /**
     * Remove filters / actions after the email is sent
     *
     * @since 2.1
     */
    public function send_after() {
        remove_filter('wp_mail_from', array($this, 'get_from_address'));
        remove_filter('wp_mail_from_name', array($this, 'get_from_name'));
        remove_filter('wp_mail_content_type', array($this, 'get_content_type'));

        // Reset heading to an empty string
        $this->heading = '';
    }

    /**
     * Converts text to formatted HTML. This is primarily for turning line breaks into <p> and <br/> tags.
     *
     * @since 2.1
     */
    public function text_to_html($message) {

        if ('text/html' == $this->content_type || true === $this->html) {
            $message = wpautop($message);
        }

        return $message;
    }

}
