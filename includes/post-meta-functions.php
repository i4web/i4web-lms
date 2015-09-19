<?php
/**
 * Template Functions
 *
 * @package     I4Web_LMS
 * @subpackage  Functions/Post Meta Functions
 * @copyright   Copyright (c) 2015, i-4Web
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the Date of posting
 *
 * @since 1.2
 * @return string
 */

if ( ! function_exists( 'i4_lms_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function i4_lms_posted_on() {

  $time_string = '<time class="announcement-posted-date published" datetime="%1$s">%2$s</time>';

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'i4web' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	return $time_string; // WPCS: XSS OK.

}
endif;
