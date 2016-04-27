<?php
/**
 * I4Web_LMS Course Docs List Table Class.
 * Based off tutorial from http://www.sitepoint.com/using-wp_list_table-to-create-wordpress-admin-tables/
 *
 * @package I4Web_LMS
 * @subpackage Classes/Course Docs List Table
 * @copyright Copyright (c) 2015, i-4Web
 * @since 0.0.1
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

//Load WP List Table class
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * I4Web_LMS_Course_Docs_List Class
 *
 * This handles the Course Docs List Table admin page, and any functions pertaining to the listing of the docs
 *
 * @since 0.0.1
 */
class I4Web_LMS_Course_Docs_List extends WP_List_Table{
    /**
     * Class Construct to get started
     *
     * @since 0.0.1
     */
    public function __construct() {

    }

    /**
     * Get all course docs from the db
     *
     * @since 0.0.1
     */
    function i4_lms_get_all_docs( $per_page = 5, $page_number = 1 ) {
         global $wpdb;
         $sql = "SELECT * FROM {$wpdb->prefix}i4_lms_course_docs";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
        else{
            $sql .= ' ORDER BY course_doc_title ASC';
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Delete a course doc from the db
     *
     * @since 0.0.1
     */
    function i4_lms_delete_doc( $id ) {
         global $wpdb;
         $wpdb->delete(
            "{$wpdb->prefix}i4_lms_course_docs",
            [ 'id' => $id ],
            [ '%d' ]
          );
    }

    /**
     * Return course docs record count from the db
     *
     * @since 0.0.1
     */
    function i4_lms_course_docs_record_count( ) {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}i4_lms_course_docs";

        return $wpdb->get_var( $sql );
    }

    /**
     * Text displayed when no course docs data is available
     *
     * @since 0.0.1
     */
    public function no_items() {
      _e( 'No Course Documents Avaliable.', 'i4' );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     * @return string
     * @since 0.0.1
     */
    function column_name( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'i4_delete_course_doc' );

        $title = '<strong>' . $item['course_doc_title'] . '</strong>';

        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&course_doc=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['ID'] ), $delete_nonce )
        ];

        return $title . $this->row_actions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     * @since 0.0.1
     */
    public function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'course_doc_title':
                return $item['course_doc_title'];
            case 'course_doc_id':
                //Store the course id for the doc and get it's title using i4_get_course_title
                $current_doc_course_id = $item['course_id'];
                $current_doc_course_name = I4Web_LMS()->i4_coordinators->i4_get_course_title( $current_doc_course_id );
                //return $item['course_id']; for debugging
                return $current_doc_course_name;
            case 'course_doc_url':
                //display the url for the course document
                return '<a target="_blank" href="'. $item[ $column_name ]. '">View</a>';
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     * @since 0.0.1
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     * @since 0.0.1
     */
    function get_columns() {

      $columns = [
        'cb'      => '<input type="checkbox" />',
        'course_doc_title'    => __( 'Doc Title', 'i4' ),
        'course_doc_id' => __( 'Course', 'i4' ),
        'course_doc_url'    => __( 'Course Doc URL', 'i4' )
      ];

      return $columns;
    }

    /**
     *  Define which columns are hidden
     *
     * @return array
     * @since 0.0.1
     */
    function get_hidden_columns() {

      return array();
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     * @since 0.0.1
     */
    public function get_sortable_columns() {

      $sortable_columns = array(
        'course_doc_title' => array( 'course_doc_title', true ),
        'course_doc_id' => array( 'course_id', false )
      );

      return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     * @since 0.0.1
     */
    public function get_bulk_actions() {
      $actions = [
        'bulk-delete' => 'Delete'
      ];

      return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     * @since 0.0.1
     */
    public function prepare_items() {

        $this->screen = get_current_screen(); // Added Since 4.4 update requires the screen object now
        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'docs_per_page', 10 );
        $current_page = $this->get_pagenum();
        $total_items  = self::i4_lms_course_docs_record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );


        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $data = $this->i4_lms_get_all_docs($per_page, $current_page);

        //usort( $data, array( &$this, 'sort_data' ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;


    }

    /**
     * Handles deleting course docs records either when the delete link is clicked or when a
     * group of records is checked and the delete option is selected from the bulk action.
     *
     * @since 0.0.1
     */
    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, 'i4_delete_course_doc' ) ) {
              die( 'No. No. No!' );
            }
            else {
              self::i4_lms_delete_doc( absint( $_GET['course_doc'] ) );

              wp_redirect( esc_url( add_query_arg() ) );
              exit;
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
           || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-delete'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
              self::i4_lms_delete_doc( $id );

            }

            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }







} //end I4Web_LMS_Course_Docs_List class
