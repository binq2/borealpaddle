<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Wvas
 * @subpackage Wvas/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wvas
 * @subpackage Wvas/includes
 * @author     ORION <freelance@orionorigin.com>
 */
class Wvas {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wvas_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'wvas';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wvas_Loader. Orchestrates the hooks of the plugin.
	 * - Wvas_i18n. Defines internationalization functionality.
	 * - Wvas_Admin. Defines all hooks for the admin area.
	 * - Wvas_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wvas-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wvas-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wvas-admin.php';

                /**
		 * The class responsible for defining all actions that occur in the admin area and related to products.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wvas-product.php';
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wvas-public.php';
                
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wvas-skin.php';

		$this->loader = new Wvas_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wvas_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wvas_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wvas_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                
                //skin configuration
                $skin_configuration = new WVAS_Configuration();
                $this->loader->add_action( 'save_post',$skin_configuration, 'save_wvas_config');
                
                //Products
                $product_admin=new Wvas_Product();
                $this->loader->add_action( 'woocommerce_product_write_panel_tabs',$product_admin, 'get_wvas_editor_tab_label');
                $this->loader->add_action( 'woocommerce_product_write_panels',$product_admin, 'get_wvas_editor_tab');
                $this->loader->add_action( 'wp_ajax_get_wvas_editor_content',$product_admin, 'get_wvas_editor_tab_content_ajx');
                $this->loader->add_action( 'admin_menu',$product_admin, 'wvas_menu');
                $this->loader->add_action( 'save_post_product',$product_admin, 'save_visual_variation');
                
                //Configurations
                $config=new WVAS_Configuration();
                $this->loader->add_action( 'init', $config, 'register_cpt_config' );
//                $this->loader->add_filter( 'manage_edit-wpc-template_columns', $config, 'get_templates_columns' );
//                $this->loader->add_action( 'manage_wpc-template_posts_custom_column', $config, 'get_templates_columns_values', 5, 2 );
                $this->loader->add_action( 'add_meta_boxes', $config, 'get_config_metabox' );
                
                $this->loader->add_action( 'admin_notices', $plugin_admin, 'wvas_admin_notice' );
                $this->loader->add_action( 'admin_init', $plugin_admin, 'dismiss_notification' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wvas_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                $this->loader->add_action( 'woocommerce_variable_add_to_cart', $plugin_public, 'display_visual_attr' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wvas_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
