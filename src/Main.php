<?php
/**
 * Initializer
 * @since   1.0.0
 *
 * @package    Iccs_Schedule
 * @author     Michael Foley <mifoley@fordham.edu>
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Iccs__Schedule__Main' ) ) {
    class Iccs__Schedule__Main {

        /**
         * The unique identifier of this plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $plugin_name    The string used to uniquely identify this plugin.
         */
        protected $plugin_name = 'iccs-schedule';

        /**
         * The current version of the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string    $version    The current version of the plugin.
         */
        protected $version = '1.0.0';

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
         * @param    string  $plugin_file    The main file of the plugin
         */
        public static function register($plugin_file) {
            $plugin = new self($plugin_file);
            $plugin->load_dependancies();

            add_action( 'init', array($plugin, 'flush_rewrite'), 50 );
            add_action( 'enqueue_block_assets', array($plugin, 'block_assets') );
            add_action( 'enqueue_block_editor_assets', array($plugin, 'editor_assets') );
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         * - Iccs__Schedule__Admin. Defines all hooks for the admin area.
         *
         * @since    1.0.0
         * @access   private
         */
        private function load_dependancies() {
            require_once $this->plugin_path . 'src/Utils.php';
            require_once $this->plugin_path . 'src/Admin.php';
            if (class_exists( 'Iccs__Schedule__Admin' )) {
                Iccs__Schedule__Admin::register($this->plugin_file);
            }
        }

        /**
         * Flush rewrite rules if needed
         * @since 1.0.0
         * @access   public
         */
        public function flush_rewrite() {
            if ( get_option( 'iccs_flush_rewrite_rules' ) ) {
                flush_rewrite_rules();
                delete_option( 'iccs_flush_rewrite_rules' );
            }
        }

        /**
         * Enqueue Gutenberg block assets for both frontend + backend.
         *
         * @uses {wp-editor} for WP editor styles.
         * @since 1.0.0
         * @access   public
         */
        public function block_assets() {
            wp_enqueue_style(
                'iccs_schedule-cgb-style-css',
                plugins_url( 'dist/blocks.style.build.css', $this->plugin_path ),
                array( 'wp-editor' )
            );
        }

        /**
         * Enqueue Gutenberg block assets for backend editor.
         *
         * @uses {wp-blocks} for block type registration & related functions.
         * @uses {wp-element} for WP Element abstraction — structure of blocks.
         * @uses {wp-i18n} to internationalize the block's text.
         * @uses {wp-editor} for WP editor styles.
         * @since 1.0.0
         * @access   public
         */
        public function editor_assets() {
            wp_enqueue_script(
                'iccs_schedule-cgb-block-js',
                plugins_url( '/dist/blocks.build.js', $this->plugin_path ),
                array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ),
                $this->version,
                true
            );
            wp_enqueue_style(
                'iccs_schedule-cgb-block-editor-css',
                plugins_url( 'dist/blocks.editor.build.css', $this->plugin_path ),
                array( 'wp-edit-blocks' )
            );
        }
    }
}
