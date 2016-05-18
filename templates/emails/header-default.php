<?php
/**
 * Default Email Header
 * Taken from Easy Digital Downloads
 *
 * @author        i-4Web
 * @package    I4Web_LMS
 * @version   1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

//Retrieve the Branding Settings

$email_branding = I4Web_LMS()->i4_custom_login_page->retrieve_custom_settings();
$email_logo = esc_attr($email_branding['i4-lms-login-logo']);
$email_bg = esc_attr($email_branding['i4-lms-secondary-color']);
$email_font_color = esc_attr($email_branding['i4-lms-primary-color']);

$branding_settings = (array)get_option('i4-lms-settings');

$email_logo = esc_attr($email_branding['i4-lms-login-logo']);

// For gmail compatibility, including CSS styles in head/body are stripped out therefore styles need to be inline. These variables contain rules which are added to the template inline. !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
$body = "
	background-color: " . $email_bg . ";
	border: 1px solid #EEE;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
";
$wrapper = "
	width:100%;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 70px 0 70px 0;
";
$template_container = "
	box-shadow:0 0 0 1px #f3f3f3 !important;
	border-radius:3px !important;
	background-color: #ffffff;
	border: 1px solid #e9e9e9;
	border-radius:3px !important;
	padding: 20px;
";
$template_header = "
	color: #00000;
	border-top-left-radius:3px !important;
	border-top-right-radius:3px !important;
	border-bottom: 0;
	font-weight:bold;
	line-height:100%;
	text-align: center;
	vertical-align:middle;
";
$body_content = "
	border-radius:3px !important;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
";
$body_content_inner = "
	color: #000000;
	font-size:14px;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
	line-height:150%;
	text-align:left;
";
$header_content_h1 = "
	color: #000000;
	margin:0;
	padding: 28px 24px;
	display:block;
	font-family: 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif;
	font-size:32px;
	font-weight: 500;
	line-height: 1.2;
";


?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?php echo get_bloginfo('name'); ?></title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="<?php echo $body; ?>">
<div style="<?php echo $wrapper; ?>">
    <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tr>
            <td align="center" valign="top">
                <?php if (!empty($email_logo)) : ?>
                    <div id="template_header_image">
                        <?php echo '<p style="margin-top:0;"><img src="' . esc_url($email_logo) . '" style="width:25%;" alt="' . get_bloginfo('name') . '" /></p>'; ?>
                    </div>
                <?php endif; ?>
                <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_container"
                       style="<?php echo $template_container; ?>">
                    <tr>
                        <td align="center" valign="top">
                            <!-- Header -->
                            <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_header"
                                   style="<?php echo $template_header; ?>" bgcolor="#ffffff">
                                <tr>
                                    <td>
                                        <h1 style="<?php echo $header_content_h1; ?>"><?php echo I4Web_LMS()->i4_emails->get_heading(); ?></h1>
                                    </td>
                                </tr>
                            </table>
                            <!-- End Header -->
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <!-- Body -->
                            <table border="0" cellpadding="0" cellspacing="0" width="520" id="template_body">
                                <tr>
                                    <td valign="top" style="<?php echo $body_content; ?>">
                                        <!-- Content -->
                                        <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                            <tr>
                                                <td valign="top">
                                                    <div style="<?php echo $body_content_inner; ?>">
