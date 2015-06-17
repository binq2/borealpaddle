<?php
/**
 * The template for displaying product attribute thumbnails within loops.
 *
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce_loop;



// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
else $woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', $woocommerce_loop['columns']);
// Increase loop count
$woocommerce_loop['loop']++;
?>



<div data-element class="product-category <?php
    if ( ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] == 0 || $woocommerce_loop['columns'] == 1)
        echo ' first';
	if ( $woocommerce_loop['loop'] % $woocommerce_loop['columns'] == 0 )
		echo ' last';
	?>">

	<?php do_action( 'woocommerce_before_subcategory', $attribute ); ?>

	<a href="<?php echo get_term_link( $attribute->slug, $attribute->taxonomy ); ?>">

		<?php
			/**
			 * woocommerce_before_subcategory_title hook
			 *
			 * @hooked woocommerce_subcategory_thumbnail - 10
			 */
			do_action( 'woocommerce_before_subcategory_title', $attribute );
		?>

		<h3>
			<?php
				echo $attribute->name;

				if ( $attribute->count > 0 )
					echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . $attribute->count . ')</mark>', $attribute );
			?>
		</h3>

		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $attribute );
		?>

	</a>

	<?php do_action( 'woocommerce_after_subcategory', $attribute ); ?>

</div>