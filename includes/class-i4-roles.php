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
      * Adds the patient role
      *
      * This role is reserved for patients with limited access. We explicitly deny capabilities by using 'false'
      *
      */
      public function i4_add_roles(){
        global $wp_roles;

        add_role( 'patient', __( 'Patient', 'i4'), array(
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

        add_role( 'coordinator', __( 'Coordinator', 'i4'), array(
          'create_users'                  => true,
          'delete_users'                  => true,
          'edit_users'                    => true,
          'delete_others_pages'               => false,
          'delete_others_posts'               => false,
          'delete_pages'                      => false,
          'delete_posts'                      => false,
          'delete_private_pages'              => false,
          'delete_private_posts'              => false,
          'delete_published_pages'            => false,
          'delete_published_posts'            => false,
          'edit_dashboard'                    => true,
          'edit_others_pages'                 => true,
          'edit_others_posts'                 => true,
          'edit_pages'                        => true,
          'edit_posts'                        => true,
          'edit_private_pages'                => true,
          'edit_private_posts'                => true,
          'edit_published_pages'              => true,
          'edit_published_posts'              => true,
          'edit_theme_options'                => false,
          'manage_categories'                 => false,
          'manage_links'                      => false,
          'manage_options'                    => true,
          'moderate_comments'                 => false,
          'publish_pages'                     => false,
          'publish_posts'                     => false,
          'read'                              => true,
          'read_private_pages'                => false,
          'read_private_posts'                => false,
          'switch_themes'                     => false,
          'upload_files'                      => true
          ));
      }

      /**
       * Adds Capabilities to certain roles
       * @since 1.0.0
       */
       public function i4_add_capabilities(){
         // gets the editor role
         $role = get_role( 'editor' );

         //Add 'manage_patients' capability to the Coordinator and Admin roles
         // This only works, because it accesses the class instance.
         // would allow the author to edit others' posts for current theme only        
         $coordinator_role = get_role( 'coordinator' );
         $coordinator_role->add_cap( 'manage_patients' );

         $admin_role = get_role( 'administrator' );
         $admin_role->add_cap( 'manage_patients' );

       }
      /**
       * Remove Roles from the Site (for devs only)
       *
       * @since 1.0.0
       */
       public function i4_remove_roles(){
         //remove_role( 'student' ); For development purposes only
       }
  }
