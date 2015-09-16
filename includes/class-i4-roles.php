<?php
/**
  * I4Web_LMS roles
  *
  * @package I4Web_LMS
  * @subpackage Classes/Roles
  * @copyright Copyright (c) 2015, i-4Web
  * @since 0.0.1
  */

  // Exit if accessed directly
  if ( ! defined( 'ABSPATH' ) ) exit;

  /**
   * I4Web_LMS_Roles Classes
   * This handles creating roles for the i4Web LMS Plugin
   *
   * @since 0.0.1
   */
  class I4Web_LMS_Roles{
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
     public function __construct(){

     }

     /**
      * Adds the student role
      *
      * This role is reserved for students. We explicitly deny capabilities by using 'false'
      *
      */
      public function i4_add_roles(){
        add_role( 'student', __( 'Student', 'i4'), array(
          'delete_others_pages'               => false,
          'delete_others_posts'               => false,
          'delete_pages'                      => false,
          'delete_posts'                      => false,
          'delete_private_pages'              => false,
          'delete_private_posts'              => false,
          'delete_published_pages'            => false,
          'delete_published_posts'            => false,
          'edit_dashboard'                    => false,
          'edit_others_pages'                 => false,
          'edit_others_posts'                 => false,
          'edit_pages'                        => false,
          'edit_posts'                        => false,
          'edit_private_pages'                => false,
          'edit_private_posts'                => false,
          'edit_published_pages'              => false,
          'edit_published_posts'              => false,
          'edit_theme_options'                => false,
          'manage_categories'                 => false,
          'manage_links'                      => false,
          'manage_options'                    => false,
          'moderate_comments'                 => false,
          'publish_pages'                     => false,
          'publish_posts'                     => false,
          'read'                              => true,
          'read_private_pages'                => false,
          'read_private_posts'                => false,
          'switch_themes'                     => false,
          'upload_files'                      => false
          ));
      }

      /**
       * Adds Capabilities to certain roles
       * @since 1.0.0
       */
       public function i4_add_capabilities(){
         // gets the editor role
         $role = get_role( 'editor' );

         // This only works, because it accesses the class instance.
        // would allow the author to edit others' posts for current theme only
        $role->add_cap( 'create_users' );

        $role->add_cap( 'edit_users' );

        $role->add_cap( 'delete_users' );

        $role->add_cap( 'list_users' );

       }
      /**
       * Remove Roles from the Site
       *
       * @since 1.0.0
       */
       public function i4_remove_roles(){
         remove_role( 'subscriber' );
       }
  }
