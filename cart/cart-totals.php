<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<style>
  .cart_totals{
    margin: 0 auto;
  }
  .shaelah-app-wrap{
    margin-top: 5px;
    margin-bottom: 15px;
  }
  .shaelah-app-wrap p{
    margin-bottom: 10px;
  }
  .shaelah-app-wrap .input-with-select{
    width: 100%;
  }
  .shaelah-app-wrap .demo-form-inline{
    margin: 0;
  }
  .shaelah-app-wrap .demo-form-inline .mt-10{
    margin-top: 10px;
  }
  .shaelah-app-wrap .demo-form-inline .full-width{
    width: 100%;
  }
</style>

<div class="cart_totals <?php if ( WC()->customer->has_calculated_shipping() ) echo 'calculated_shipping'; ?>" id="shaelah-pickup-locations-app">
  <div id="shaelah-app-wrap">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2><?php _e( 'Cart Totals', 'woocommerce' ); ?></h2>

	<table cellspacing="0" class="shop_table shop_table_responsive">

		<tr class="cart-subtotal">
			<th><?php _e( 'Estimated Subtotal', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

			<?php wc_cart_totals_shipping_html(); ?>

			<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>

		<?php elseif ( WC()->cart->needs_shipping() && 'yes' === get_option( 'woocommerce_enable_shipping_calc' ) ) : ?>

			<tr class="shipping">
				<th><?php _e( 'Shipping', 'woocommerce' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Shipping', 'woocommerce' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
			</tr>

		<?php endif; ?>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if ( wc_tax_enabled() && 'excl' === WC()->cart->tax_display_cart ) :
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
					? sprintf( ' <small>(' . __( 'estimated for %s', 'woocommerce' ) . ')</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
					: '';

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title( $code ); ?>">
						<th><?php echo esc_html( $tax->label ) . $estimated_text; ?></th>
						<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; ?></th>
					<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>" id="shealah-estimated-total-wrap"><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php _e( 'Estimated Total', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
          <div style="display: none;"><?php wc_cart_totals_order_total_html(); ?></div>
          ${{ currentEstimatedTotal }}.00
      </td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

  </div>
</div>

<script type="application/javascript">
    <?php
    // Get data from session
    $sessionCost = WC()->session->get('shealah_shipping_cost');
    // Save area in session
    $sessionArea = WC()->session->get('shealah_shipping_area');
    // Save pickup in session
    $sessionPickupLocationValue = WC()->session->get('shealah_shipping_pickup');
    // Save pickup in session
    $sessionPostcode = WC()->session->get('shealah_shipping_postcode');
    ?>
    const SHEALAH_AJAX_SUCCESS = 100;
    const SHEALAH_SHIPPING_COST = 25.00;
    var ShaelahPickupLocationsApp = new Vue({
      el:'#shaelah-pickup-locations-app',
      data: function(){
        return {
          pickupLocation:{
            postcode:'<?php echo $sessionPostcode?$sessionPostcode:null; ?>',
            area:'<?php echo $sessionArea?$sessionArea:null; ?>',
            option:'',
            country: 'Australia',
            state:'VIC',
            suburb: '',
            cost: <?php echo $sessionCost?$sessionCost:0; ?>,
            value:'<?php echo $sessionPickupLocationValue?$sessionPickupLocationValue:null; ?>'
          },
          useFlatRate: <?php echo $sessionCost?'true':'false'; ?>,
          showWarning: false,
          querying: false,
          pickupLocations: [], // pickup locations
          proceedToCheckoutLinkOrigin:'',
          proceedToCheckoutAllowed: false,
          proceedToCheckoutButtonObject: null,
          // Current estimated total value
          currentEstimatedTotal:0.00,
          originEstimatedTotal:0.00,
          currentEstimatedTotalObject:null
        };
      },
      watch:{
        'pickupLocation.value':function(newVal, oldVal){
          if(newVal !== oldVal){
            this._updateCheckoutLink();
          }
          if(newVal.length > 0){
            this._handleProceedToCheckoutButtonStatus(true);
          }
        },
        'proceedToCheckoutAllowed': function(allowed){
          if(allowed){
            this.proceedToCheckoutButtonObject.removeClass('disabled');
            this.currentEstimatedTotal = this.originEstimatedTotal + SHEALAH_SHIPPING_COST;
          }else{
            this.proceedToCheckoutButtonObject.addClass('disabled');
            this.currentEstimatedTotal = this.originEstimatedTotal;
          }
        }
      },
      mounted: function(){
        var that = this;
        // Observe proceed to checkout button
        jQuery(document).ready(function(){
          jQuery('#shaelah-app-wrap').removeClass('hidden');
          that.proceedToCheckoutButtonObject = jQuery('#vue-proceed-to-checkout-button');
          that.proceedToCheckoutLinkOrigin = that.proceedToCheckoutButtonObject.attr('href');
          // Watch proceed button click event
          that.proceedToCheckoutButtonObject.on('click',function(evt){
            evt.preventDefault();
            if(that.proceedToCheckoutAllowed){
              window.location.href = that.proceedToCheckoutButtonObject.attr('href');
            }
          });
          // get estimated total

          if(jQuery('.woocommerce-Price-amount:first')){
            var text = jQuery('.woocommerce-Price-amount:last').text();
            that.originEstimatedTotal = parseFloat(text.substring(1));
            that.currentEstimatedTotal = that.pickupLocation.cost + that.originEstimatedTotal;
          }

          // In case the delivery info exists
          if(that.pickupLocation.postcode.length > 0){
            that._doSearchAction(false);
          }
        });
      },
      methods: {
        queryPickupLocations: function(event){
          event.preventDefault();
          if(this.pickupLocation.postcode > 3){
            this._handleProceedToCheckoutButtonStatus(false); // proceed disallowed, wait for response
            this._doSearchAction(true);
          }
        },
        _doSearchAction: function(pleaseResetLocationValue){
          this.querying = true;
          var that = this;
          jQuery.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: "POST",
            data: {
              'action': 'handle_postcode_search',
              'postcode': this.pickupLocation.postcode
            },
            dataType: "json"
          }).done(function (res) {
            that.querying = false;
            if(res && res.error_no == SHEALAH_AJAX_SUCCESS){
              that.showWarning = false;
              that.useFlatRate = true;
              that.pickupLocation.area = res.data.region;

              if(pleaseResetLocationValue){
                that.pickupLocation.value = ''; // reset pickup location valve in every request
              }

              if(res.data.pickups && res.data.pickups.length > 0){
                // pickup locations
                that.pickupLocations = res.data.pickups;
                if(!pleaseResetLocationValue){
                  // Means it called from mounted hook
                  that._handleProceedToCheckoutButtonStatus(true); // proceed allowed
                }
              }else{
                that.pickupLocations = [];
                that._handleProceedToCheckoutButtonStatus(true); // proceed allowed
              }
              that._refreshWooCommerceShippingForm();
            }else{
              // No result, the postcode is not a support area
              that.showWarning = true;
              that.useFlatRate = false;
              that.pickupLocations = [];
              that._handleProceedToCheckoutButtonStatus(false); // proceed disallowed
              that._fill(null);
              that._resetWooCommerceShippingForm();
            }
          });
        },
        _handleProceedToCheckoutButtonStatus: function(allowed){
          // Switch proceed to checkout button status
          this.proceedToCheckoutAllowed = allowed;
        },
        _refreshWooCommerceShippingForm: function(){
          jQuery("#calc_shipping_country").val(this.pickupLocation.country);
          jQuery("#calc_shipping_state").val('VIC');
          jQuery("#calc_shipping_city").val(this.pickupLocation.area);
          jQuery("#calc_shipping_postcode").val(this.pickupLocation.postcode);
          // jQuery("#original-calculate-shipping-button").trigger('click');
          // jQuery.ajax({
          //   url: '/cart/',
          //   type: "POST",
          //   data: {
          //     'calc_shipping_state': 'VIC',
          //     'calc_shipping_city': this.pickupLocation.area,
          //     'woocommerce-shipping-calculator-nonce': jQuery("#woocommerce-shipping-calculator-nonce").val(),
          //     '_wp_http_referer': '/cart/',
          //     'calc_shipping': 'x',
          //     'calc_shipping_postcode': this.pickupLocation.postcode
          //   },
          //   dataType: "html"
          // }).done(function (res) {
          //
          // });
          this._updateCheckoutLink();
        },
        _updateCheckoutLink: function(){
          var queryParams = '?shipping=25&area='+this.pickupLocation.area
              +'&postcode='+this.pickupLocation.postcode
              +'&pickup='+this.pickupLocation.value;
          jQuery('#vue-proceed-to-checkout-button')
              .attr('href',this.proceedToCheckoutLinkOrigin+queryParams);
        },
        _resetWooCommerceShippingForm: function(){
          jQuery("#calc_shipping_country").val('');
          jQuery("#calc_shipping_state").val('');
          jQuery("#calc_shipping_city").val('');
          jQuery("#calc_shipping_postcode").val('');
          jQuery('#vue-proceed-to-checkout-button').attr('href',this.proceedToCheckoutLinkOrigin);
        },
        handleSelect: function(item){
          this._fill(item);
        },
        _fill: function(item){
          if(item){
            this.pickupLocation.postcode = item.postcode;
            this.pickupLocation.suburb = item.suburb;
            this.pickupLocation.area = item.area;
            this.pickupLocation.option = item.option;

            // Todo: Get the real cost for delivery
            this.pickupLocation.cost = item.cost;
            this.pickupLocation.value = item.value;
          }else{
            this.pickupLocation.suburb = "";
            this.pickupLocation.area = "";
            this.pickupLocation.option = "";

            // Todo: Get the real cost for delivery
            this.pickupLocation.cost = 0;
            this.pickupLocation.value = "";
          }
        }
      }
    });
</script>