<?php
/**
 * Installation related functions and actions.
 *
 * @author   Spreadr
 * @category Admin
 * @package  Spreadr/Classes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Install Class.
 */
class Spreadr_Install {



	/**
	 * Install Spreadr.
	 */



	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );


		
	}


	public static function install_actions() {
		

	}




	public static function install() {

		set_transient( 'spreadr_installing', 'yes', 5 );
		//spredr_maybe_define_constant( 'spreadr_installing', true );
		
		
		if (!get_option( 'spreadr_token')) {
		
			self::maybe_enable_setup_wizard();
		
		}
		

	}

	
	
	
	

	
	/**
	 * See if we need the wizard or not.
	 *
	 * @since 3.2.0
	 */
	private static function maybe_enable_setup_wizard() {

		if (isset($_GET['plugin']) && $_GET['plugin'] == 'spreadr-for-woocomerce/spreadr.php') {

			$user = wp_get_current_user();

			$email = $user->user_email;
			$blog_details = get_bloginfo(1);
			$admin_email = get_option( 'admin_email' ); 
			$user = get_user_by( 'email', $admin_email );
			$name = $user->display_name;


			$url = SPREADR_APP_URL.'authuser';


			$data['name'] = $name;
			$data['email'] = $admin_email;
			$data['installer_email'] = $email;
			$data['domain'] = get_option( 'siteurl' );;//$_SERVER['HTTP_HOST'];

			$response = wp_remote_post( $url, array(
				'method' => 'POST',
				'body'   => $data,
				)
			);

			if ( is_wp_error( $response ) ) {
			  	
			} else {
			 	$spreadrReply = json_decode($response['body'],true);
			 	if ($spreadrReply['status'] == true) {
			 		$spreadrToken = $spreadrReply['token'];
			 		update_option('spreadr_token',$spreadrToken, true );
			 		update_option('spreadr_token',$spreadrToken, true );
			 		update_option( 'spreadr_button_text', 'View On Amazon' ,true );
			 	}else {
			 		
			 	}

			}


			//wp_safe_redirect( admin_url( 'index.php?page=spreadr-setup' ) );
			
			}
	}

	
	
	
	
}

Spreadr_Install::init();
