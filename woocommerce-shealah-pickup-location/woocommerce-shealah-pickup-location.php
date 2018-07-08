<?php
/**
 * @package ShaelahPickupLocationPlugin
 */

/**
 * Plugin Name: Shaelah Pickup Location lugin
 * Plugin URI: https://www.webmelbourne.com
 * Description: To customize the pickup locations for Shaelah
 * Version: 0.0.1
 * Author: Shaelah and Justin
 * Author URI: https://www.promcreative.com.au
 * License: Prom Creative Pty Ltd Only
 */

require_once __DIR__.'/JsonBuilder.php';;
require_once __DIR__.'/PickupLocationSearchUtil.php';;

add_action('wp_ajax_handle_postcode_search', 'handle_postcode_search');
add_action('wp_ajax_nopriv_handle_postcode_search', 'handle_postcode_search');

function handle_postcode_search(){
    $postcode = $_POST['postcode'];
	if($postcode){
        // Todo: Do the real search by post code
        echo PickupLocationSearchUtil::find($postcode);
    }else{
        echo JsonBuilder::Error();
    }
    die();
}