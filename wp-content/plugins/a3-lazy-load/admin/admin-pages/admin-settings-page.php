<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
a3 LazyLoad Settings Page

TABLE OF CONTENTS

- var menu_slug
- var page_data

- __construct()
- page_init()
- page_data()
- add_admin_menu()
- tabs_include()
- admin_settings_page()

-----------------------------------------------------------------------------------*/

class A3_Lazy_Load_Settings_Page extends A3_Lazy_Load_Admin_UI
{
	/**
	 * @var string
	 */
	private $menu_slug = 'a3-lazy-load';

	/**
	 * @var array
	 */
	private $page_data;

	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->page_init();
		$this->tabs_include();
	}

	/*-----------------------------------------------------------------------------------*/
	/* page_init() */
	/* Page Init */
	/*-----------------------------------------------------------------------------------*/
	public function page_init() {
		add_filter( $this->plugin_name . '_add_admin_menu', array( $this, 'add_admin_menu' ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* page_data() */
	/* Get Page Data */
	/*-----------------------------------------------------------------------------------*/
	public function page_data() {

		$page_data = array( 
			array(
				'type'				=> 'menu',
				'page_title'		=> __('a3 Lazy Load','a3_lazy_load'),
				'menu_title'		=> __('Lazy Load','a3_lazy_load'),
				'icon_url'			=> '',
				'position'			=> '25.564',
				'capability'		=> 'manage_options',
				'menu_slug'			=> $this->menu_slug,
				'function'			=> 'a3_lazy_load_settings_page_show',
				'admin_url'			=> 'admin.php',
				'callback_function' => 'callback_a3_lazy_load_settings_page_show',
				'script_function' 	=> '',
				'view_doc'			=> '',
			),
			array(
				'type'				=> 'submenu',
				'parent_slug'		=> $this->menu_slug,
				'page_title'		=> __( 'a3 Lazy Load', 'a3_lazy_load' ),
				'menu_title'		=> __( 'Settings', 'a3_lazy_load' ),
				'capability'		=> 'manage_options',
				'menu_slug'			=> $this->menu_slug,
				'function'			=> 'a3_lazy_load_settings_page_show',
				'admin_url'			=> 'admin.php',
				'callback_function' => 'a3_lazy_load_global_settings_tab_manager',
				'script_function' 	=> '',
				'view_doc'			=> '',
			),
		);

		if ( $this->page_data ) return $this->page_data;
		return $this->page_data = $page_data;

	}

	/*-----------------------------------------------------------------------------------*/
	/* add_admin_menu() */
	/* Add This page to menu on left sidebar */
	/*-----------------------------------------------------------------------------------*/
	public function add_admin_menu( $admin_menu ) {

		if ( ! is_array( $admin_menu ) ) $admin_menu = array();
		$admin_menu = array_merge( $this->page_data(), $admin_menu );

		return $admin_menu;
	}

	/*-----------------------------------------------------------------------------------*/
	/* tabs_include() */
	/* Include all tabs into this page
	/*-----------------------------------------------------------------------------------*/
	public function tabs_include() {
		include_once( $this->admin_plugin_dir() . '/tabs/template-settings/global-settings-tab.php' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page */
	/*-----------------------------------------------------------------------------------*/
	public function admin_settings_page() {
		global $a3_lazy_load_admin_init;

		$my_page_data = $this->page_data();
		$my_page_data = array_values( $my_page_data );
		$a3_lazy_load_admin_init->admin_settings_page( $my_page_data[1] );
	}

	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page */
	/*-----------------------------------------------------------------------------------*/
	public function callback_admin_settings_page() {
		global $a3_lazy_load_global_settings_panel;

		$a3_lazy_load_global_settings_panel->settings_form();
	}

}

global $a3_lazy_load_settings_page;
$a3_lazy_load_settings_page = new A3_Lazy_Load_Settings_Page();

/**
 * a3_lazy_load_settings_page_show()
 * Define the callback function to show page content
 */
function a3_lazy_load_settings_page_show() {
	global $a3_lazy_load_settings_page;
	$a3_lazy_load_settings_page->admin_settings_page();
}

function callback_a3_lazy_load_settings_page_show() {
	global $a3_lazy_load_settings_page;
	$a3_lazy_load_settings_page->callback_admin_settings_page();
}

?>
