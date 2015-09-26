<?php
/**
* Metabox functions for the Course Units CPT
*
* @package I4_Web_LMS
* @subpackage Admin/Units
* @copyright Copyright (c) 2015, i-4Web
* @since 0.0.1
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Metaboxes for the Unit Courses
 *
 * @since 0.0.1
 * @return void
 */
 function i4_add_unit_meta_box(){
   add_meta_box("video-meta-box", __( 'Video Details', 'i4web' ), "i4_render_video_meta_box", "course_unit", "normal", "high" );

 }

 add_action( 'add_meta_boxes', 'i4_add_unit_meta_box' );

/**
 * Video Metabox
 *
 * @since 0.0.1
 * @return void
 */
 function i4_render_video_meta_box(){

   wp_nonce_field(basename(__FILE__), "video-meta-box-nonce"); ?>

   <div>
   <label for="meta-box-text">Video URL</label>
   <input name="meta-box-text" type="text" value="<?php echo get_post_meta($object->ID, "meta-box-text", true); ?>">

   </div>

<?php
  }

/**
 * Save Video Metabox Data
 *
 * @since 0.0.1
 * @return void
 */
 function i4_save_video_meta_box( $post_id, $post, $update ){

   if (!isset($_POST["video-meta-box-nonce"]) || !wp_verify_nonce($_POST["video-meta-box-nonce"], basename(__FILE__)))
      return $post_id;

   if(!current_user_can("edit_post", $post_id))
      return $post_id;

   if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
      return $post_id;

   $slug = "course_unit";

   if($slug != $post->post_type)
      return $post_id;

   $meta_box_text_value = "";
   $meta_box_dropdown_value = "";
   $meta_box_checkbox_value = "";

   if(isset( $_POST["meta-box-text"] )){
      $meta_box_text_value = $_POST["meta-box-text"];
   }

  update_post_meta($post_id, "meta-box-text", $meta_box_text_value);

 }

add_action("save_post", "i4_save_video_meta_box", 10, 3);
