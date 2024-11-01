<?php
/**
 * Spreadr Update Default Settings Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author 		Spreadr
 * @category 	Core
 * @package 	Spreadr/Functions
 * @version     0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_spreadr_update_default_settings', 'spreadr_update_default_settings' );
add_action( 'wp_ajax_nopriv_spreadr_update_default_settings', 'spreadr_update_default_settings' );



function spreadr_update_default_settings() {


	$button_text 	= sanitize_text_or_array_field_spreadr($_POST['button_text']);
	$button_type 	= sanitize_text_or_array_field_spreadr($_POST['button_type']);

	update_option( 'spreadr_button_text', $button_text ,true );
	update_option( 'spreadr_button_type', $button_type ,true );

	die('done');
exit();
}

add_action( 'wp_ajax_nopriv_spreadr_update_user_settings', 'spreadr_update_user_settings' );



function spreadr_update_user_settings() {


	if (isset($_POST['is_analytics_on'])) {

			$isAnalytics   = sanitize_text_or_array_field_spreadr($_POST['is_analytics_on']) ;
			update_option( 'spreadr_is_analytics', $isAnalytics ,true );
	}

	if (isset($_POST['is_localize_on'])) {
			$isGeolocalize = sanitize_text_or_array_field_spreadr($_POST['is_localize_on']);
			update_option( 'spreadr_geo_localize', $isGeolocalize ,true );
	}

	if (isset($_POST['is_facebook_pixel'])) {
			$isFacebookPixel = sanitize_text_or_array_field_spreadr($_POST['is_facebook_pixel']);
		update_option( 'is_facebook_pixel', $isFacebookPixel ,true );
	}

	if (isset($_POST['spreadr_exit_popup'])) {
		update_option( 'spreadr_exit_popup',  stripslashes(sanitize_text_or_array_field_spreadr($_POST['spreadr_exit_popup'])) ,true );
	}


	if (isset($_POST['spreadr_token'])) {
		$spreadr_token = sanitize_text_or_array_field_spreadr($_POST['spreadr_token']);
		update_option( 'spreadr_token',  $spreadr_token ,true );
	}

die('done');
exit();
}
add_action( 'wp_ajax_nopriv_spreadr_update_custom_code', 'spreadr_update_custom_code' );
function spreadr_update_custom_code() {

	if( isset( $_POST['spreadr_custom_button_type'] ) ) {

		update_option( 'spreadr_custom_button_type', sanitize_text_or_array_field_spreadr($_POST['spreadr_custom_button_type']) ,true );
	}
	
	if( isset( $_POST['spreadr_custom_single_page'] ) ) {
		update_option( 'spreadr_custom_single_page',sanitize_text_or_array_field_spreadr ($_POST['spreadr_custom_single_page']) ,true );
	}
	
	if( isset( $_POST['spreadr_custom_collection_page'] ) ) {

		update_option( 'spreadr_custom_collection_page',sanitize_text_or_array_field_spreadr($_POST['spreadr_custom_collection_page']) ,true );
	}

	if( isset( $_POST['spreadr_tags'] ) ) {

		update_option( 'spreadr_tags',sanitize_text_or_array_field_spreadr($_POST['spreadr_tags']) ,true );
	}
	//
	die('done');
	exit();
}

add_action( 'wp_ajax_nopriv_spreadr_update_product_button_type', 'spreadr_update_product_button_type' );

function spreadr_update_product_button_type() {

	if( isset( $_POST['product_id'] ) && isset( $_POST['button_type'] ) ) {
		update_post_meta( sanitize_text_or_array_field_spreadr($_POST['product_id']), 'spreadr_product_button_type', sanitize_text_or_array_field_spreadr($_POST['button_type']) );
		if($_POST['button_type'] == 0){
			wp_set_object_terms( sanitize_text_or_array_field_spreadr($_POST['product_id']), 'external', 'product_type' );
		}
		
	}
	

	die('done');
	exit();
}

add_action( 'wp_ajax_nopriv_spreadr_update_product_meta', 'spreadr_update_product_meta' );
add_action( 'wp_ajax_nopriv_spreadr_update_product_meta', 'spreadr_update_product_meta' );

function spreadr_update_product_meta() {

	if( isset( $_POST['product'] )  ) {

		$product = sanitize_text_or_array_field_spreadr($_POST['product']);
		$post_id = $product['product_id'];
		$product_meta = $product['metafields'];
		
		foreach ($product_meta as $metakey => $meta) {
				update_post_meta( $post_id,$meta['key'], $meta['value'] );
				$meta = array();
		}

	}

	die('done');
	exit();
}
