<?php
/**
 * Plugin Name: Lightspeed Import
 * Plugin URI:  http://inteleck.com/wordpress/plugins
 * Description: Lightspeed Retail Import for WordPress.
 * Version:     1.0
 * Author:      Aaron Affleck
 * Author URI:  http://inteleck.com
 * License:     GPLv2+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 *  Lightspeed Import CONTAINER CLASS
 */
if(!class_exists("LightspeedImport")) :
	class LightspeedImport {
	
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
		 * @var $API_account
		 */
		public $API_account = null;
		
		/**
		 * @var $LI_cache
		 */
		public $LI_cache = null;

		/**
		* Main Lightspeed Import Instance
		*
		* Ensures only one instance of Lightspeed Import is loaded or can be loaded.
		*
		* @since 2.1
		* @static
		* @see LI()
		* @return Lightspeed Import - Main instance
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
		 * Constructor.
		 * @access public
		 *
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
				$options_page = new lightspeedImportOptions();
				add_action('admin_menu', array($options_page, 'add_pages')); // adds page to menu
				add_action('admin_init', array($options_page, 'register_settings'));
			}*/
			
			register_activation_hook( __FILE__, array($this, 'lightspeed_import_activation') );
			
			add_action( 'lightspeed_import_hourly_event_hook', array($this, 'lightspeed_import') );
			
			//add_action('init', array($this, 'lightspeed_import'));
			
			add_filter( 'cron_schedules', array($this, 'cron_add_five_mins') );
		}
		
		
		
		/**
		 * On activation, set a time, frequency and name of an action hook to be scheduled.
		 */
		function lightspeed_import_activation() {
			wp_schedule_event( time(), 'everyfive', 'lightspeed_import' );
			
			
		}

		
		/**
		 * On the scheduled action hook, run the function.
		 */
		function lightspeed_import() {
			// do import every hour
			
			
			
			$emitter = 'https://api.merchantos.com/API/Account/'.LI()->API_account.'/ItemMatrix';

			$xml_query_string = 'tag=webstore&limit=100&load_relations=["ItemECommerce","Tags","Images","Category"]';
			
			$products = LI()->api->makeAPICall("Account.ItemMatrix","Read",null,null,$emitter, $xml_query_string);
			
			
			if(!empty($products))
				$products->asXML($this->plugin_path() . '/xml/boreal-paddle-lightspeed-products.xml');
				
			
			
		}
		
		
 
		function cron_add_five_mins( $schedules ) {
			// Adds once weekly to the existing schedules.
			$schedules['everyfive'] = array(
				'interval' => 300,
				'display' => __( 'Once Every Five Mins' )
			);
			return $schedules;
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
				define('LSI_PLUGIN_NAME', 'Lightspeed Import');
			}

			if(!defined('LSI_PLUGIN_SLUG')) {
				define('LSI_PLUGIN_SLUG', 'lightspeed-import');
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
			
			/*if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				$this->frontend_includes();
			}*/
			
			// Require Library
			require_once("includes/phpFastCache/phpfastcache/phpfastcache.php");
			
			// include our handy API wrapper that makes it easy to call the API, it also depends on MOScURL to make the cURL call
			require_once('includes/mosapi/class-li-curl.php');
			
			include_once("includes/mosapi/class-li-api.php");
			
			include_once("includes/mosapi/mosapi-functions.php");

						
		}

		/**
		 * Function used to Init Lightspeed Import Template Functions - This makes them pluggable by plugins and themes.
		 */
		public function include_template_functions() {
			if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
				include_once( 'includes/li-template-functions.php' );
			}
		}
		
		
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
 * @return LightspeedImport
 */
function LI() {
	return LightspeedImport::instance();
}

// Global for backwards compatibility.
$GLOBALS['lightspeedimport'] = LI();