<?php
/**
 * simple-instagram
 *
 * A plugin to allow users to include InstaGram feeds, media, and information. 
 * 
 *
 * @package   Simple Instagram
 * @author    Aaron Speer <adspeer@gmail.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2013 Aaron Speer
 *
 * @wordpress-plugin
 * Plugin Name:       !!! DONT UPDATE !!!Simple Instagram!!!
 * Description:       DON'T UPDATE!! CUSTOM SHORTCODE FOR CAROUSEL. A plugin to allow users to include InstaGram feeds, media, and information.
 * Version:           1.2
 * Author:            Aaron Speer
 * Author URI:        aaronspeer.com
 * Text Domain:       simple-instagram
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/simple-instagram.php' );

//Get Shortcodes file
require_once( plugin_dir_path( __FILE__ ) . 'includes/simple-instagram-shortcodes.php' );

//Get Widget File
require_once( plugin_dir_path( __FILE__ ) . 'includes/simple-instagram-widgets.php' );


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'simpleInstagram', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'simpleInstagram', 'deactivate' ) );

/*
 */
add_action( 'plugins_loaded', array( 'simpleInstagram', 'get_instance' ) );

// Add register iframe
add_action('wp_ajax_register_instagram', 'register_instagram');
add_action('wp_ajax_search_users', 'search_users');

function register_instagram(){
  	$options_check = get_option('si_options');
	$config = array(
	            'apiKey'      => $options_check['instagram_app_id'],
	            'apiSecret'   => $options_check['instagram_app_secret'],
	            'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
	          );
	
	if(isset($_GET['code'])){
	  $options_check = get_option('si_options');
	  $instagram = new Instagram($config);
	  $token = $instagram->getOAuthToken($_GET['code']);
	  if(isset($token->access_token)){
	  	update_option( 'si_oauth', $token->access_token );
	  }else{
	  	update_option( 'si_oauth', 'error');
	  }
	}
	
	
	if(strlen($options_check['instagram_app_id']) > 0 && strlen($options_check['instagram_app_secret']) > 0){
	  $set = 1;
	}else{
	  $set = 0;
	}
	
	$auth_check = get_option('si_oauth');
	if(strlen($auth_check) > 0){
	  $auth = 1;
	}else{
	  $auth = 0;
	}?>
	
	<head>
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<link href='<?php echo plugins_url( 'admin/assets/css/iframe.css' , __FILE__ ); ?>' rel='stylesheet' type='text/css'>
		<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
	</head>
	
	<?php if($auth == 0){
	  $instagram = new Instagram($config);
	  // Display the login button
	  $loginUrl = $instagram->getLoginUrl();
	?> 
	
	<div class="instagram">
		<span><i class="fa fa-instagram"></i></span><a href="<?php echo $loginUrl; ?>" target="_blank">Login with Instagram</a>
	</div>
	<script>
		setTimeout(function(){ location.reload(); },5000);
	</script>
	<?php }else{
		if($auth_check == 'error'){
			//Error with auth
			$instagram = new Instagram($config);
			// Display the login button
			$loginUrl = $instagram->getLoginUrl();
			?>
			<p>Whoops! It looks like there's a problem with your App credentials. Please check your entries in Step 02 and then use the button below to authorize the app once again.</p>
			<div class="instagram">
				<span><i class="fa fa-instagram"></i></span><a href="<?php echo $loginUrl; ?>" target="_blank">Login with Instagram</a>
			</div>
			<script>
				setTimeout(function(){ location.reload(); },5000);
			</script>
		<?php }else{
		  //We have auth credentials, check to make sure they haven't expired
		  $instagram = new Instagram($config);
		  $instagram->setAccessToken($auth_check);
		  $user = $instagram->getUser();
		  if(isset($user->data->username)){?>
		    <h2>Success!</h2>
		    <p>Alright! You're all set up and ready to go!</p>
		  <?php }else{
		  	//Auth token has expired. Show login button instead. 
		    $instagram = new Instagram($config);
		  	// Display the login button
		  	$loginUrl = $instagram->getLoginUrl();
		  ?>
		  <p>Whoops! It looks like your authorization has expired. Please use the button below to authorize the app once again.</p>
		  <div class="instagram">
				<span><i class="fa fa-instagram"></i></span><a href="<?php echo $loginUrl; ?>" target="_blank">Login with Instagram</a>
			</div>
			<script>
				setTimeout(function(){ location.reload(); },5000);
			</script>
		  <?php } ?>
		<?php }
		}
	exit;
  }

function search_users(){
  	$options = get_option('si_options');
    $auth = get_option('si_oauth');
	$user = $_POST['user'];
    $config = array(
          'apiKey'      => $options['instagram_app_id'],
          'apiSecret'   => $options['instagram_app_secret'],
          'apiCallback' => site_url() . '/wp-admin/admin-ajax.php?action=register_instagram'
        );
   
    $instagram = new Instagram($config);
    $instagram->setAccessToken($auth);
	
	$feed = $instagram->searchUser($user, 20);
	
	foreach($feed->data as $result){ ?>
		<div class="si_sr">
			<div class="si_sr_user_image">
				<img src="<?php echo $result->profile_picture; ?>">
			</div>
			<div class="si_sr_user_info">
				<div class="si_sr_user_name">
					<strong>username: </strong><?php echo $result->username; ?>
				</div>
				<div class="si_sr_user_id">
					<strong>user ID: </strong><?php echo $result->id; ?>
				</div>
			</div>
		</div>
	<?php }
	exit;
  }
/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/simple-instagram-admin.php' );
	add_action( 'plugins_loaded', array( 'simpleInstagram_admin', 'get_instance' ) );

}
