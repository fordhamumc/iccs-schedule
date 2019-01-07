<?php
/**
 * The admin-specific functionality
 * @since      1.0.0
 *
 * @package    Iccs_Schedule
 * @subpackage Iccs_Schedule/admin
 * @author     Michael Foley <mifoley@fordham.edu>
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Iccs__Schedule__Admin' ) ) {
    class Iccs__Schedule__Admin {

        /**
         * The url slug for the custom post type
         * @since   1.0.0
         * @access  private
         * @var     string  $slug           The url slug for the custom post type
         */
        private $slug = 'schedule';

        /**
         * The name of the schedule post type
         * @since   1.0.0
         * @access  private
         * @var     string  $post_type       The name of the schedule post type
         */
        private $post_type = 'iccs-schedule';

        /**
         * The category taxonomy
         * @since   1.0.0
         * @access  private
         * @var     string  $category       The category taxonomy
         */
        private $category = 'iccs-event-year';

        /**
         * The main file of the plugin
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $plugin_file    The main file of the plugin
         */
        protected $plugin_file;

        /**
         * The directory of the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $plugin_dir    The directory of the plugin.
         */
        protected $plugin_path;

        /**
         * Initialize the class and set its properties.
         *
         * @since   1.0.0
         * @access   public
         * @param   string  $plugin_file    The main file of the plugin
         */
        public function __construct($plugin_file) {
            $this->plugin_file = $plugin_file;
            $this->plugin_path = plugin_dir_path($plugin_file);
        }

        /**
         * Register the plugin with Wordpress
         *
         * @since    1.0.0
         * @access   public
         * @param   string  $plugin_file    The main file of the plugin
         */
        public static function register($plugin_file) {
            $plugin = new self($plugin_file);

            add_action( 'init', array($plugin, 'create_post_type'), 2 );
            add_action( 'init', array($plugin, 'register_taxonomies') );
            add_action( 'init', array($plugin, 'register_year_archive') );
            add_action( 'pre_get_posts', array($plugin, 'add_year_query') );
            add_action( 'add_meta_boxes_' . $plugin->post_type, array($plugin, 'create_meta_boxes') );
            add_action( 'save_post_' . $plugin->post_type, array($plugin, 'save_date_meta_box') );
            add_action( 'save_post_' . $plugin->post_type, array($plugin, 'save_speakers_meta_box') );
            add_filter( 'query_vars', array($plugin, 'register_year_vars') );
            add_filter( 'post_type_link', array($plugin, 'add_year_permalink'), 1, 2 );
        }

        /**
         * Register the custom post type
         * @since   1.0.0
         * @access   public
         */
        public function create_post_type() {
            $labels = array(
                'name'                  => _x( 'Schedule', 'Post Type General Name', 'iccs-schedule' ),
                'singular_name'         => _x( 'Event', 'Post Type Singular Name', 'iccs-schedule' ),
                'menu_name'             => __( 'Schedule', 'iccs-schedule' ),
                'name_admin_bar'        => __( 'Schedule', 'iccs-schedule' ),
                'archives'              => __( 'Event Archives', 'iccs-schedule' ),
                'attributes'            => __( 'Event Attributes', 'iccs-schedule' ),
                'parent_item_colon'     => __( 'Parent Event:', 'iccs-schedule' ),
                'all_items'             => __( 'All Events', 'iccs-schedule' ),
                'add_new_item'          => __( 'Add New Event', 'iccs-schedule' ),
                'add_new'               => __( 'Add New', 'iccs-schedule' ),
                'new_item'              => __( 'New Event', 'iccs-schedule' ),
                'edit_item'             => __( 'Edit Event', 'iccs-schedule' ),
                'update_item'           => __( 'Update Event', 'iccs-schedule' ),
                'view_item'             => __( 'View Event', 'iccs-schedule' ),
                'view_items'            => __( 'View Events', 'iccs-schedule' ),
                'search_items'          => __( 'Search Event', 'iccs-schedule' ),
                'not_found'             => __( 'Not found', 'iccs-schedule' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'iccs-schedule' ),
                'featured_image'        => __( 'Featured Image', 'iccs-schedule' ),
                'set_featured_image'    => __( 'Set featured image', 'iccs-schedule' ),
                'remove_featured_image' => __( 'Remove featured image', 'iccs-schedule' ),
                'use_featured_image'    => __( 'Use as featured image', 'iccs-schedule' ),
                'insert_into_item'      => __( 'Insert into event', 'iccs-schedule' ),
                'uploaded_to_this_item' => __( 'Uploaded to this event', 'iccs-schedule' ),
                'items_list'            => __( 'Schedule', 'iccs-schedule' ),
                'items_list_navigation' => __( 'Schedule navigation', 'iccs-schedule' ),
                'filter_items_list'     => __( 'Filter Schedule', 'iccs-schedule' ),
            );
            $rewrite = array(
                'slug'                  => $this->slug . '/%iccs_year%',
                'pages'                 => false,
                'feeds'                 => true,
            );
            $args = array(
                'label'                 => __( 'Event', 'iccs-schedule' ),
                'description'           => __( 'Schedule of Events', 'iccs-schedule' ),
                'labels'                => $labels,
                'supports'              => array( 'title' ),
                'taxonomies'            => array( 'iccs_event_type', $this->category ),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'menu_position'         => 20,
                'menu_icon'             => 'dashicons-calendar-alt',
                'show_in_admin_bar'     => false,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => 'schedule',
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'rewrite'               => $rewrite,
                'capability_type'       => 'post',
                'show_in_rest'          => true,
            );
            register_post_type( $this->post_type, $args );
            add_rewrite_tag( '%iccs_year%', '([0-9]{4})', 'iccs_year');
        }

        /**
         * Register the taxonomies
         *
         * @since   1.0.0
         * @access   public
         */
        function register_taxonomies() {
            $fyear_labels = array(
                'name'              => _x('Years', 'taxonomy general name', 'iccs-schedule'),
                'singular_name'     => _x('Year', 'taxonomy singular name', 'iccs-schedule'),
                'all_items'         => __('All Years', 'iccs-schedule'),
                'edit_item'         => __('Edit Year', 'iccs-schedule'),
                'view_item'         => __('View Year', 'iccs-schedule'),
                'update_item'       => __('Update Year', 'iccs-schedule'),
                'add_new_item'      => __('Add New Year', 'iccs-schedule'),
                'new_item_name'     => __('New Year', 'iccs-schedule'),
                'search_items'      => __('Search Years', 'iccs-schedule'),
                'popular_items'     => __('Popular Years', 'iccs-schedule'),
                'not_found'         => __('No years found', 'iccs-schedule'),
                'back_to_items'     => __('Back to years', 'iccs-schedule'),
                'separate_items_with_commas'    => __('Separate years with commas', 'iccs-schedule'),
                'add_or_remove_items'           => __('Add or remove years', 'iccs-schedule'),
                'choose_from_most_used'         => __('Choose from the most used years', 'iccs-schedule')
            );
            $fyear_args = array(
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'labels'            => $fyear_labels,
                'rewrite'           => array(
                    'slug'              => $this->slug,
                    'ep_mask'           => EP_PERMALINK
                )
            );
            //register_taxonomy( $this->category, $this->post_type, $year_args);
        }

        /**
         * Add year archive rewrite rule
         * @since 1.0.0
         * @access   public
         */
        public function register_year_archive() {
            add_rewrite_rule(
                $this->slug . '/([0-9]{4})/?$',
                'index.php?iccs_year=$matches[1]&post_type=' . $this->post_type,
                'bottom'
            );
            add_rewrite_rule(
                $this->slug . '/?$',
                'index.php?iccs_year=2019&post_type=' . $this->post_type,
                'bottom'
            );
        }

        /**
         * Filter query vars to add year
         * @since 1.0.0
         * @access  public
         * @param   array   $vars   The query vars array
         * @return  array
         */
        public function register_year_vars( $vars ) {
            $vars[] = 'iccs_year';
            return $vars;
        }

        /**
         * Add year meta search to query
         * @since 1.0.0
         * @access  public
         * @param   WP_Query    $query  The query object
         */
        public function add_year_query( $query ) {
            $year = $query->get('iccs_year');
            if (!$year) return;
            $meta_query = $query->get('meta_query', array());
            $meta_query[] = array(
                'key'       => 'iccs_date',
                'value'     => array($year . '-01-01', $year . '-12-31'),
                'compare'   => 'BETWEEN',
                'type'      => 'DATE'
            );
            $query->set( 'meta_query', $meta_query );
        }

        /**
         * Filter the post permalink to add the year
         * @since 1.0.0
         * @access  public
         * @param   string  $post_link      The post's permalink.
         * @param   WP_Post $post           The post in question.
         * @return  string
         */
        public function add_year_permalink($post_link, $post) {
            $year = Iccs__Schedule__Utils::sanitize_datetime(get_post_meta($post->ID, 'iccs_date', true), 'Y');
            if ( $year ){
                return str_replace( '%iccs_year%' , $year , $post_link );
            }
            return $post_link;
        }

        /**
         * Register the meta boxes
         *
         * @since   1.0.0
         * @access   public
         */
        public function create_meta_boxes() {
            add_meta_box(
                'iccs_schedule_date',
                __( 'Date', 'iccs-schedule' ),
                array($this, 'date_display'),
                $this->post_type,
                'normal',
                'high'
            );
            add_meta_box(
                'iccs_schedule_speakers',
                __( 'Speakers', 'iccs-schedule' ),
                array($this, 'speakers_display'),
                $this->post_type,
                'normal',
                'default'
            );
        }

        /**
         * Date fields display callback
         *
         * @param   WP_Post $post   The post object
         * @since   1.0.0
         * @access   public
         */
        public function date_display( $post ) {
            wp_nonce_field( 'iccs_date_nonce', 'iccs_date_nonce' );
            $values = array(
                'iccs_date'         => get_post_meta( $post->ID, 'iccs_date', true ),
                'iccs_start_time'   => get_post_meta( $post->ID, 'iccs_start_time', true ),
                'iccs_end_time'     => get_post_meta( $post->ID, 'iccs_end_time', true ),
            );
            include $this->plugin_path . 'views/admin/date-box.php';
        }

        /**
         * Speakers display callback
         *
         * @param   WP_Post $post   The post object
         * @since   1.0.0
         * @access   public
         */
        public function speakers_display( $post ) {
            wp_nonce_field( 'iccs_speakers_nonce', 'iccs_speakers_nonce' );
            $speakers = get_post_meta( $post->ID, 'iccs_speakers', true );
            include $this->plugin_path . 'views/admin/speakers-box.php';
        }

        /**
         * Save meta box content.
         *
         * @param   int     $post_id    Post ID
         * @param   array   $fields     Associative array of fields (key) and sanitize callback function (value)
         * @since   1.0.0
         * @access   public
         */
        public function save_meta_box( $post_id, $fields ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
            if ( $parent_id = wp_is_post_revision( $post_id ) ) {
                $post_id = $parent_id;
            }
            foreach ( $fields as $field => $callback ) {
                if ( array_key_exists( $field, $_POST ) ) {
                    update_post_meta( $post_id, $field, call_user_func($callback, $_POST[$field]) );
                }
            }
        }

        /**
         * Save date meta box content.
         *
         * @param   int     $post_id    Post ID
         * @since   1.0.0
         * @access   public
         */
        public function save_date_meta_box( $post_id ) {
            if ( !isset( $_POST['iccs_date_nonce'] ) ||
                 !wp_verify_nonce( $_POST['iccs_date_nonce'], 'iccs_date_nonce' ) )
                return;
            $fields = [
                'iccs_date'         => 'Iccs__Schedule__Utils::sanitize_date',
                'iccs_start_time'   => 'Iccs__Schedule__Utils::sanitize_time',
                'iccs_end_time'     => 'Iccs__Schedule__Utils::sanitize_time'
            ];
            $this->save_meta_box($post_id, $fields);
        }

        /**
         * Save speakers meta box content.
         *
         * @param   int     $post_id    Post ID
         * @since   1.0.0
         * @access   public
         */
        public function save_speakers_meta_box( $post_id ) {
            if ( !isset( $_POST['iccs_speakers_nonce']) ||
                 !wp_verify_nonce( $_POST['iccs_speakers_nonce'], 'iccs_speakers_nonce' ) )
                return;
            $fields = [
                'iccs_speakers'     => 'sanitize_textarea_field'
            ];
            $this->save_meta_box($post_id, $fields);
        }
    }
}