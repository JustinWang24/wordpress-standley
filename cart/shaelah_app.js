const SHEALAH_AJAX_SUCCESS = 100;
const SHEALAH_AJAX_ERROR = 99;
var ShaelahPickupLocationsApp = new Vue({
    el:'#shaelah-pickup-locations-app',
    data: function(){
        return {
            pickupLocation:{
                postcode:'',
                area:'',
                option:'',
                country: 'Australia',
                state:'VIC',
                suburb: 'Suburb name',
                cost: 0,
                value:''
            },
            useFlatRate: false,
            showWarning: false,
            querying: false,
            pickupLocations: [], // pick up locations
            // Todo: Submit cart to server to calculate the shopping cost if possible
            cart:null,
            proceedToCheckoutLinkOrigin:''
        };
    },
    mounted: function(){
        var that = this;
        // Observe proceed to checkout button
        jQuery(document).ready(function(){
          jQuery('#shaelah-app-wrap').removeClass('hidden');
          that.proceedToCheckoutLinkOrigin = jQuery('#vue-proceed-to-checkout-button').attr('href');
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
                        }
                        that._refreshWooCommerceShippingForm();
                    }else{
                        // No result, the postcode is not a support area
                        that.showWarning = true;
                        that._fill(null);
                        that._resetWooCommerceShippingForm();
                    }
                });
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
            console.log(res);
          });
          var queryParams = '?shipping=25&area='+this.pickupLocation.area+'&pickup='+this.pickupLocation.value;
          jQuery('#vue-proceed-to-checkout-button')
              .attr('href',this.proceedToCheckoutLinkOrigin+queryParams);
        },
        _resetWooCommerceShippingForm: function(){
          jQuery("#calc_shipping_country").val('');
          jQuery("#calc_shipping_state").val('');
          jQuery("#calc_shipping_city").val('');
          jQuery("#calc_shipping_postcode").val('');
          jQuery('#proceedToCheckoutLinkOrigin').attr('href',this.proceedToCheckoutLinkOrigin);
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