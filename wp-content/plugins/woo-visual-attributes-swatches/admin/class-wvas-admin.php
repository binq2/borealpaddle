<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://orionorigin.com
 * @since      1.0.0
 *
 * @package    Wvas
 * @subpackage Wvas/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wvas
 * @subpackage Wvas/admin
 * @author     ORION <freelance@orionorigin.com>
 */
class Wvas_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wvas_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wvas_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                wp_enqueue_style("wvas-colorpicker-css", plugin_dir_url(__FILE__) . 'js/colorpicker/css/colorpicker.css', array(), $this->version, 'all');
                wp_enqueue_style( "wvas-ui", plugin_dir_url( __FILE__ ) . 'css/UI.css', array(), $this->version, 'all' );
				wp_enqueue_style( "wvas-nouislider-css", plugin_dir_url( __FILE__ ) . 'js/noUiSlider/jquery.nouislider.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wvas-admin.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wvas_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wvas_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                wp_enqueue_media();                 
                wp_enqueue_script('wvas-colorpicker-js', plugin_dir_url(__FILE__) . 'js/colorpicker/js/colorpicker.js', array('jquery'), $this->version, false);
                wp_enqueue_script( "wvas-tabs", plugin_dir_url( __FILE__ ) . 'js/SpryAssets/SpryTabbedPanels.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( "wvas-nouislider-min-js", plugin_dir_url( __FILE__ ) . 'js/noUiSlider/jquery.nouislider.min.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wvas-admin.js', array( 'jquery' ), $this->version, false );
                wp_localize_script($this->plugin_name, 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
	}
        
        function wvas_admin_notice($param) {
            global $current_user ;
            $user_id = $current_user->ID;
            /* Check that the user hasn't already clicked to ignore the message */
            if ( ! get_user_meta($user_id, 'wvas_dismiss_notice_1') ) {
                $this->run_wvas_db_updates_requirements();
            }
        }
        
        	//Check for db update requirement 
	public function run_wvas_db_updates_requirements(){
	    //Checks db structure for v1.0
	    if($this->check_previous_release_meta('wpb_product_configurator')>0 )
            {
                ?>
                    <div class="updated" id="wvas-updater-container">
	                <strong><?php echo _e("Woo Visual Attributes Swatches attributes update required","wvas");?></strong>
	                <div>
	                <?php echo _e("Hello! <br/> Thank you again for updating this plugin. This new version comes with a lot of changes and amazing features which are unfortunately incompatible with the previous versions. This means you'll have to redefine your variable products attributes otherwise, the plugin won't be able to work properly. <br/> 
                                        - If you don't want to use our new features and stick with the previous version, please remove this new version and reinstall the old one. <br/> 
                                        - If you want to use our new features, you can easily redefine your attributes per product or using our bulk attributes definition feature available <a href=".admin_url( 'admin.php?page=wvas-bulk-attributes-definition').">here</a>.","wvas");?><br>
	                </div>
                        <a href="?wvas_dismiss_notice_1=0"><span class="dashicons dashicons-dismiss"style="vertical-align: middle;"></span> Dismiss</a>
                    </div>
	        <style>
	        #wvas-updater-container
	        {
	            padding: 3px 17px;
	            font-size: 15px;
	            line-height: 36px;
	            margin-left: 0px;
	            border-left: 5px solid #e14d43 !important;
	        }
	        </style>
	      <?php
		}
	}
        
        public function dismiss_notification()
        {
            global $current_user;
            $user_id = $current_user->ID;
            if ( isset($_GET['wvas_dismiss_notice_1']) && '0' == $_GET['wvas_dismiss_notice_1'] ) {
                 add_user_meta($user_id, 'wvas_dismiss_notice_1', 'true', true);
            }
        }
        
        function check_previous_release_meta(){
            $meta = 'visual_attr';
            global $wpdb;
	    $sql_result=$wpdb->get_var("
                SELECT count(*) 
                FROM $wpdb->posts p 
                JOIN $wpdb->postmeta pm on pm.post_id = p.id 
                WHERE p.post_type = 'product' 
                AND pm.meta_key = '".$meta."' 
                AND pm.meta_value LIKE '%wva_tpl_%'  
                AND p.post_status='publish'
            ");
        
        
	    return $sql_result;
        }

}
