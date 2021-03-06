<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Add by Justin Wang
 */
$shipping = '';
if(isset($_GET['shipping'])){
    if(isset($_GET['shipping'])){
        $shipping .= '$'.$_GET['shipping'].'.00';
    }
    if(isset($_GET['area'])){
//        $shipping .= ' ('.ucwords($_GET['area']).').';
    }
    if(isset($_GET['pickup']) && strlen(trim($_GET['pickup']))>0){
        $shipping .= ' (Pickup location: '.$_GET['pickup'].')';
    }
    // Save shopping cost description in session
    WC()->session->set('shealah_shipping_msg',$shipping);
    // Save shopping cost in session
    WC()->session->set('shealah_shipping_cost',$_GET['shipping']);
    // Save area in session
    WC()->session->set('shealah_shipping_area',$_GET['area']);
    // Save pickup in session
    WC()->session->set('shealah_shipping_pickup',$_GET['pickup']);
    // Save postcode in session
    WC()->session->set('shealah_shipping_postcode',$_GET['postcode']);
}else{
    // No valid shipping area is submitted
    // Save shopping cost description in session
//    WC()->session->set('shealah_shipping_msg',null);
//    // Save shopping cost in session
//    WC()->session->set('shealah_shipping_cost',null);
//    // Save area in session
//    WC()->session->set('shealah_shipping_area',null);
//    // Save pickup in session
//    WC()->session->set('shealah_shipping_pickup',null);
//    // Save postcode in session
//    WC()->session->set('shealah_shipping_postcode',null);
//    $shipping = WC()->session->get('shealah_shipping_msg');
    $shipping = WC()->session->get('shealah_shipping_msg');
//    $shipping = 'Sorry we don’t deliver to your area, here are <a href="/how-it-works/">our shipping area</a>';
}
/**
 * Add by Justin Wang end
 */
?>
<tr class="shipping">
    <th><?php echo wp_kses_post( $package_name ); ?></th>
    <td data-title="<?php echo esc_attr( $package_name ); ?>">
        <?php if ( 1 < count( $available_methods ) ) : ?>
            <ul id="shipping_method">
                <?php foreach ( $available_methods as $method ) : ?>
                    <li>
                        <?php
                        printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
								<label for="shipping_method_%1$d_%2$s">%5$s</label>',
                            $index, sanitize_title( $method->id ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ), wc_cart_totals_shipping_method_label( $method ) );

                        do_action( 'woocommerce_after_shipping_rate', $method, $index );
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ( 1 === count( $available_methods ) ) :  ?>
            <?php
            $method = current( $available_methods );
            printf( '%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr( $method->id ), wc_cart_totals_shipping_method_label( $method ) );
            do_action( 'woocommerce_after_shipping_rate', $method, $index );
            ?>
        <?php elseif ( WC()->customer->has_calculated_shipping() ) : ?>
            <?php
//            if ( is_cart() ) {
//                echo apply_filters( 'woocommerce_cart_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
//            } else {
//                echo apply_filters( 'woocommerce_no_shipping_available_html', wpautop( __( 'There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce' ) ) );
//            }
            ?>
        <?php elseif ( ! is_cart() ) : ?>
            <?php
                // Means it not the cart view
//                echo wpautop( __( 'Enter your full address to see shipping costs.', 'woocommerce' ) );
                echo $shipping;
            ?>
        <?php endif; ?>

        <?php if ( $show_package_details ) : ?>
            <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html( $package_details ) . '</small></p>'; ?>
        <?php endif; ?>

        <?php if ( ! empty( $show_shipping_calculator ) ) : ?>
            <?php woocommerce_shipping_calculator(); ?>
        <?php endif; ?>
    </td>
</tr>
