<?php

function theme_enqueue_styles() {

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
//add_filter('woocommerce_product_gallery_attachment_ids', 'remove_featured_image', 10,2);
function remove_featured_image($ids, $product) {
	$featured_image = null;
	if(!empty($product->children['post']))
		$i = $product->children['post']->ID;
		$featured_image = get_post_thumbnail_id($i);
		//echo "Featured Image ID =".$featured_image;
		print_r($ids);
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