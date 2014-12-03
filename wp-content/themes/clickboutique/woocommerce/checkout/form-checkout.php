<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if (get_option('catalog_mode') == 'on') return;

if (get_option('checkout_steps') == 'on') {
	$steps_checkout = true;
} else {
	$steps_checkout = false;
}

wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_checkout_form', $checkout ); ?>

<?php // If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', $woocommerce->cart->get_checkout_url() ); ?>


	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php if ( is_user_logged_in() && $steps_checkout ) {

    		$current_user = wp_get_current_user();

			echo '<div class="tab-pane active" id="authorization">';
			echo '<h3 class="pt-content-title">' . __( 'Welcome Back', 'plumtree' ) . '</h3>';
		    echo '<p class="logged-in-as">' .
		    sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $current_user->user_login, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) )
		    . '</p>';
            echo '<p class="form-row form-row-last step-nav">
                <span class="pt-dark-button step-checkout" data-toggle="tab" data-show="billing">'.__('Continue to Billing Address', 'plumtree').'&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></span>
            </p>';
		    echo '</div>';

		} else {?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

            <?php if ( get_option('woocommerce_enable_checkout_login_reminder') != 'yes' ) : ?>

			<div class="tab-pane active" id="authorization">
				<h3 class="pt-content-title"><?php _e( 'Fill in the required fields', 'plumtree' ); ?></h3>
			    <p class="guest-checkout"><?php _e('You may checkout as guest', 'plumtree'); ?></p>

			    <?php if ($steps_checkout) {
			    		echo '<p class="form-row form-row-last step-nav guest-step">
			                  <span class="pt-dark-button step-checkout guest" data-toggle="tab" data-show="billing">'.__('Continue to Billing Address', 'plumtree').'&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></span>
			                  </p>'; 
			    }?>

			</div>
            <?php endif; ?>
		<?php };?>

	</div><!-- login form ends -->

	<form name="checkout" method="post" id="customer_details" class="checkout <?php if ($steps_checkout) { echo "tab-content"; }; ?>" action="<?php echo esc_url( $get_checkout_url ); ?>">

		<div class="<?php if ($steps_checkout) { echo "tab-pane"; }; ?>" id="billing" >

			<?php do_action( 'woocommerce_checkout_billing' ); ?>

		</div>

		<div class="<?php if ($steps_checkout) { echo "tab-pane"; }; ?>" id="shipping">

			<?php do_action( 'woocommerce_checkout_shipping' ); ?>

		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

	<div id="payment" class="<?php if ($steps_checkout) { echo "tab-pane"; }; ?>">

		<h3 class="pt-content-title"><?php _e( 'Payment Method', 'woocommerce' ); ?></h3>

		<?php if (WC()->cart->needs_payment()) : ?>
		<ul class="payment_methods methods">
			<?php
				$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
				if ( ! empty( $available_gateways ) ) {

					// Chosen Method
					if ( isset( WC()->session->chosen_payment_method ) && isset( $available_gateways[ WC()->session->chosen_payment_method ] ) ) {
						$available_gateways[ WC()->session->chosen_payment_method ]->set_current();
					} elseif ( isset( $available_gateways[ get_option( 'woocommerce_default_gateway' ) ] ) ) {
						$available_gateways[ get_option( 'woocommerce_default_gateway' ) ]->set_current();
					} else {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li>
							<input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
							<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
							<?php
								if ( $gateway->has_fields() || $gateway->get_description() ) :
									echo '<div class="payment_box payment_method_' . $gateway->id . '" ' . ( $gateway->chosen ? '' : 'style="display:none;"' ) . '>';
									$gateway->payment_fields();
									echo '</div>';
								endif;
							?>
						</li>
						<?php
					}
				} else {

					if ( ! WC()->customer->get_country() )
						echo '<p>' . __( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) . '</p>';
					else
						echo '<p>' . __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';

				}
			?>
		</ul>
		<?php endif; ?>

		<?php if ($steps_checkout) { ?>
			<p class="form-row form-row-first step-nav">
				<span class="pt-dark-button step-checkout" data-show="shipping" data-toggle="tab"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e('Back', 'plumtree');?></span>
			</p>
			<p class="form-row form-row-last step-nav">
				<span class="pt-dark-button step-checkout" data-toggle="tab" data-show="order_review_container"><?php _e('Continue to Order Review', 'plumtree');?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></span>
			</p>
		<?php } ?>

	</div>

	<div id="order_review_container" class="<?php if ($steps_checkout) { echo "tab-pane"; }; ?>">

		<h3 class="pt-content-title" id="order_review_heading"><?php _e( 'Your order', 'woocommerce' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>

	</div>
</form>

