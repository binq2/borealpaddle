<?php //Child Theme Functions


//add_action('wp_print_scripts','dequeue_myscript');
function dequeue_myscript() {
  wp_deregister_script('wc-add-to-cart-variation');
  wp_dequeue_script('wc-add-to-cart-variation');
  
  wp_deregister_script('wc-single-product');
  wp_dequeue_script('wc-single-product');
}

function theme_name_scripts() {
	wp_enqueue_style( 'style-name', get_stylesheet_directory_uri() . '/product-options.css');        
    //wp_enqueue_script( 'single-product', get_stylesheet_directory_uri() . '/woocommerce/single-product.js', array('jquery'), '', true );
    //wp_enqueue_script( 'cart-variation', get_stylesheet_directory_uri() . '/woocommerce/add-to-cart-variation.js', array('jquery'), '', true );
    wp_enqueue_script( 'magnific-popup', get_stylesheet_directory_uri() . '/jquery.magnific-popup.min.js', array('jquery'), '0.9.9', true );
	wp_enqueue_script( 'owlcarousel', get_stylesheet_directory_uri() . '/owl.carousel.min.js', array('jquery'), '', true );
	wp_enqueue_script( 'quickview', get_stylesheet_directory_uri() . '/cg_quickview.js', array('jquery'), '1.0.0', true );
	wp_localize_script( 'quickview', 'cg_ajax', array( 'cg_ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

// Initialize some global js vars
add_action( 'wp_head', 'cg_js_init' );
if ( !function_exists( 'cg_js_init' ) ) {

    function cg_js_init() {
        global $cg_options;
        ?>
        <script type="text/javascript">
            var view_mode_default = 'grid-layout';
            var cg_sticky_default = 'yes';
            var cg_chosen_variation = 'wc_chosen_variation_disabled';
        </script>
        <?php
    }

}

// WooCommerce Quick View Ajax Helpers
function cg_quickview() {
    global $post, $product, $woocommerce;
    $cg_prod_id = $_POST["productid"];
    $post = get_post( $cg_prod_id );
    $product = get_product( $cg_prod_id );

    ob_start();

    woocommerce_get_template( 'content-single-product-cg-quickview.php' );

    $cg_output = ob_get_contents();
    ob_end_clean();
    echo $cg_output;
    die();
}

add_action( 'wp_ajax_cg_quickview', 'cg_quickview' );
add_action( 'wp_ajax_nopriv_cg_quickview', 'cg_quickview' );


// Register Support
add_theme_support( 'woocommerce' );

// Set path to WooFramework and theme specific functions
$woocommerce_path = get_stylesheet_directory() . '/woocommerce/';

// WooCommerce
if ( function_exists( "is_woocommerce" ) ) {
    require_once ( $woocommerce_path . 'woocommerce-config.php' );    //woocommerce shop plugin    
}
