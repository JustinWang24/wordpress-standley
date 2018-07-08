<?php
/**
 * Created by PhpStorm.
 * User: Justin Wang
 * Date: 3/7/18
 * Time: 2:42 AM
 */
class PickupLocationSearchUtil{
    const OPTION_HOME_DELIVERY = 'Home delivery';
    const OPTION_CUSTOMER_PICKUP = 'Customer picks up from pickup point';
    /**
     * Do the searching by give postcode
     * @param $postcode
     * @return array|bool
     */
    public static function find($postcode){
        return self::_queryByPostCode($postcode);
    }

    /**
     * Fetching data from the data source
     * @param string $postcode
     * @return mixed
     */
    private static function _queryByPostCode($postcode){
    	// User smartbro geo webservice
    	$url = 'http://api.smartbro.com.au/api/geo/postcode?postcode='.$postcode.'&c=a&v=1';
    	$response = wp_remote_get($url);
    	return wp_remote_retrieve_body( $response );
    }
}