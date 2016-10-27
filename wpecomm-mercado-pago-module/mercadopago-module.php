<?php

/**
 * Plugin Name: WPeComm Mercado Pago Module
 * Plugin URI: https://github.com/mercadopago/cart-wp-commerce
 * Description: This is the <strong>oficial</strong> module of Mercado Pago for WP-eCommerce plugin. This module enables WP-eCommerce to use Mercado Pago as a payment Gateway for purchases made in your e-commerce store.
 * Author: Mercado Pago
 * Author URI: https://www.mercadopago.com.br/developers/
 * Developer: Marcelo T. Hama (marcelo.hama@mercadolibre.com)
 * Copyright: Copyright(c) MercadoPago [https://www.mercadopago.com]
 * Version: 4.2.1
 * License: https://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * Text Domain: wpecomm-mercadopago-module
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if class is already loaded.
if ( ! class_exists( 'WPeComm_MercadoPago_Module' ) ) :

  /**
   * Summary: WPeCommerce MercadoPago Module main class.
   * Description: Used as a kind of manager to enable/disable each Mercado Pago gateway.
   * @since 4.2.0
   */
	class WPeComm_MercadoPago_Module {

		const VERSION = '4.2.1';

		// Singleton design pattern.
		protected static $instance = null;
		public static function init_mercado_pago_gateway_class() {
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		// Class constructor.
		private function __construct() {

			// Load plugin text domain.
			add_action( 'init', array( $this, 'load_plugin_textdomain_wpecomm' ) );

			// Verify if WPeCommerce is already installed.
			if ( class_exists( 'WP_eCommerce' ) ) {
				$this->recurse_copy( plugin_dir_path( __FILE__ ) .
					'wpecomm-mercado-pago-module/mercadopago-languages',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-languages'
				);
				$this->recurse_copy( plugin_dir_path( __FILE__ ) .
					'wpecomm-mercado-pago-module/mercadopago-images',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-images'
				);
				$this->recurse_copy( plugin_dir_path( __FILE__ ) .
					'wpecomm-mercado-pago-module/mercadopago-lib',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-lib'
				);
				copy(
					plugin_dir_path( __FILE__ ) . 'wpecomm-mercado-pago-module/mercadopago-basic.php',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-basic.php'
				);
				copy(
					plugin_dir_path( __FILE__ ) . 'wpecomm-mercado-pago-module/mercadopago-custom.php',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-custom.php'
				);
				copy(
					plugin_dir_path( __FILE__ ) . 'wpecomm-mercado-pago-module/mercadopago-ticket.php',
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-ticket.php'
				);
				register_deactivation_hook( __FILE__, array( $this, "on_deactivation" ) );
			} else {
				add_action( 'admin_notices', array( $this, 'notify_wp_ecommerce_miss' ) );
			}

		}

    /**
     * Summary: Custom deactivation hook.
     * Description: Custom deactivation hook.
     */
		public function on_deactivation() {

			if ( ! current_user_can( 'activate_plugins' ) )
				return;

			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-ticket.php' ) ) {
				unlink(
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-ticket.php'
				);
			}
			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-custom.php' ) ) {
				unlink(
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-custom.php'
				);
			}
			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-basic.php' ) ) {
				unlink(
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-basic.php'
				);
			}
			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-lib' ) ) {
				$this->delete_dir(
					dirname(plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-lib'
				);
			}
			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-images' ) ) {
				$this->delete_dir(
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-images'
				);
			}
			if ( file_exists( dirname( plugin_dir_path( __FILE__ ) ) .
			'/wp-e-commerce/wpsc-merchants/mercadopago-languages' ) ) {
				$this->delete_dir(
					dirname( plugin_dir_path( __FILE__ ) ) .
					'/wp-e-commerce/wpsc-merchants/mercadopago-languages'
				);
			}

			flush_rewrite_rules();

		}

    /**
     * Summary: Get preference data for a specific country.
     * Description: Get preference data for a specific country.
     * @return an array with sponsor id, country name, banner image for checkout, and currency.
     */
    public static function get_country_config( $site_id ) {
      switch ( $site_id ) {
        case 'MLA':
          return array(
            'sponsor_id' => 219693774,
            'country_name' => __( 'Argentine', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/standard_mla.jpg',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLA/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'ARS'
          );
        case 'MLB':
          return array(
            'sponsor_id' => 219691508,
            'country_name' => __( 'Brazil', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLB/standard_mlb.jpg',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLB/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'BRL'
          );
        case 'MCO':
          return array(
            'sponsor_id' => 219695429,
            'country_name' => __( 'Colombia', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MCO/standard_mco.jpg',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MCO/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'COP'
          );
        case 'MLC':
          return array(
            'sponsor_id' => 219691655,
            'country_name' => __( 'Chile', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLC/standard_mlc.gif',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLC/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'CLP'
          );
        case 'MLM':
          return array(
            'sponsor_id' => 219696864,
            'country_name' => __( 'Mexico', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLM/standard_mlm.jpg',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLM/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'MXN'
          );
        case 'MLV':
          return array(
            'sponsor_id' => 219696139,
            'country_name' => __( 'Venezuela', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLV/standard_mlv.jpg',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MLV/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'VEF'
          );
        case 'MPE':
          return array(
            'sponsor_id' => 219692012,
            'country_name' => __( 'Peru', 'wpecomm-mercadopago-module' ),
            'checkout_banner' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MPE/standard_mpe.png',
              plugin_dir_path( __FILE__ )
            ),
            'checkout_banner_custom' => plugins_url(
              'wp-e-commerce/wpsc-merchants/mercadopago-images/MPE/credit_card.png',
              plugin_dir_path( __FILE__ )
            ),
            'currency' => 'PEN'
          );
        default:
          return null;
      }
    }

    public static function build_currency_conversion_err_msg( $currency ) {
      return '<img width="12" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/error.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ' .
        __( 'ERROR: It was not possible to convert the unsupported currency', 'wpecomm-mercadopago-module' ) .
        ' ' . WPSC_Countries::get_currency_code( absint( get_option( 'currency_type' ) ) ) . ' '  .
        __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . $currency . '. ' .
        __( 'Currency conversions should be made outside this module.', 'wpecomm-mercadopago-module' );
    }

    public static function build_currency_not_converted_msg( $currency, $country_name ) {
      return '<img width="12" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/warning.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ' .
        __( 'ATTENTION: The currency', 'wpecomm-mercadopago-module' ) .
        ' ' . WPSC_Countries::get_currency_code( absint( get_option( 'currency_type' ) ) ) . ' ' .
        __( 'defined in WPeCommerce is different from the one used in your credentials country.<br>The currency for transactions in this payment method will be', 'wpecomm-mercadopago-module' ) .
        ' ' . $currency . ' (' . $country_name . '). ' .
        __( 'Currency conversions should be made outside this module.', 'wpecomm-mercadopago-module' );
    }

    public static function build_currency_converted_msg( $currency, $currency_ratio ) {
      return '<img width="12" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/check.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ' .
        __( 'CURRENCY CONVERTED: The currency conversion ratio from', 'wpecomm-mercadopago-module' )  .
        ' ' . WPSC_Countries::get_currency_code( absint( get_option( 'currency_type' ) ) ) . ' ' .
        __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . $currency .
        __( ' is: ', 'wpecomm-mercadopago-module' ) . $currency_ratio . '.';
    }

    public static function build_valid_credentials_msg( $country_name, $site_id ) {
      return '<img width="12" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/check.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ' .
        __( 'Your credentials are <strong>valid</strong> for', 'wpecomm-mercadopago-module' ) .
        ': ' . $country_name . ' <img width="18.6" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/' . $site_id . '/' . $site_id . '.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ';
    }

    public static function build_invalid_credentials_msg() {
      return '<img width="12" height="12" src="' .
        plugins_url(
          'wp-e-commerce/wpsc-merchants/mercadopago-images/error.png',
          plugin_dir_path( __FILE__ )
        ) . '"> ' .
        __( 'Your credentials are <strong>not valid</strong>!', 'wpecommcommerce-mercadopago-module' );
    }

    /**
     * Summary: Delete a directory.
     * Description: Delete a directory.
     * @return a boolean indicating if deletion was success or not
     */
		public function delete_dir( $path ) {

			if ( is_dir( $path ) === true ) {
				$files = array_diff( scandir( $path ), array( '.', '..' ) );
				foreach ( $files as $file ) {
					$this->delete_dir( realpath( $path ) . '/' . $file );
				}
				return rmdir( $path );
			} else if ( is_file( $path ) === true ) {
				return unlink( $path );
			}

			return false;

		}

    /**
     * Summary: Places a warning error to notify user that WP-eCommerce is missing.
     * Description: Places a warning error to notify user that WP-eCommerce is missing.
     */
		public function notify_wp_ecommerce_miss() {

			echo
				'<div class="error"><p>' . sprintf(
					__( 'WPeComm Mercado Pago Module depends on the last version of %s to execute!', 'wpecomm-mercadopago-module' ),
					'<a href="https://wordpress.org/extend/plugins/wp-e-commerce/">' . 'WP-eCommerce' . '</a>'
				) .
				'</p></div>';

		}

    /**
     * Summary: Multi-language plugin.
     * Description: Multi-language plugin.
     */
		public function load_plugin_textdomain_wpecomm() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'wpecomm-mercadopago-module' );
			load_textdomain(
				'wpecomm-mercadopago-module',
				trailingslashit( WP_LANG_DIR ) .
          'wpecomm-mercadopago-module/wpecomm-mercadopago-module-' .
          $locale . '.mo'
			);
			load_plugin_textdomain(
        'wpecomm-mercadopago-module',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages/'
      );

		}

    /**
     * Summary: Recursively (hierarchy) copy a directory.
     * Description: Recursively (hierarchy) copy a directory.
     */
		public function recurse_copy( $src, $dst ) {

			$dir = opendir( $src );
			@mkdir( $dst );
			while( false !== ( $file = readdir( $dir ) ) ) {
				if ( ( $file != '.' ) && ( $file != '..' ) ) {
			 		if ( is_dir( $src . '/' . $file ) ) {
						$this->recurse_copy( $src . '/' . $file,$dst . '/' . $file );
			 		} else {
						copy( $src . '/' . $file,$dst . '/' . $file );
					}
				}
			}

			closedir( $dir );

		}

    /**
     * Summary: Get store categories from Mercado Pago.
     * Description: Trigger API to get available categories and proper description.
     * @return an array with found categories and a description for its selector title.
     */
    /*public static function get_categories() {

      $store_categories_id = array();
      $store_categories_description = array();

      // Get Mercado Pago store categories.
      $categories = MPRestClient::get(
        array( 'uri' => '/item_categories' ),
        WPeComm_MercadoPago_Module::get_module_version()
      );
      foreach ( $categories['response'] as $category ) {
        array_push(
          $store_categories_id, str_replace( '_', ' ', $category['id'] )
        );
        array_push(
          $store_categories_description, str_replace( '_', ' ', $category['description'] )
        );
      }

      return array(
        'store_categories_id' => $store_categories_id,
        'store_categories_description' => $store_categories_description
      );

    }

    /**
     * Summary: Get the rate of conversion between two currencies.
     * Description: The currencies are the one used in WPeCommerce and the one used in $site_id.
     * @return a float that is the rate of conversion.
     */
    public static function get_conversion_rate( $used_currency ) {
      $currency_obj = MPRestClient::get(
        array( 'uri' => '/currency_conversions/search?' .
          'from=' . WPSC_Countries::get_currency_code( absint( get_option( 'currency_type' ) ) ) .
          '&to=' . $used_currency
        ),
        WPeComm_MercadoPago_Module::get_module_version()
      );
      if ( isset( $currency_obj['response'] ) ) {
        $currency_obj = $currency_obj['response'];
        if ( isset( $currency_obj['ratio'] ) ) {
          return ( (float) $currency_obj['ratio'] );
        }
      }
      return -1;
    }

    /**
     * Summary: Fix to URL Problem : #038; replaces & and breaks the navigation.
     * Description: Fix to URL Problem : #038; replaces & and breaks the navigation.
     * @return a string with the fixed url.
     */
    public static function workaround_ampersand_bug( $link ) {
      return str_replace( '\/', '/', str_replace( '&#038;', '&', $link) );
    }

    /**
     * Summary: Get module's version.
     * Description: Get module's version.
     * @return a string with the given version.
     */
    public static function get_module_version() {
      return WPeComm_MercadoPago_Module::VERSION;
    }

	}

  // ==========================================================================================

	add_action(
    'plugins_loaded',
    array( 'WPeComm_MercadoPago_Module', 'init_mercado_pago_gateway_class' ),
    0
  );

	// Add settings link on plugin page.
	function wpecomm_mercadopago_settings_link( $links ) {

    $option_page = 'options-general.php?page=wpsc-settings&tab=gateway&payment_gateway_id=';
		$plugin_links = array();

		$plugin_links[] = '<a href="' . esc_url( admin_url(
      $option_page . 'WPSC_Merchant_MercadoPago_Basic' ) ) . '">' .
      __( 'Basic Checkout', 'wpecomm-mercadopago-module' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url(
      $option_page . 'WPSC_Merchant_MercadoPago_Custom' ) ) . '">' .
      __( 'Custom Checkout', 'wpecomm-mercadopago-module' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url(
      $option_page . 'WPSC_Merchant_MercadoPago_Ticket' ) ) . '">' .
      __( 'Ticket', 'wpecomm-mercadopago-module' ) . '</a>';

		$plugin_links[] = '<br><a target="_blank" href="' .
			'https://wordpress.org/support/view/plugin-reviews/wpecomm-mercado-pago-module?filter=5#postform">' .
			sprintf(
        __( 'Rate Us', 'wpecomm-mercadopago-module' ) . ' %s', '&#9733;&#9733;&#9733;&#9733;&#9733;'
      ) . '</a>';
		$plugin_links[] = '<a target="_blank" href="' .
      'https://wordpress.org/support/plugin/wpecomm-mercado-pago-module#postform">' .
      __( 'Report Issue', 'wpecomm-mercadopago-module' ) . '</a>';

		return array_merge( $plugin_links, $links );

	}

	$plugin = plugin_basename( __FILE__ );
	add_filter( "plugin_action_links_$plugin", 'wpecomm_mercadopago_settings_link' );

endif;
