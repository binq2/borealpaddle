<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop, $post, $pid, $products;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
$classes[] = $woocommerce_loop['columns'];
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';

// Adding extra data for isotope filtering
/*$attributes = $product->get_attributes();
if ($attributes) {
	foreach ( $attributes as $attribute ) {
		if ( $attribute['is_taxonomy'] ) {
			$values = woocommerce_get_product_terms( $product->id, $attribute['name'], 'names' );
			$result = implode( ', ', $values );
		} else {
			$values = array_map( 'trim', explode( '|', $attribute['value'] ) );
			$result = implode( ', ', $values );
		}
		$arr[] = strtolower($result);
	}
	$attr = implode(', ', $arr);
}*/

// Adding extra featured img if exists
$attr = '';
$slug = sanitize_title($product['description']);
$id = $product['itemMatrixID'];
$lightspeed_id = '1000000000' + $product['itemMatrixID'];
$link = "http://borealpaddle.lightspeedwebstore.com/". $slug ."/dp/". $lightspeed_id ;
$has_image = (!empty($product['Images']))? true : false;
$images = $product['Images'][0]['Image'];
$image_base = $product['Images'][0]['Image'][0]['baseImageURL'];
$image_id = $product['Images'][0]['Image'][0]['publicID'];
$image_url = $image_base .'c_fill,h_220,w_220/'. $image_id;
$price = $product['Prices'][0]['ItemPrice'][0]['amount'];
$pid = $id;
$flip_class = 'flip-enabled';

if($has_image){

?>

<li <?php post_class( $classes ); ?> data-element="<?php echo $attr; ?>">

	<div class="product-wrapper <?php echo $flip_class; ?> <?php echo get_option('product_hover');?>">

		<div class="animation-section" data-product-link="<?php echo $link; ?>">

			<div class="product-img-wrapper flip-container">

				<div class="flipper">

					<div class="front img-wrap">
						<?php echo '<img src="'. $image_url .'" alt="'. $product['Images'][0]['Image'][0]['description'] .'" />'; ?>
					</div>					

						<div class="back img-wrap">
							<?php echo '<img src="'. $image_url .'" alt="'. $product['Images'][0]['Image'][0]['description'] .'" />'; ?>
						</div>

				</div>

			</div>
            <?php do_action( 'woocommerce_animation_section_end' ); ?>

            <div class="product-controls-wrapper" data-product-link="<?php echo $link; ?>">

				<div class="buttons-wrapper">

					<?php 
					// woocommerce_before_shop_loop_item_title hook
					do_action( 'woocommerce_before_shop_loop_item_title' );

					// add to wishlist button
					/*if ( ( class_exists( 'YITH_WCWL_Shortcode' ) ) && ( get_option('yith_wcwl_enabled') == true ) ) {
						$atts = array(
			                'per_page' => 10,
			                'pagination' => 'no', 
			            	);
						echo YITH_WCWL_Shortcode::add_to_wishlist($atts);
					}*/
					?>

					<span class="product-tooltip"></span>

				</div>

				<div class="vertical-helper"></div>

			</div>

		</div><!-- .animation-section ends -->

		<div class="product-description-wrapper">

			<?php $product_title = strip_tags( $product['description'] ); ?>

			<a class="product-title" href="<?php echo $link; ?>" title="Click to learn more about <?php echo $product_title; ?>">
				<h3><?php echo $product_title; ?></h3>
			</a>

			<?php if ( !empty($product['shortDescription'] )) : ?>
				<div itemprop="description" class="entry-content">
					<?php echo apply_filters( 'woocommerce_short_description', $product['shortDescription'] ); ?>
				</div>
			<?php endif; ?>

			<div class="product-price-wrapper">
				<?php
				// woocommerce_after_shop_loop_item_title hook
				//do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
			</div>

			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

		</div>

	</div><!-- .product-wrapper ends -->

</li>
<?php } ?>