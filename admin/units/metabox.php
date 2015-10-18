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
 function i4_render_video_meta_box( $post ){

   wp_nonce_field(basename(__FILE__), "video-meta-box-nonce");

   $video_id = get_post_meta( $post->ID, 'video-id', true );
   $video_description = get_post_meta ( $post->ID, 'video-description', true );

   ?>
   <table class="form-table">
     <tr>
       <th style="width:15%"><label for="video-id">Video ID</label> </th>
       <td>
         <input name="video-id" type="text" value="<?php echo esc_attr( $video_id ); ?>">
       </td>
     </tr>
     <tr>
       <th style="width:15%"><label for="video-description">Video Description</label></th>
       <td>
         <textarea name="video-description"><?php echo esc_attr( $video_description ); ?></textarea>
       </td>
     </tr>
   </table>

<?php
  }

/**
 * Save Video Metabox Data
 *
 * @since 0.0.1
 * @return void
 */
 function i4_save_video_meta_box( $post_id, $post, $update ){

   //Check the nonce to prevent CSRF attacks during form submission
   if (!isset($_POST["video-meta-box-nonce"]) || !wp_verify_nonce($_POST["video-meta-box-nonce"], basename(__FILE__)))
      return $post_id;

   //Check if the user has the proper permissions
   if(!current_user_can("edit_post", $post_id))
      return $post_id;

   if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
      return $post_id;

   //Store the slug of the CPT
   $slug = "course_unit";

   //If we are not saving the correct CPT, return.
   if($slug != $post->post_type)
      return $post_id;

   $video_id = "";

   //If our video id field was set, store the value
   if(isset( $_POST["video-id"] )){
      //Sanitize the users input by forcing the video id to be an int. Any non integer values are stripped here.
      $video_id = intval( $_POST["video-id"] );
   }

   //If our video id field was set, store the value
   if(isset( $_POST["video-description"] )){
      //Sanitize the users input by forcing the video id to be an int. Any non integer values are stripped here.
      $video_description = sanitize_text_field( $_POST["video-description"] );
   }

   //Update the Post Meta
   update_post_meta($post_id, "video-id", $video_id);
   update_post_meta($post_id, "video-description", $video_description);

 }

add_action("save_post", "i4_save_video_meta_box", 10, 3);
