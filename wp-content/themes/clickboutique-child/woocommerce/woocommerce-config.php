<?php
global $cg_options;

// Custom hook for lightbox single product
add_action( 'cg_woocommerce_single_product_summary_quickview', 'woocommerce_template_single_price', 10 );
add_action( 'cg_woocommerce_single_product_summary_quickview', 'woocommerce_template_single_excerpt', 20 );
add_action( 'cg_woocommerce_single_product_summary_quickview', 'woocommerce_template_single_add_to_cart', 30 );