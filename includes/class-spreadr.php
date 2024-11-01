<?php

/**
 * Spredr setup
 *
 * @author   Spreadr
 * @category API
 * @package  Spreadr
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Spreadr Class.
 *
 * @class SpreadrApp
 * @version	0.0.1
 */
final class SpreadrApp {

	protected static $_instance = null;
	/**
	 * SpreadrApp version.
	 *
	 * @var string
	 */
	public $version = '0.0.1';

	/**
	 * SpreadrApp Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}


	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}



	/**
	 * Hook into actions and filters.
	 *
	 */
	private function init_hooks() {

		register_activation_hook( SPREADR_PLUGIN_FILE, array( 'Spreadr_Install', 'install' ) );


	}


	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {

		include_once( SPREADR_ABSPATH . 'includes/spreadr-core-functions.php' );
		include_once( SPREADR_ABSPATH . 'includes/class-spreadr-install.php' );
		include_once( SPREADR_ABSPATH . 'includes/spreadr-update-default-settings.php');
		include_once( SPREADR_ABSPATH . 'includes/spreadr-frontend-functions.php');



	}


	private function define_constants() {

		$this->define( 'SPREADR_ABSPATH', dirname( SPREADR_PLUGIN_FILE ) . '/' );
		$this->define( 'SPREADR_APP_URL', 'https://woo.spreadr.co/' );
		$this->define( 'SPREADR_REVIEW_URL', 'https://api.spreadr.co/reviews-2/' );

	}






	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}


	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}







}
