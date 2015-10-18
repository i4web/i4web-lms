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

/**
 * Returns the Category of the Post
 *
 * @since 1.2
 * @return string
 */

if ( ! function_exists( 'i4_lms_post_category' ) ) :
/**
 * Returns HTML string with category information for the current post.
 */
function i4_lms_post_category() {

  $categories = get_the_category();

  $i = 1; //counter

  $num_categories = count($categories);

  foreach ( $categories as $category ){

    $category_link = get_category_link( $category->cat_ID ); //store the URL for the category

    if ( $num_categories == $i  ){ //If we are at the end of the category list we don't need a comma displayed after the link
      $category_link_html .= '<a href="' . $category_link . '">'.$category->name . '</a>';
    }
    else
    $category_link_html .= '<a href="'. $category_link . '">' . $category->name . '</a>, ';

    $i++; //increment our counter

  }

  $category_string = '<span class="announcement-posted-date">in ' . $category_link_html . '</span>';


	return $category_string; // WPCS: XSS OK.

}
endif;
