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
	        add_action( 'admin_menu', array( $plugin, 'add_submenu' ) );
	        add_action( 'admin_init', array( $plugin, 'add_submenu_fields' ) );
            add_action( 'pre_get_posts', array($plugin, 'add_year_query') );
	        add_action( 'pre_update_option_' . $plugin->post_type . '-settings-year', array($plugin, 'filter_update_settings_year'), 10, 2 );
            add_filter( 'query_vars', array($plugin, 'register_year_vars') );
            add_filter( 'post_type_link', array($plugin, 'add_year_permalink'), 1, 2 );
            add_action( 'add_meta_boxes_' . $plugin->post_type, array($plugin, 'create_meta_boxes') );
            add_action( 'save_post_' . $plugin->post_type, array($plugin, 'save_date_meta_box') );
            add_action( 'save_post_' . $plugin->post_type, array($plugin, 'save_speakers_meta_box') );
            add_filter( 'manage_' . $plugin->post_type . '_posts_columns', array($plugin, 'add_custom_columns') );
            add_action( 'manage_' . $plugin->post_type . '_posts_custom_column', array($plugin, 'add_custom_column_data'), 10, 2 );
            add_filter( 'manage_edit-' . $plugin->post_type . '_sortable_columns', array($plugin, 'set_sortable_columns') );
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
                'taxonomies'            => array( 'iccs_event_type' ),
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
            $tag_args = array(
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
	            'show_in_nav_menus' => false,
	            'show_tagcloud'     => false,
            );
            register_taxonomy( 'iccs-schedule-tag', $this->post_type, $tag_args);
        }

	    /**
	     * Add submenu page to custom post type
	     * @since 1.0.0
	     * @access  public
	     */
	    public function add_submenu() {
		    add_submenu_page(
			    'edit.php?post_type=' . $this->post_type,
			    __('Schedule Settings', 'iccs-schedule'),
			    __('Settings', 'iccs-schedule'),
			    'manage_options',
			    $this->post_type . '-settings',
			    array($this, 'settings_display'));
	    }

	    /**
	     * Add settings fields
	     * @since 1.0.0
	     * @access  public
	     */
	    public function add_submenu_fields() {
		    add_settings_section(
		    	$this->post_type . '-settings-section',
			    __('All Settings', 'iccs-schedule'),
			    null,
			    $this->post_type . '-settings'
		    );
		    register_setting(
		    	$this->post_type . '-settings-section',
			    $this->post_type . '-settings-year',
			    array(
			    	'type'              => 'integer',
				    'sanitize_callback' => 'intval',
				    'default'           => date('Y')
			    )
		    );
		    add_settings_field(
			    $this->post_type . '-settings-year',
			    __('Current Schedule Year','iccs-schedule'),
			    array($this, 'settings_year_display'),
			    $this->post_type . '-settings',
			    $this->post_type . '-settings-section',
			    array(
			    	'label_for'     => 'iccsSettingsYear'
			    )
		    );
	    }

	    /**
	     * Settings display callback
	     * @since 1.0.0
	     * @access  public
	     */
	    public function settings_display() {
		    include $this->plugin_path . 'views/admin/settings.php';
	    }

	    /**
	     * Settings year field callback
	     * @since 1.0.0
	     * @access  public
	     */
	    public function settings_year_display() {
	    	$name = $this->post_type . '-settings-year';
	    	$value = get_option($this->post_type . '-settings-year');
	    	$options = get_option($this->post_type . '-years');
		    include $this->plugin_path . 'views/admin/settings-year.php';
	    }

	    /**
	     * Filter the year setting before it's updated
	     * Trigger a rewrite flush if updated.
	     * @since 1.0.0
	     * @access  public
	     * @param   string  $new_value  The new value
	     * @param   string  $old_value  The old value
	     * @return  string
	     */
	    public function filter_update_settings_year($new_value, $old_value) {
		    if ( $new_value !== $old_value ) {
			    add_option( 'iccs_flush_rewrite_rules', true );
		    }
			return $new_value;
	    }

        /**
         * Filter admin columns list to add custom fields
         * @since 1.0.0
         * @access  public
         * @param   array   $columns    An array of columns.
         * @return  array
         */
        public function add_custom_columns( $columns ) {
            $date = $columns['date'];
            unset( $columns['date'] );

            $columns['iccs_year'] = __( 'Conference Year', 'iccs-schedule' );
	        $columns['iccs_date'] = __( 'Date', 'iccs-schedule' );

            return $columns;
        }

        /**
         * Add data to the custom columns
         * @since 1.0.0
         * @access  public
         * @param   string  $column_name    The name of the column to display.
         * @param   int     $post_id        The ID of the current post.
         */
        public function add_custom_column_data( $column_name, $post_id ) {
        	$start_date = get_post_meta($post_id, 'iccs_start_date', true);

            if ( $column_name === 'iccs_year' ) {
                $year = Iccs__Schedule__Utils::sanitize_datetime($start_date, 'Y');
                printf('<a href="%s">%s</a>', add_query_arg('iccs_year', $year), $year );
            }
	        if ( $column_name === 'iccs_date' ) {
		        $end_date = get_post_meta($post_id, 'iccs_end_date', true);
		        echo Iccs__Schedule__Utils::sanitize_datetime($start_date, 'm/d/Y') . '<br>';
		        printf('<small>%s - %s</small>',
			        Iccs__Schedule__Utils::sanitize_datetime($start_date, 'g:i a'),
			        Iccs__Schedule__Utils::sanitize_datetime($end_date, 'g:i a')
		        );
	        }
        }

        /**
         * Add data to the custom columns
         * @since 1.0.0
         * @access  public
         * @param   array   $columns    An array of sortable columns.
         * @return  array
         */
        public function set_sortable_columns( $columns ) {
            $columns['iccs_date'] = 'iccs_year';
            return $columns;
        }

        /**
         * Add year archive rewrite rule
         * @since 1.0.0
         * @access  public
         */
        public function register_year_archive() {
        	$current_year = get_option($this->post_type . '-settings-year');
            add_rewrite_rule(
                $this->slug . '/([0-9]{4})/?$',
                'index.php?iccs_year=$matches[1]&post_type=' . $this->post_type,
                'bottom'
            );
            add_rewrite_rule(
                $this->slug . '/?$',
                'index.php?iccs_year=' . $current_year . '&post_type=' . $this->post_type,
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
            if ( $query->get('post_type') !== $this->post_type ) return;
            $year = $query->get('iccs_year');
            $orderby = $query->get( 'orderby');
            if ($year)  {
                $meta_query = $query->get('meta_query', array());
                $meta_query[] = array(
                    'key'       => 'iccs_start_date',
                    'value'     => array($year . '-01-01', $year . '-12-31'),
                    'compare'   => 'BETWEEN',
                    'type'      => 'DATE'
                );
                $query->set( 'meta_query', $meta_query );
            }

            if ( is_admin() && $orderby === 'iccs_year' ) {
                $query->set( 'meta_key', 'iccs_start_date' );
                $query->set( 'meta_type', 'DATETIME' );
                $query->set( 'orderby', 'meta_value_datetime' );
            }
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
            $year = Iccs__Schedule__Utils::sanitize_datetime(get_post_meta($post->ID, 'iccs_start_date', true), 'Y');
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
            $start_date = get_post_meta( $post->ID, 'iccs_start_date', true );
	        $end_date = get_post_meta( $post->ID, 'iccs_end_date', true );

            $values = array(
                'iccs_date'         => Iccs__Schedule__Utils::sanitize_date($start_date),
                'iccs_start_time'   => Iccs__Schedule__Utils::sanitize_time($start_date),
                'iccs_end_time'     => Iccs__Schedule__Utils::sanitize_time($end_date),
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
         * @param   array   $fields     Array of fields
         * @since   1.0.0
         * @access   public
         */
        public function save_meta_box( $post_id, $fields ) {
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
            if ( $parent_id = wp_is_post_revision( $post_id ) ) {
                $post_id = $parent_id;
            }
            foreach ( $fields as $field => $args ) {
	            /**
	             * Save meta box content.
	             *
	             * @param   array               $args['callback']   Sanitize callback function
	             * @param   string|string[]     $args['fields']     Field(s) to combine
	             * @param   string|string[]     $args['separator']  Separator between fields (default ' ')
	             * @since   1.0.0
	             */
	            $args = array_merge( array(
		            'callback'  => function($input){return $input;},
		            'fields'    => '',
		            'separator' => ' '
	            ), $args);
	            $value = '';
	            if ( is_array($args['fields']) ) {
					foreach ($args['fields'] as $input) {
						if ( array_key_exists( $input, $_POST ) ) {
							$value .= ($value !== '') ? $args['separator'] : '';
							$value .= $_POST[$input];
						}
					}
	            } else {
		            if ( array_key_exists( $args['fields'], $_POST ) ) {
		            	$value = $_POST[$args['fields']];
		            }
	            }
                update_post_meta( $post_id, $field, call_user_func($args['callback'], $value) );
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
            $fields = array(
            	'iccs_start_date'   => array(
            		'callback'  => 'Iccs__Schedule__Utils::sanitize_datetime',
		            'fields'    => array('iccs_date', 'iccs_start_time')
	            ),
	            'iccs_end_date'     => array(
		            'callback'  => 'Iccs__Schedule__Utils::sanitize_datetime',
		            'fields'    => array('iccs_date', 'iccs_end_time')
	            )
            );
            $this->save_meta_box($post_id, $fields);
            if ( array_key_exists( 'iccs_date', $_POST ) ) {
	            $this->update_year_options($_POST['iccs_date']);
            }
        }

	    /**
	     * Save speakers meta box content.
	     *
	     * @param   string     $date    The date for the event
	     * @since   1.0.0
	     * @access  private
	     */

        private function update_year_options($date) {
	        $year = Iccs__Schedule__Utils::sanitize_datetime($date, 'Y');
	        $years = get_option($this->post_type . '-years');
	        if ( $year && !in_array($year, $years) ) {
	        	$years[] = $year;
		        sort($years);
	        	update_option($this->post_type . '-years', $years);
	        }
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
            $fields = array(
                'iccs_speakers'     => array(
	                'callback'  => 'sanitize_textarea_field',
	                'fields'    => 'iccs_speakers'
                )
            );
            $this->save_meta_box($post_id, $fields);
        }
    }
}