<?php

/**
 * Plugin Name: eCommerce Product Catalog for WordPress
 * Plugin URI: https://implecode.com/wordpress/product-catalog/#cam=in-plugin-urls&key=plugin-url
 * Description: WordPress eCommerce easy to use, powerful and beautiful plugin from impleCode. Great choice if you want to sell easy and quick. Or just beautifully present your products on WordPress website. Full WordPress integration does great job not only for Merchants but also for Developers and Theme Constructors.
 * Version: 2.7.14
 * Author: impleCode
 * Author URI: https://implecode.com/#cam=in-plugin-urls&key=author-url
 * Text Domain: ecommerce-product-catalog
 * Domain Path: /lang/

  Copyright: 2017 impleCode.
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( 'eCommerce_Product_Catalog' ) ) {

	/**
	 * Main eCommerce_Product_Catalog Class
	 *
	 * @since 2.4.7
	 */
	final class eCommerce_Product_Catalog {

		/**
		 * @var eCommerce_Product_Catalog The one true eCommerce_Product_Catalog
		 * @since 2.4.7
		 */
		private static $instance;

		/**
		 * Main eCommerce_Product_Catalog Instance
		 *
		 * Insures that only one instance of eCommerce_Product_Catalog exists in memory at any one
		 * time.
		 *
		 * @since 2.4.7
		 * @return type
		 */
		public static function instance() {
			if ( !isset( self::$instance ) && !( self::$instance instanceof eCommerce_Product_Catalog ) ) {
				self::$instance = new eCommerce_Product_Catalog;
				self::$instance->setup_constants();

				//add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				//add_action( 'plugins_loaded', array( self::$instance, 'implecode_addons' ), 30 );
				add_action( 'admin_enqueue_scripts', array( self::$instance, 'implecode_run_admin_styles' ) );
				add_action( 'init', array( self::$instance, 'implecode_register_styles' ) );
				add_action( 'admin_init', array( self::$instance, 'implecode_register_admin_styles' ) );
				add_action( 'wp_enqueue_scripts', array( self::$instance, 'implecode_enqueue_styles' ), 9 );

				self::$instance->includes();

				//register_activation_hook( __FILE__, 'add_product_caps' );
				//register_activation_hook( __FILE__, 'epc_activation_function' );
				self::$instance->load_textdomain();
				self::$instance->implecode_addons();
				do_action( 'ic_epc_loaded' );
			}
			return self::$instance;
		}

		/**
		 * Disable cloning
		 *
		 *
		 * @since 2.4.7
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ecommerce-product-catalog' ), '4.3' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 2.4.7
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ecommerce-product-catalog' ), '4.3' );
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 2.4.7
		 * @return void
		 */
		private function setup_constants() {
			if ( !defined( 'AL_BASE_PATH' ) ) {
				define( 'AL_BASE_PATH', dirname( __FILE__ ) );
			}
			if ( !defined( 'AL_PLUGIN_BASE_PATH' ) ) {
				define( 'AL_PLUGIN_BASE_PATH', plugins_url( '/', __FILE__ ) );
			}
			if ( !defined( 'AL_PLUGIN_MAIN_FILE' ) ) {
				define( 'AL_PLUGIN_MAIN_FILE', __FILE__ );
			}
			if ( !defined( 'AL_BASE_TEMPLATES_PATH' ) ) {
				define( 'AL_BASE_TEMPLATES_PATH', dirname( __FILE__ ) );
			}
			if ( !defined( 'IC_EPC_VERSION' ) ) {
				define( 'IC_EPC_VERSION', '2.7.13' );
			}
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 2.4.7
		 * @return void
		 */
		private function includes() {
			require_once(AL_BASE_PATH . '/functions/activation.php' );
			require_once(AL_BASE_PATH . '/templates.php' );

			require_once(AL_BASE_PATH . '/functions/index.php' );
			require_once(AL_BASE_PATH . '/includes/index.php' );
			require_once(AL_BASE_PATH . '/includes/product-settings.php' );
			require_once(AL_BASE_PATH . '/functions/base.php' );
			require_once(AL_BASE_PATH . '/functions/capabilities.php' );
			require_once(AL_BASE_PATH . '/functions/functions.php' );
			require_once(AL_BASE_PATH . '/theme-product_adder_support.php' );
			require_once(AL_BASE_PATH . '/config/const.php' );

			require_once(AL_BASE_PATH . '/functions/shortcodes.php' );
			require_once(AL_BASE_PATH . '/ext-comp/index.php' );

			require_once(AL_BASE_PATH . '/modules/index.php' );
		}

		/**
		 * Registers catalog styles and scripts
		 */
		public function implecode_register_styles() {
			wp_register_style( 'al_product_styles', AL_PLUGIN_BASE_PATH . 'css/al_product.min.css' . ic_filemtime( AL_BASE_PATH . '/css/al_product.min.css' ), array( 'dashicons' ) );
			do_action( 'register_catalog_styles' );
			wp_register_script( 'ic-integration', AL_PLUGIN_BASE_PATH . 'js/integration-script.min.js' . ic_filemtime( AL_BASE_PATH . '/js/integration-script.min.js' ), array( 'al_product_scripts', 'jquery-ui-tooltip' ) );
			wp_register_style( 'ic_chosen', AL_PLUGIN_BASE_PATH . 'js/chosen/chosen.css' . ic_filemtime( AL_BASE_PATH . '/js/chosen/chosen.css' ) );
			wp_register_style( 'al_product_admin_styles', AL_PLUGIN_BASE_PATH . 'css/al_product-admin.min.css' . ic_filemtime( AL_BASE_PATH . '/css/al_product-admin.min.css' ), array( 'wp-color-picker', 'ic_chosen' ) );
		}

		/**
		 * Adds catalog admin styles and scripts
		 *
		 */
		public function implecode_run_admin_styles() {
			wp_enqueue_style( 'al_product_styles' );
			wp_enqueue_style( 'al_product_admin_styles' );
			do_action( 'enqueue_catalog_admin_styles' );
			if ( is_ic_admin_page() ) {
				wp_enqueue_script( 'al_product_admin-scripts' );
				do_action( 'enqueue_catalog_admin_scripts' );
			}
		}

		/**
		 * Registers catalog admin styles and scripts
		 */
		public function implecode_register_admin_styles() {
			wp_register_style( 'ic_chosen', AL_PLUGIN_BASE_PATH . 'js/chosen/chosen.css' . ic_filemtime( AL_BASE_PATH . '/js/chosen/chosen.css' ) );
			wp_register_style( 'al_product_admin_styles', AL_PLUGIN_BASE_PATH . 'css/al_product-admin.min.css' . ic_filemtime( AL_BASE_PATH . '/css/al_product-admin.min.css' ), array( 'wp-color-picker', 'ic_chosen' ) );
			wp_register_script( 'jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js', array( 'jquery' ) );
			wp_register_script( 'jquery-validate-add', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.min.js', array( 'jquery-validate' ) );
			wp_register_script( 'ic_chosen', AL_PLUGIN_BASE_PATH . 'js/chosen/chosen.jquery.js' . ic_filemtime( AL_BASE_PATH . '/js/chosen/chosen.jquery.js' ), array( 'jquery' ) );
			wp_register_script( 'al_product_admin-scripts', AL_PLUGIN_BASE_PATH . 'js/admin-scripts.min.js' . ic_filemtime( AL_BASE_PATH . '/js/admin-scripts.min.js' ), array( 'jquery-ui-sortable', 'jquery-ui-tooltip', 'jquery-validate-add', 'wp-color-picker', 'jquery-ui-autocomplete', 'ic_chosen' ) );
			do_action( 'register_catalog_admin_styles' );
		}

		/**
		 * Adds catalog front-end styles and scripts
		 *
		 */
		public function implecode_enqueue_styles() {
			wp_enqueue_style( 'al_product_styles' );
			$colorbox_set = json_decode( apply_filters( 'colorbox_set', '{"transition": "elastic", "initialWidth": 200, "maxWidth": "90%", "maxHeight": "90%", "rel":"gal"}' ) );
			wp_localize_script( 'al_product_scripts', 'product_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'lightbox_settings' => $colorbox_set ) );
			wp_enqueue_script( 'al_product_scripts' );
			if ( is_ic_integration_wizard_page() ) {
				wp_enqueue_style( 'al_product_admin_styles' );
				wp_enqueue_script( 'ic-integration' );
			}
			do_action( 'enqueue_catalog_scripts' );
		}

		/**
		 * Loads Plugin textdomain
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'ecommerce-product-catalog', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Adds support for impleCode Addons
		 */
		public function implecode_addons() {
			if ( !is_network_admin() ) {
				do_action( 'ecommerce-prodct-catalog-addons' );
			}
		}

	}

} // End if class_exists check

if ( !function_exists( 'impleCode_EPC' ) ) {

	add_action( 'plugins_loaded', 'impleCode_EPC', -2 );

	/**
	 * The main function responsible for returning eCommerce_Product_Catalog
	 *
	 * @since 2.4.7
	 * @return type
	 */
	function impleCode_EPC() {
		return eCommerce_Product_Catalog::instance();
	}

}


if ( !function_exists( 'IC_EPC_install' ) ) {

	register_activation_hook( __FILE__, 'IC_EPC_install' );

	function IC_EPC_install() {
		eCommerce_Product_Catalog::instance();
		update_option( 'IC_EPC_install', 1 );
		epc_activation_function();
	}

}
// Get impleCode_EPC Running
//impleCode_EPC();
