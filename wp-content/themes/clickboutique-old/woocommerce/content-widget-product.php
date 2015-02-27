<?php global $product; ?>
<li>
	<div class="thumb-wrapper">
		<a href="<?php echo esc_url( get_permalink( $product->id ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
			<?php echo $product->get_image($size = 'woocommerce-widget-thumb'); ?>
		</a>
	</div>

	<a class="product-title" href="<?php echo esc_url( get_permalink( $product->id ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
		<?php echo $product->get_title(); ?>
	</a>
	<?php if ( ! empty( $show_rating ) ) echo $product->get_rating_html(); ?>

	<div class="price-wrapper">
		<?php echo $product->get_price_html(); ?>
	</div>
</li>