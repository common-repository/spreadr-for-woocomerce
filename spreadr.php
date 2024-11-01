<?php 

/**
 * Plugin Name: Spreadr Woocommerce Plugin - Amazon Importer for Dropshipping and Affiliate
 * Plugin URI: https://spreadr.co/woocommerce
 * Description: Use Spreadr Plugin to import products from Amazon to your WooCommerce store. Earn commissions via Amazon Affiliate Program or run your dropshipping business.
 * Version: 1.0.1
 * Author: spreadr
 * Author URI: https://spreadr.co
 * Requires at least: 4.4
 * Tested up to: 5.3
 * WC requires at least: 2.2
 * WC tested up to: 5.3
 * @package Spreadr
 * @category Products
 * @author spreadr
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}


/**
 * Check if WooCommerce is active
 **/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
   


// Define WC_PLUGIN_FILE.
if ( ! defined( 'SPREADR_PLUGIN_FILE' ) ) {
  define( 'SPREADR_PLUGIN_FILE', __FILE__ );
}

// Include the main SpredrApp class.
if ( ! class_exists( 'SpreadrApp' ) ) {
  include_once dirname( __FILE__ ) . '/includes/class-spreadr.php';
}








register_uninstall_hook( __FILE__, 'spreadr_plugin_uninstall' );


function spreadr_plugin_uninstall(){

$url = SPREADR_APP_URL.'uninstall-plugin';

$data['spreadr_token'] = get_option( 'spreadr_token');
$data['email'] =  get_option( 'admin_email' );
           

           $response = wp_remote_post( $url, array(
               'method' => 'POST',
               'body'   => $data,
               )
           );


}

add_filter( 'plugin_row_meta', 'spreadr_plugin_row_meta', 10, 2 );
 
function spreadr_plugin_row_meta( $links, $file ) {


    
    if ( plugin_basename( __FILE__ ) == $file ) {
      unset($links[2]);

      if (get_option( 'spreadr_token') != "") {
        $spreadr_token =  get_option( 'spreadr_token');
        $spreadrDashboardUrl = SPREADR_APP_URL."verifyuser/".$spreadr_token;
         $row_meta = array(
              'spreadrdashboard'    => '<a href="' . esc_url( $spreadrDashboardUrl ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" style="color:green;">' . esc_html__( 'Dashboard', 'domain' ) . '</a>'
            );

         return array_merge( $links, $row_meta );

      }

      
 
        
    }
    return (array) $links;
}


SpreadrApp::instance();



}

function sanitize_text_or_array_field_spreadr($array_or_string) {
    if( is_string($array_or_string) ){
        $array_or_string = sanitize_text_field($array_or_string);
    }elseif( is_array($array_or_string) ){
        foreach ( $array_or_string as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = sanitize_text_or_array_field_spreadr($value);
            }
            else {
                $value = sanitize_text_field( $value );
            }
        }
    }

    return $array_or_string;
}


  
