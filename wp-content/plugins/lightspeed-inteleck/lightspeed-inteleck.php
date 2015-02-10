<?php
/**
 * Plugin Name: Lightspeed Inteleck
 * Plugin URI:  http://inteleck.com/wordpress/plugins
 * Description: Lightspeed Retail Integration for WordPress.
 * Version:     1.0
 * Author:      Aaron Affleck
 * Author URI:  http://inteleck.com
 * License:     GPLv2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 *  Lightspeed Inteleck CONTAINER CLASS
 */
if(!class_exists("LightspeedInteleck")) :
	class LightspeedInteleck {
	
		/**
		 * @var WooCommerce The single instance of the class
		 * @since 2.1
		 */
		protected static $_instance = null;
	
		/**
		 * @var $API_key
		 */
		public $API_key = null;
		
		/**
		 * @var $API_key
		 */
		public $API_account = null;
		
		/**
		 * @var $LI_cache
		 */
		public $LI_cache = null;

		/**
		* Main Lightspeed Inteleck Instance
		*
		* Ensures only one instance of Lightspeed Inteleck is loaded or can be loaded.
		*
		* @since 2.1
		* @static
		* @see LI()
		* @return Lightspeed Inteleck - Main instance
		*/
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
		}
			return self::$_instance;
		}
		
		/**
		 * Cloning is forbidden.
		 *
		 * @since 2.1
		*/
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
		}

		/**
		* Unserializing instances of this class is forbidden.
		*
		* @since 2.1
		*/
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
		}

		/**
		 * WooCommerce Constructor.
		 * @access public
		 * @return WooCommerce
		 */

		public function __construct() {

			// Auto-load classes on demand
			if ( function_exists( "__autoload" ) ) {
				spl_autoload_register( "__autoload" );
			}

			spl_autoload_register( array( $this, 'autoload' ) );

			// Define constants
			$this->define_constants();

			// Include required files
			$this->includes();
			
			$this->API_key = "992e498dfa5ab5245f5bd5afee4ee1ce6ac6e0a1ee7d11e36480694a9b5282e7";
			
			$this->API_account = "83442";

			// Init API
			$this->api = new LI_API($this->API_key,$this->API_account);
			
			// simple Caching with:
			$this->LI_cache = phpFastCache();
			
			//$this->options = get_option('lightspeed_inteleck_options');

			/*if(is_admin()) {
				require_once(LSI_DIR_PATH.'views/lsi-options.php'); // include options file
				$options_page = new lightspeedInteleckOptions();
				add_action('admin_menu', array($options_page, 'add_pages')); // adds page to menu
				add_action('admin_init', array($options_page, 'register_settings'));
			}*/

			add_action('init', array($this, 'init'));
			add_action('init', array('LI_Shortcodes', 'init'));
			add_action('widgets_init', array($this, 'include_widgets'));
		}
		
		
		/**
		 * Auto-load LI classes on demand to reduce memory consumption.
		 *
		 * @param mixed $class
		 */
		public function autoload( $class ) {
			$path  = null;
			$class = strtolower( $class );
			$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

			if ( strpos( $class, 'li_widget_' ) === 0 ) {
				$path = $this->plugin_path() . '/includes/widgets/';
			}
			elseif ( strpos( $class, 'li_shortcode_' ) === 0 ) {
				$path = $this->plugin_path() . '/includes/shortcodes/';
			}

			if ( $path && is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}

			// Fallback
			if ( strpos( $class, 'li_' ) === 0 ) {
				$path = $this->plugin_path() . '/includes/';
			}

			if ( $path && is_readable( $path . $file ) ) {
				include_once( $path . $file );
				return;
			}
		}
		
		/**
		 * Define LSI Constants
		 */
		
		private function define_constants(){
			 /* CONSTANTS */
			if(!defined('LSI_MIN_WP_VERSION')) {
				define('LSI_MIN_WP_VERSION', '3.1');
			}

			if(!defined('LSI_PLUGIN_NAME')) {
				define('LSI_PLUGIN_NAME', 'Lightspeed Inteleck');
			}

			if(!defined('LSI_PLUGIN_SLUG')) {
				define('LSI_PLUGIN_SLUG', 'lightspeed-inteleck');
			}

			if(!defined('LSI_DIR_PATH')) {
				define('LSI_DIR_PATH', plugin_dir_path(__FILE__));
			}

			if(!defined('LSI_DIR_URL')) {
				define('LSI_DIR_URL', plugin_dir_url(__FILE__));
			}
		}
		
		/**
		 * Include required core files used in admin and on the frontend.
		 */
		private function includes() {

			/*if ( is_admin() ) {
				include_once( 'includes/admin/class-li-admin.php' );
			}*/
			
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				$this->frontend_includes();
			}
			
			// Require Library
			require_once("includes/phpFastCache/phpfastcache/phpfastcache.php");
			
			// include our handy API wrapper that makes it easy to call the API, it also depends on MOScURL to make the cURL call
			require_once('includes/mosapi/class-li-curl.php');
			
			include_once("includes/mosapi/class-li-api.php");
			
			include_once("includes/mosapi/mosapi-functions.php");

						
		}

		/**
		 * Function used to Init Lightspeed Inteleck Template Functions - This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				include_once( 'includes/li-template-functions.php' );
			}
		}
		
		/**
		 * Include required frontend files.
		 */
		public function frontend_includes() {
			// Functions
			
			// Classes
			include_once( 'includes/class-li-shortcodes.php' );                     // Shortcodes class
		}	

		/**
		 * Include core widgets
		 */
		public function include_widgets() {
			include_once( 'includes/abstract/abstract-li-widget.php' );

			register_widget( 'LI_Widget_Tag_Cloud' );
		}

		/**
		 * Init Lightspeed Inteleck when WordPress Initialises.
		 */
		public function init() {
			// Before init action
			//Do some init
		}

		/**
		 * register_scripts
		 *
		 */
		/*public function register_scripts() {
			$options = $this->options;
			
			// ENQUEUE VISUALSEARCH SCRIPTS
			//			wp_enqueue_script('underscore', MAS_DIR_URL.'js/underscore-min.js');
			//			wp_enqueue_script('backbone', MAS_DIR_URL.'js/backbone-min.js', array('underscore'));
			wp_enqueue_script('underscore');
			wp_enqueue_script('backbone');
			wp_enqueue_script(
				'visualsearch',
				MAS_DIR_URL.'js/visualsearch.js',
				array(
					 'jquery',
					 'jquery-ui-core',
					 'jquery-ui-datepicker',
					 'jquery-ui-widget',
					 'jquery-ui-position',
					 'jquery-ui-autocomplete',
					 'backbone',
					 'underscore'
				)
			);
			
			// ENQUEUE AND LOCALIZE MAIN JS FILE
			wp_enqueue_script('mas-script', MAS_DIR_URL.'js/main.js', array('visualsearch'));

			


			// ENQUEUE STYLES
			wp_enqueue_style('mas-bar', MAS_DIR_URL.'css/visualsearch.css');
			wp_enqueue_style('mas-calendar', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css');
		}*/		
		
		
		//****** Helper Funtions *******//
		
		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		
		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}
				
	}
endif;

/**
 * Returns the main instance of LI to prevent the need to use globals.
 *
 * @since  1.0
 * @return LightspeedInteleck
 */
function LI() {
	return LightspeedInteleck::instance();
}

// Global for backwards compatibility.
$GLOBALS['woocommerce'] = LI();