<?php
/**
 * Shipping Calculator
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/shipping-calculator.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( 'no' === get_option( 'woocommerce_enable_shipping_calc' ) || ! WC()->cart->needs_shipping() ) {
	return;
}

wp_enqueue_script( 'wc-country-select' );

do_action( 'woocommerce_before_shipping_calculator' );
$jsApplicationCodes = file_get_contents(__DIR__.'/shaelah_app.js');
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
<div id="shaelah-pickup-locations-app">
    <div class="shaelah-app-wrap hidden" id="shaelah-app-wrap">
        <p v-show="useFlatRate"><b>$25 flat rate</b></p>
        <p v-show="showWarning" style="color:red;display: none;">Sorry we don’t deliver to your area. Please see our <a href="/how-it-works/">delivery areas</a>.</p>
        <p v-show="pickupLocation.value.length>0"><b>Pickup: {{ pickupLocation.value }}</b></p>
        <el-form :model="pickupLocation" class="demo-form-inline">
            <el-input class="mt-10 full-width" v-model="pickupLocation.postcode" placeholder="Insert post code to see your delivery options"></el-input>
            <el-select class="mt-10 full-width" v-model="pickupLocation.value" placeholder="Choose a pick up location" v-show="pickupLocations.length>0">
                <el-option
                        v-for="(item, idx) in pickupLocations"
                        :key="idx"
                        :label="item"
                        :value="item">
                </el-option>
            </el-select>
            <el-button class="mt-10 full-width" :loading="querying" type="primary" @click="queryPickupLocations($event)">Submit</el-button>
        </el-form>
    </div>

<form class="woocommerce-shipping-calculator" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<p><a href="#" class="shipping-calculator-button" style="display: none;">
            <?php esc_html_e( 'Calculate shipping', 'woocommerce' ); ?></a></p>

	<section class="shipping-calculator-form" style="display:none;">

		<p class="form-row form-row-wide" id="calc_shipping_country_field">
			<select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state country_select" rel="calc_shipping_state">
				<option value=""><?php esc_html_e( 'Select a country&hellip;', 'woocommerce' ); ?></option>
				<?php
				foreach ( WC()->countries->get_shipping_countries() as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
				}
				?>
			</select>
		</p>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_state', true ) ) : ?>

			<p class="form-row form-row-wide" id="calc_shipping_state_field">
				<?php
				$current_cc = WC()->customer->get_shipping_country();
				$current_r  = WC()->customer->get_shipping_state();
				$states     = WC()->countries->get_states( $current_cc );

				if ( is_array( $states ) && empty( $states ) ) {
					?>
					<input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" />
					<?php
				} elseif ( is_array( $states ) ) {
					?>
					<span>
						<select name="calc_shipping_state" class="state_select" id="calc_shipping_state" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>">
							<option value=""><?php esc_html_e( 'Select a state&hellip;', 'woocommerce' ); ?></option>
							<?php
							foreach ( $states as $ckey => $cvalue ) {
								echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . esc_html( $cvalue ) . '</option>';
							}
							?>
						</select>
					</span>
					<?php
				} else {
					?>
					<input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php esc_attr_e( 'State / County', 'woocommerce' ); ?>" name="calc_shipping_state" id="calc_shipping_state" />
					<?php
				}
				?>
			</p>

		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', true ) ) : ?>

			<p class="form-row form-row-wide" id="calc_shipping_city_field">
				<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php esc_attr_e( 'City', 'woocommerce' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
			</p>

		<?php endif; ?>

		<?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>

			<p class="form-row form-row-wide" id="calc_shipping_postcode_field">
				<input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php esc_attr_e( 'Postcode / ZIP', 'woocommerce' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
			</p>

		<?php endif; ?>

		<p><button type="submit" name="calc_shipping" value="1" class="button" id="original-calculate-shipping-button"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button></p>

		<?php wp_nonce_field( 'woocommerce-shipping-calculator', 'woocommerce-shipping-calculator-nonce' ); ?>
	</section>
</form>
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
      proceedToCheckoutButtonObject: null
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
    }
  },
  mounted: function(){
    var that = this;
    // Observe proceed to checkout button
    jQuery(document).ready(function(){
      jQuery('#shaelah-app-wrap').removeClass('hidden');
      that.proceedToCheckoutButtonObject = jQuery('#vue-proceed-to-checkout-button');
      that.proceedToCheckoutLinkOrigin = that.proceedToCheckoutButtonObject.attr('href');
      that.proceedToCheckoutButtonObject.on('click',function(evt){
        evt.preventDefault();
        if(that.proceedToCheckoutAllowed){
          window.location.href = that.proceedToCheckoutButtonObject.attr('href');
        }
      });
    });
  },
  methods: {
    checkSubmitLocation:function(e){
      e.preventDefault();
      console.log(this.postcode);
    },
    queryPickupLocations: function(event){
      event.preventDefault();
      if(this.pickupLocation.postcode > 3){
        this._handleProceedToCheckoutButtonStatus(false); // proceed disallowed, wait for response
        var that = this;
        this.querying = true;
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
            if(res.data.pickups && res.data.pickups.length > 0){
              // pickup locations
              that.pickupLocations = res.data.pickups;
            }else{
              that.pickupLocations = [];
              that.pickupLocation.value = '';
              that._handleProceedToCheckoutButtonStatus(true); // proceed allowed
            }
            that._refreshWooCommerceShippingForm();
          }else{
            // No result, the postcode is not a support area
            that.showWarning = true;
            that.useFlatRate = false;
            that._handleProceedToCheckoutButtonStatus(false); // proceed disallowed
            that._fill(null);
            that._resetWooCommerceShippingForm();
          }
        });
      }
    },
    _handleProceedToCheckoutButtonStatus: function(allowed){
      // Switch proceed to checkout button status
      this.proceedToCheckoutAllowed = allowed;
      if(allowed){
        this.proceedToCheckoutButtonObject.removeClass('disabled');
      }else{
        this.proceedToCheckoutButtonObject.addClass('disabled');
      }
    },
    _refreshWooCommerceShippingForm: function(){
      jQuery("#calc_shipping_country").val(this.pickupLocation.country);
      jQuery("#calc_shipping_state").val('VIC');
      jQuery("#calc_shipping_city").val(this.pickupLocation.area);
      jQuery("#calc_shipping_postcode").val(this.pickupLocation.postcode);
      // jQuery("#original-calculate-shipping-button").trigger('click');
      jQuery.ajax({
        url: '/cart/',
        type: "POST",
        data: {
          'calc_shipping_state': 'VIC',
          'calc_shipping_city': this.pickupLocation.area,
          'woocommerce-shipping-calculator-nonce': jQuery("#woocommerce-shipping-calculator-nonce").val(),
          '_wp_http_referer': '/cart/',
          'calc_shipping': 'x',
          'calc_shipping_postcode': this.pickupLocation.postcode
        },
        dataType: "html"
      }).done(function (res) {

      });
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
<?php
    do_action( 'woocommerce_after_shipping_calculator' );
?>
