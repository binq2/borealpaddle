<?php

function theme_enqueue_styles() {

	global $wp_styles;
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core', array('jquery'));
	wp_enqueue_script('jquery-ui-accordion', array('jquery'));
	wp_enqueue_script('jquery-ui-widget', array('jquery'));
	
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
	
	//----Load jQuery Easings----------
	wp_enqueue_script( 'plumtree-easings', get_template_directory_uri() . '/js/jquery.easing.js', array('jquery'), '1.3.0');	
	
	//----Load Bootsrap JS-------------
	wp_enqueue_script( 'plumtree-bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), '1.0');	
	wp_enqueue_script( 'plumtree-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '1.0');

    //----Enqurie ---------------------
    wp_enqueue_script( 'enquire', get_template_directory_uri() . '/js/enquire.min.js', array('jquery'), '1.2' );

    //----Load Waypoints---------------
	wp_enqueue_script('plumtree-waypoints', get_template_directory_uri() . '/js/waypoints.min.js', array('jquery'));
	wp_enqueue_script('plumtree-waypoints-sticky', get_template_directory_uri() . '/js/waypoints-sticky.min.js', array('jquery'));
	
	wp_enqueue_script('plumtree-img', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js');
		
	//----Load Hover Intent Plugin----------------
	wp_enqueue_script( 'plumtree-hoverintent', get_template_directory_uri() . '/js/hoverIntent.js', array('jquery'), '1.0');

    //----Load Validate Plugin----------------
    wp_enqueue_script( 'plumtree-validate', get_template_directory_uri() . '/js/jquery.validate.min.js', array('jquery'), '1.0');

	//----Load Theme JS Helper---------------------
	wp_enqueue_script( 'plumtree-helper', get_template_directory_uri() . '/js/helper.js', array('jquery'), '1.0', true);
	
	//----Shop Tooltips---------------------
	wp_enqueue_script( 'plumtree-shop-tooltips', get_template_directory_uri() . '/js/shop-tooltips.js', array('jquery'), '1.0', true);

	//----WooCommerce Checkout with form styler fix-----------
	//wp_enqueue_script('checkout-fix', get_template_directory_uri().'/js/country-select.js', array('jquery'), '1.0', true);
	

    $parent_style = 'plumtree-style';

    //----Load CSS--------------------------------
	wp_enqueue_style( 'plumtree-bootstrap', get_template_directory_uri(). '/css/bootstrap.min.css' );
	wp_enqueue_style( 'plumtree-bootstrap-r', get_template_directory_uri(). '/css/bootstrap.r.min.css' );
	//wp_enqueue_style( 'plumtree-bootstrap-responsive', get_template_directory_uri(). '/css/bootstrap-responsive.min.css' );
	wp_enqueue_style( 'plumtree-awesome-fonts', get_template_directory_uri(). '/css/font-awesome.min.css' );
	wp_enqueue_style( 'plumtree-reset', get_template_directory_uri().'/css/reset.css' );
	wp_enqueue_style( 'plumtree-layout', get_template_directory_uri().'/css/layout.css' );
	wp_enqueue_style( 'plumtree-basic', get_stylesheet_uri() );
	wp_enqueue_style( 'plumtree-animation', get_template_directory_uri().'/css/animation.css' );
	wp_enqueue_style( 'plumtree-specials', get_template_directory_uri().'/css/specials.css' );
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style )
    );
    
    //---- Theme fonts -----------------------------------
	wp_enqueue_style( 'ptpanel-fonts', get_template_directory_uri().'/css/fonts.css' );


	//---- Theme Patterns -------------------------------
	wp_enqueue_style( 'plumtree-ie', get_template_directory_uri() . '/css/ie.css', array( 'plumtree-style' ), '20121010' );
	$wp_styles->add_data( 'plumtree-ie', 'conditional', 'lt IE 9' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


/**
 * Get Matrix title 
 * @param int itemMatrixID
 */
function get_matrix_title($itemMatrixID, $title=null){

	$ptitle = $title;
		
	if(empty($itemMatrixID))
		return;
		
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml')) {
		$xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml');
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$matrix = $xml->xpath("//ItemMatrices/ItemMatrix/itemMatrixID[.='$itemMatrixID']/parent::*");
		
		$title = $matrix[0]->description;
		
		
		
	}
	
	if(empty($title)){
		$has_value = false;
		$title = $ptitle;
		$attributes = '';
		if(!empty($matrix[0]->itemAttributes))
			$attributes = $matrix[0]->itemAttributes;
		if(!empty($attributes)){	
			foreach($attributes as $attribute){
				if($attribute)
					$has_value = true;
			}
		}
		if($has_value)
			$title = substr($title,0,strrpos($title, " "));
	}	
	
	return $title;
	
}

/**
 * Get Matrix Images 
 * @param int itemMatrixID
 */
function get_matrix_images($itemMatrixID, $itemID=null){
	
	$images = null;
	
	if(empty($itemMatrixID))
		return;
		
	$path = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/items/lightspeed-webstore-products.xml';
	$type = 'items';
	
	$images = wclsc_get_product_images($type, $path, '', $itemID);	
	
	if(empty($images)){
		$path = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml';
		$type = 'matrix';
		$images = wclsc_get_product_images($type, $path, $itemMatrixID);
	}
	
	return $images;
	
}

//return product images from Lightspeed XML 
function wclsc_get_product_images($type, $path, $itemMatrixID=null, $itemID=null){
	$image_paths = null;
	if (file_exists($path)) {
		$xml = file_get_contents($path);
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$results = ($type == 'items')? $xml->xpath("//Items/Item/itemID[.='$itemID']/parent::*") : $xml->xpath("//ItemMatrices/ItemMatrix/itemMatrixID[.='$itemMatrixID']/parent::*");
		
		if(!empty($results)){
			$images = $results[0]->Images;
		
		
			if(!empty($images)){
				$images=$images[0];
				foreach($images as $image){			
					$image_paths .= $image->baseImageURL.$image->publicID."\r\n";
				}
			}
		}
	}
	return $image_paths;
}

//remove the featured image from the product gallery display.
add_filter('woocommerce_product_gallery_attachment_ids', 'remove_featured_image', 10,2);
function remove_featured_image($ids, $product) {
	$featured_image = null;
	if(!empty($product->children['post']))
		$i = $product->children['post']->ID;
		$featured_image = get_post_thumbnail_id($i);
		//echo "Featured Image ID =".$featured_image;
		//print_r($ids);
	if (($key = array_search($featured_image, $ids)) !== false) {
	    unset($ids[$key]);
	}
	return $ids;
	
}

add_action( 'after_setup_theme', 'register_footer_menu' );
function register_footer_menu() {
  register_nav_menu( 'footer', __( 'Footer Menu', 'commercegurus' ) );
}


function get_item_vendor($vendorID){
	if(empty($vendorID))
		return;
	
	$vendor = '';
	
	$path = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/vendors/lightspeed-webstore-vendors.xml';
	
	if (file_exists($path)) {
		$xml = file_get_contents($path);
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$results = $xml->xpath("//Vendors/Vendor/vendorID[.='$vendorID']/parent::*");
		$vendor = $results[0]->name;
		
	}
	
	return $vendor;

}

function get_item_manufacturer($manufacturerID){
	if(empty($manufacturerID))
		return;
	
	$manufacturer = '';
	
	$path = $_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/manufacturers/lightspeed-webstore-manufacturers.xml';
	
	if (file_exists($path)) {
		$xml = file_get_contents($path);
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$results = $xml->xpath("//Manufacturer/manufacturerID[.='$manufacturerID']/parent::*");
		$manufacturer = $results[0]->name;
		
	}
	
	return $manufacturer;

}

/**
 * Get Matrix Short Desc 
 * @param int itemMatrixID
 */
function get_matrix_short_desc($itemMatrixID, $itemID){

		
	if(empty($itemMatrixID))
		return;
		
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/items/lightspeed-webstore-products.xml')) {
		$xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/items/lightspeed-webstore-products.xml');
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$item = $xml->xpath("//Items/Item/itemID[.='$itemID']/parent::*");
		
		$desc = $item[0]->ItemECommerce->shortDescription;
		
		
		
	}
	
	if(empty($desc)){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml')) {
		$xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml');
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$matrix = $xml->xpath("//ItemMatrices/ItemMatrix/itemMatrixID[.='$itemMatrixID']/parent::*");
		
		$desc = $matrix[0]->ItemECommerce->shortDescription;
		
		
		
	}
	}	
	
	return $desc;
	
}


/**
 * Get Matrix Long Desc 
 * @param int itemMatrixID
 */
function get_matrix_long_desc($itemMatrixID, $itemID){


		
	if(empty($itemMatrixID))
		return;
		
	if (file_exists($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/items/lightspeed-webstore-products.xml')) {
		$xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/items/lightspeed-webstore-products.xml');
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$item = $xml->xpath("//Items/Item/itemID[.='$itemID']/parent::*");
		
		$desc = $item[0]->ItemECommerce->longDescription;
		
		
		
	}
	
	if(empty($desc)){
		if (file_exists($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml')) {
		$xml = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/lightspeed-import/xml/matrices/lightspeed-webstore-product_matrices.xml');
				
		if ($xml == FALSE)
		{
		  echo "Failed loading XML\n";

		  foreach (libxml_get_errors() as $error) 
		  {
			echo "\t", $error->message;
		  }   
		}
		
		
		$xml = simplexml_load_string($xml);
		
		$matrix = $xml->xpath("//ItemMatrices/ItemMatrix/itemMatrixID[.='$itemMatrixID']/parent::*");
		
		$desc = $matrix[0]->ItemECommerce->longDescription;
		
		
		
	}
	}	
	
	return $desc;
	
}

add_action( 'woocommerce_product_meta_end', 'cj_show_attribute_links' );

function cj_show_attribute_links() {
	global $post;
	$attribute_names = array( 'pa_brand', 'pa_size' ); // Insert attribute names here

	foreach ( $attribute_names as $attribute_name ) {
		$taxonomy = get_taxonomy( $attribute_name );

		if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
			$terms = wp_get_post_terms( $post->ID, $attribute_name );
			$terms_array = array();

	        if ( ! empty( $terms ) ) {
		        foreach ( $terms as $term ) {
			       $archive_link = get_term_link( $term->slug, $attribute_name );
			       $full_line = '<a href="' . $archive_link . '">'. $term->name . '</a>';
			       array_push( $terms_array, $full_line );
		        }

		        echo $taxonomy->labels->name . ' ' . implode( $terms_array, ', ' );
	        }
    	}
    }
}


add_shortcode( 'product_attributes', 'product_attributes' );

/**
	 * List all (or limited) product atributes
	 *
	 * @param array $atts
	 * @return string
	 */
	function product_attributes( $atts ) {
		global $woocommerce_loop;

		$atts = shortcode_atts( array(
			'number'     => null,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'columns'    => '4',
			'hide_empty' => 0,
			'attribute'     => '',
			'ids'        => ''
		), $atts );

		if ( isset( $atts['ids'] ) ) {
			$ids = explode( ',', $atts['ids'] );
			$ids = array_map( 'trim', $ids );
		} else {
			$ids = array();
		}

		$hide_empty = ( $atts['hide_empty'] == true || $atts['hide_empty'] == 1 ) ? 1 : 0;

		// get terms and workaround WP bug with parents/pad counts
		$args = array(
			'orderby'    => $atts['orderby'],
			'order'      => $atts['order'],
			'hide_empty' => $hide_empty,
			'include'    => $ids,
			'number'     => $atts['number'],
			'pad_counts' => true
		);
					

		$product_attributes = get_terms( 'pa_'.$atts['attribute'], $args );
		

		if ( $hide_empty ) {
			foreach ( $product_attributes as $key => $attribute ) {
				if ( $attribute->count == 0 ) {
					unset( $product_attributes[ $key ] );
				}
			}
		}

		if ( $atts['number'] ) {
			$product_attributes = array_slice( $product_attributes, 0, $atts['number'] );
		}

		$columns = absint( $atts['columns'] );
		$woocommerce_loop['columns'] = $columns;

		ob_start();

		// Reset loop/columns globals when starting a new loop
		$woocommerce_loop['loop'] = $woocommerce_loop['column'] = '';

		if ( $product_attributes ) {

			woocommerce_product_loop_start();
			
			print_r($product_attributes);

			foreach ( $product_attributes as $attribute ) {

				wc_get_template( 'content-product_attribute.php', array(
					'attribute' => $attribute
				) );

			}

			woocommerce_product_loop_end();

		}

		woocommerce_reset_loop();

		return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}