<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.orionorigin.com
 * @since             1.0.0
 * @package           Wvas
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Visual Attributes Swatches
 * Plugin URI:        http://www.orionorigin.com/plugins/woo-visual-attributes-swatches/
 * Description:       Replace woocommerce's default attributes dropdowns by pictures or color fields
 * Version:           0.3
 * Author:            ORION
 * Author URI:        http://orionorigin.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wvas
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WVA_URL', plugins_url('/', __FILE__) );
define( 'WVA_DIR', dirname(__FILE__) );
define( 'WVA_SKINS_DIR', WVA_DIR."/includes/skins" );
define( 'WVA_SKINS_URL', WVA_URL."/includes/skins" );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wvas-activator.php
 */
function activate_wvas() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wvas-activator.php';
	Wvas_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wvas-deactivator.php
 */
function deactivate_wvas() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wvas-deactivator.php';
	Wvas_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wvas' );
register_deactivation_hook( __FILE__, 'deactivate_wvas' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wvas.php';
require plugin_dir_path( __FILE__ ) . 'admin/class-wvas-configuration.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wvas() {

	$plugin = new Wvas();
	$plugin->run();

}
run_wvas();
