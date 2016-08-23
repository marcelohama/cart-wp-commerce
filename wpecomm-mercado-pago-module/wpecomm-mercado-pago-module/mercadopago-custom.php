<?php

/**
 * Part of WPeComm Mercado Pago Module
 * Author - Mercado Pago
 * Developer - Marcelo T. Hama (marcelo.hama@mercadolibre.com)
 * Copyright - Copyright(c) MercadoPago [http://www.mercadopago.com]
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * Text Domain: wpecomm-mercadopago-module
 * Domain Path: /mercadopago-languages/
 */


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

include_once "mercadopago-lib/mercadopago.php";

$nzshpcrt_gateways[$num] = array(
	'name' =>  __( 'Mercado Pago - Credit Card', 'wpecomm-mercadopago-module' ),
	'class_name' => 'WPSC_Merchant_MercadoPago_Custom',
	'display_name' => __( 'Mercado Pago - Credit Card', 'wpecomm-mercadopago-module' ),
	'requirements' => array(
		/// so that you can restrict merchant modules to PHP 5, if you use PHP 5 features
		'php_version' => 5.6,
		 /// for modules that may not be present, like curl
		'extra_modules' => array()
	),
	'internalname' => 'WPSC_Merchant_MercadoPago_Custom',
	// All array members below here are legacy, and use the code in mercadopago_multiple.php
	'form' => 'form_mercadopago_custom',
	'function' => 'function_mercadopago_custom',
	'submit_function' => 'submit_mercadopago_custom',
	'payment_type' => 'mercadopago'
);

class WPSC_Merchant_MercadoPago_Custom extends wpsc_merchant {

	function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain_wpecomm' ) );
	}

	// Multi-language plugin
	function load_plugin_textdomain_wpecomm() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wpecomm-mercadopago-module' );
		load_textdomain(
			'wpecomm-mercadopago-module',
			trailingslashit(WP_LANG_DIR ) . 'mercadopago-languages/wpecomm-mercadopago-module-' . $locale . '.mo'
		);
		load_plugin_textdomain( 'wpecomm-mercadopago-module', false, dirname( plugin_basename( __FILE__ ) ) . '/mercadopago-languages/' );
	}

}

/**
 * Saving of Mercado Pago Custom Checkout Settings
 * @access public
 *
 * @since 4.0
 */
function submit_mercadopago_custom() {
	if (isset($_POST['mercadopago_custom_publickey'])) {
		update_option('mercadopago_custom_publickey', trim($_POST['mercadopago_custom_publickey']));
	}
	if (isset($_POST['mercadopago_custom_accesstoken'])) {
		update_option('mercadopago_custom_accesstoken', trim($_POST['mercadopago_custom_accesstoken']));
	}
	if (isset($_POST['mercadopago_custom_siteid'])) {
		update_option('mercadopago_custom_siteid', trim($_POST['mercadopago_custom_siteid']));
	}
	// TODO: find a better way to pass translated fields to customer view
	/*if (isset($_POST['mercadopago_custom_checkoutmessage1'])) {
		update_option('mercadopago_custom_checkoutmessage1', trim($_POST['mercadopago_custom_checkoutmessage1']));
	}
	if (isset($_POST['mercadopago_custom_checkoutmessage2'])) {
		update_option('mercadopago_custom_checkoutmessage2', trim($_POST['mercadopago_custom_checkoutmessage2']));
	}
	if (isset($_POST['mercadopago_custom_checkoutmessage3'])) {
		update_option('mercadopago_custom_checkoutmessage3', trim($_POST['mercadopago_custom_checkoutmessage3']));
	}
	if (isset($_POST['mercadopago_custom_checkoutmessage4'])) {
		update_option('mercadopago_custom_checkoutmessage4', trim($_POST['mercadopago_custom_checkoutmessage4']));
	}
	if (isset($_POST['mercadopago_custom_checkoutmessage5'])) {
		update_option('mercadopago_custom_checkoutmessage5', trim($_POST['mercadopago_custom_checkoutmessage5']));
	}
	if (isset($_POST['mercadopago_custom_checkoutmessage6'])) {
		update_option('mercadopago_custom_checkoutmessage6', trim($_POST['mercadopago_custom_checkoutmessage6']));
	}*/
	if (isset($_POST['mercadopago_custom_istestuser'])) {
		update_option('mercadopago_custom_istestuser', trim($_POST['mercadopago_custom_istestuser']));
	}
	if (isset($_POST['mercadopago_custom_currencyratio'])) {
		update_option('mercadopago_custom_currencyratio', trim($_POST['mercadopago_custom_currencyratio']));
	}
	if (isset($_POST['mercadopago_custom_description'])) {
		update_option('mercadopago_custom_description', trim($_POST['mercadopago_custom_description']));
	}
	if (isset($_POST['mercadopago_custom_statementdescriptor'])) {
		update_option('mercadopago_custom_statementdescriptor', trim($_POST['mercadopago_custom_statementdescriptor']));
	}
	if (isset($_POST['mercadopago_custom_coupom'])) {
		update_option('mercadopago_custom_coupom', trim($_POST['mercadopago_custom_coupom']));
	}
	if (isset($_POST['mercadopago_custom_binary'])) {
		update_option('mercadopago_custom_binary', trim($_POST['mercadopago_custom_binary']));
	}
	if (isset($_POST['mercadopago_custom_category'])) {
		update_option('mercadopago_custom_category', trim($_POST['mercadopago_custom_category']));
	}
	if (isset($_POST['mercadopago_custom_invoiceprefix'])) {
		update_option('mercadopago_custom_invoiceprefix', trim($_POST['mercadopago_custom_invoiceprefix']));
	}
	if (isset($_POST['mercadopago_custom_currencyconversion'])) {
		update_option('mercadopago_custom_currencyconversion', trim($_POST['mercadopago_custom_currencyconversion']));
	}
	if (isset($_POST['mercadopago_custom_url_sucess'])) {
		update_option('mercadopago_custom_url_sucess', trim($_POST['mercadopago_custom_url_sucess']));
	}
	if (isset($_POST['mercadopago_custom_url_pending'])) {
		update_option('mercadopago_custom_url_pending', trim($_POST['mercadopago_custom_url_pending']));
	}
	if (isset($_POST['mercadopago_custom_sandbox'])) {
		update_option('mercadopago_custom_sandbox', trim($_POST['mercadopago_custom_sandbox']));
	}
	if (isset($_POST['mercadopago_custom_debug'])) {
		update_option('mercadopago_custom_debug', trim($_POST['mercadopago_custom_debug']));
	}
	return true;
}

/**
 * Form Custom Checkout Returns the Settings Form Fields
 * @access public
 *
 * @since 4.0
 * @return $output string containing Form Fields
 */
function form_mercadopago_custom() {
	global $wpdb, $wpsc_gateways;

	$result = validateCredentials_custom(
		get_option('mercadopago_custom_publickey'),
		get_option('mercadopago_custom_accesstoken')
	);
	$store_currency = WPSC_Countries::get_currency_code(absint(get_option('currency_type')));

	// Trigger API to get payment methods and site_id, also validates Client_id/Client_secret.
	$currency_message = "";
	if ($result['is_valid'] == true) {
		try {
			// checking the currency
			if (!isSupportedCurrency_custom($result['site_id'])) {
				if (get_option('mercadopago_custom_currencyconversion') == 'inactive') {
					$result['currency_ratio'] = -1;
					$currency_message .= '<img width="12" height="12" src="' .
						plugins_url( 'wpsc-merchants/mercadopago-images/warning.png', plugin_dir_path( __FILE__ ) ) . '">' .
						' ' . __( 'ATTENTION: The currency', 'wpecomm-mercadopago-module' ) . ' ' . $store_currency .
						' ' . __( 'defined in WPeCommerce is different from the one used in your credentials country.<br>The currency for transactions in this payment method will be', 'wpecomm-mercadopago-module' ) .
						' ' . getCurrencyId( $result['site_id'] ) . ' (' . getCountryName( $result['site_id'] ) . ').' .
						' ' . __( 'Currency conversions should be made outside this module.', 'wpecomm-mercadopago-module' );
				} else if (get_option('mercadopago_custom_currencyconversion') == 'active' && $result['currency_ratio'] != -1 ) {
					$currency_message .= '<img width="12" height="12" src="' .
						plugins_url( 'wpsc-merchants/mercadopago-images/check.png', plugin_dir_path( __FILE__ ) ) . '">' .
						' ' . __( 'CURRENCY CONVERTED: The currency conversion ratio from', 'wpecomm-mercadopago-module' )  . ' ' . $store_currency .
						' ' . __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . getCurrencyId( $result['site_id'] ) . __( ' is: ', 'wpecomm-mercadopago-module' ) . $result['currency_ratio'] . ".";
				} else {
					$result['currency_ratio'] = -1;
					$currency_message .= '<img width="12" height="12" src="' .
						plugins_url( 'wpsc-merchants/mercadopago-images/error.png', plugin_dir_path( __FILE__ ) ) . '">' .
						' ' . __( 'ERROR: It was not possible to convert the unsupported currency', 'wpecomm-mercadopago-module' ) . ' ' . $store_currency .
						' '	. __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . getCurrencyId( $result['site_id'] ) . '.' .
						' ' . __( 'Currency conversions should be made outside this module.', 'wpecomm-mercadopago-module' );
				}
			} else {
				$result['currency_ratio'] = -1;
			}
			$credentials_message = '<img width="12" height="12" src="' .
				plugins_url( 'wpsc-merchants/mercadopago-images/check.png', plugin_dir_path( __FILE__ ) ) . '">' .
				' ' . __( 'Your credentials are <strong>valid</strong> for', 'wpecomm-mercadopago-module' ) .
				': ' . getCountryName_custom( $result['site_id'] ) . ' <img width="18.6" height="12" src="' .
				plugins_url( 'wpsc-merchants/mercadopago-images/' . $result['site_id'] . '/' . $result['site_id'] . '.png', plugin_dir_path( __FILE__ ) ) . '"> ';
		} catch ( MercadoPagoException $e ) {
			$credentials_message = '<img width="12" height="12" src="' .
				plugins_url( 'wpsc-merchants/mercadopago-images/error.png', plugin_dir_path( __FILE__ ) ) . '">' .
				' ' . __( 'Your credentials are <strong>not valid</strong>!', 'wpecomm-mercadopago-module' );
		}
	} else {
		$credentials_message = '<img width="12" height="12" src="' .
			plugins_url( 'wpsc-merchants/mercadopago-images/error.png', plugin_dir_path( __FILE__ ) ) . '">' .
			' ' . __( 'Your credentials are <strong>not valid</strong>!', 'wpecomm-mercadopago-module' );
	}

	$api_secret_locale = sprintf(
		'<a href="https://www.mercadopago.com/mla/account/credentials?type=custom" target="_blank">%s</a>, ' .
		'<a href="https://www.mercadopago.com/mlb/account/credentials?type=custom" target="_blank">%s</a>, ' .
		'<a href="https://www.mercadopago.com/mlc/account/credentials?type=custom" target="_blank">%s</a>, ' .
		'<a href="https://www.mercadopago.com/mco/account/credentials?type=custom" target="_blank">%s</a>, ' .
		'<a href="https://www.mercadopago.com/mlm/account/credentials?type=custom" target="_blank">%s</a>, ' .
		'<a href="https://www.mercadopago.com/mpe/account/credentials?type=custom" target="_blank">%s</a> %s ' .
		'<a href="https://www.mercadopago.com/mlv/account/credentials?type=custom" target="_blank">%s</a>',
		__( 'Argentine', 'wpecomm-mercadopago-module' ),
		__( 'Brazil', 'wpecomm-mercadopago-module' ),
		__( 'Chile', 'wpecomm-mercadopago-module' ),
		__( 'Colombia', 'wpecomm-mercadopago-module' ),
		__( 'Mexico', 'wpecomm-mercadopago-module' ),
		__( 'Peru', 'wpecomm-mercadopago-module' ),
		__( 'or', 'wpecomm-mercadopago-module' ),
		__( 'Venezuela', 'wpecomm-mercadopago-module' )
	);
	// Get callbacks
	if (get_option('mercadopago_custom_url_sucess') != '') {
		$url_sucess = get_option('mercadopago_custom_url_sucess');
	} else {
		$url_sucess = get_site_url();
	}
	if (get_option('mercadopago_custom_url_pending') != '') {
		$url_pending = get_option('mercadopago_custom_url_pending');
	} else {
		$url_pending = get_site_url();
	}

	// send output to generate settings page
	$output = "
	<tr>
		<td></td>
		<td><h3><strong>" . __('Mercado Pago Credentials', 'wpecomm-mercadopago-module' ) . "</strong></h3></td>
	</tr>
	<tr>
		<td>
			<img width='200' height='52' src='" .
				plugins_url( 'wpsc-merchants/mercadopago-images/mplogo.png', plugin_dir_path( __FILE__ ) ) .
			"'>
		</td>
		<td>
			<input type='hidden' size='60' value='" . $result['site_id'] . "' name='mercadopago_custom_siteid' />
			<input type='hidden' size='60' value='" . $result['is_test_user'] . "' name='mercadopago_custom_istestuser' />
			<input type='hidden' size='60' value='" . $result['currency_ratio'] . "' name='mercadopago_custom_currencyratio' />
			<p><a href='https://wordpress.org/support/view/plugin-reviews/wpecomm-mercado-pago-module?filter=5#postform' target='_blank' class='button button-primary'>" . sprintf(
					__( 'Please, rate us %s on WordPress.org and give your feedback to help improve this module!', 'wpecomm-mercadopago-module' ),
					'&#9733;&#9733;&#9733;&#9733;&#9733;'
					) . "
			</a></p><br>
			<p class='description'>" .
				sprintf( '%s', $credentials_message ) . '<br>' . sprintf(
					__( 'You can obtain your credentials for', 'wpecomm-mercadopago-module' ) . ' %s.',
					$api_secret_locale ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Public Key', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='text' size='60' value='" . get_option('mercadopago_custom_publickey') . "' name='mercadopago_custom_publickey' />
			<p class='description'>
				" . __( "Insert your Mercado Pago Public Key.", 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Access Token', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='text' size='60' value='" . get_option('mercadopago_custom_accesstoken') . "' name='mercadopago_custom_accesstoken' />
			<p class='description'>
				" . __( "Insert your Mercado Pago Access Token.", 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><h3><strong>" . __('Checkout Options', 'wpecomm-mercadopago-module' ) . "</strong></h3></td>
	</tr>
	<tr>
		<td>" . __('Description', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='textarea' size='60' value='" .
			(get_option( 'mercadopago_custom_description') == "" ?
				__( 'Pay with Mercado Pago', 'wpecomm-mercadopago-module' ) :
				get_option( 'mercadopago_custom_description')) .
			"' name='mercadopago_custom_description' />
			<p class='description'>" .
				__( "Description shown to the client in the checkout.", 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Statement Descriptor', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='text' size='60' value='" . (get_option( 'mercadopago_custom_statementdescriptor') == "" ?
				"Mercado Pago" : get_option( 'mercadopago_custom_statementdescriptor')) . "' name='mercadopago_custom_statementdescriptor' />
			<p class='description'>" .
				__( 'The description that will be shown in your customer\'s invoice.', 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Coupons', 'wpecomm-mercadopago-module' ) . "</td>
		<td>" .
			coupom_custom() . "
			<p class='description'>" .
				__( "If there is a Mercado Pago campaign, allow your store to give discounts to customers.", 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Binary Mode', 'wpecomm-mercadopago-module') . "
		</td>
		<td>" .
			binary() . "
			<p class='description'>" . __(
				"When charging a credit card, only [approved] or [reject] status will be taken.",
				'wpecomm-mercadopago-module'
			) . "</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Store Category', 'wpecomm-mercadopago-module' ) . "</td>
		<td>" .
			category_custom() . "
			<p class='description'>" .
				__( "Define which type of products your store sells.", 'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Store Identificator', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='text' size='60' value='" . (get_option( 'mercadopago_custom_invoiceprefix') == "" ? "WPeComm-" : get_option( 'mercadopago_custom_invoiceprefix')) . "' name='mercadopago_custom_invoiceprefix' />
			<p class='description'>" .
				__( "Please, inform a prefix to your store.", "wpecomm-mercadopago-module" ) . ' ' .
				__( "If you use your Mercado Pago account on multiple stores you should make sure that this prefix is unique as Mercado Pago will not allow orders with same identificators.", "wpecomm-mercadopago-module" ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('URL Approved Payment', 'wpecomm-mercadopago-module') . "</td>
		<td>
			<input name='mercadopago_custom_url_sucess' type='text' value='" . $url_sucess . "'/>
			<p class='description'>" .
				__( 'This is the URL where the customer is redirected if his payment is approved.',
					'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td>" . __('URL Pending Payment', 'wpecomm-mercadopago-module') . "</td>
		<td>
			<input name='mercadopago_custom_url_pending' type='text' value='" . $url_pending . "'/>
			<p class='description'>" .
				__( 'This is the URL where the customer is redirected if his payment is in process.',
					'wpecomm-mercadopago-module' ) . "
			</p>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><h3><strong>" . __('Payment Options', 'wpecomm-mercadopago-module') . "</strong></h3></td>
	</tr>
	<tr>
		<td>" . __('Currency Conversion', 'wpecomm-mercadopago-module') . "</td>
		<td>" .
			currency_conversion_custom() . "
			<p class='description'>" .
				__('If the used currency in WPeCommerce is different or not supported by Mercado Pago, convert values of your transactions using Mercado Pago currency ratio', 'wpecomm-mercadopago-module') . "<br >" .
				__(sprintf('%s', $currency_message)) . "
			</p>
		</td>
	</tr>
	<tr>
		<td></td>
		<td><h3><strong>" . __('Test and Debug Options', 'wpecomm-mercadopago-module') . "</strong></h3></td>
	</tr>
	<tr>
		<td>" . __('Enable Sandbox', 'wpecomm-mercadopago-module') . "
		</td>
		<td>" .
			sandbox_custom() . "
			<p class='description'>" . __(
				"This option allows you to test payments inside a sandbox environment.",
				'wpecomm-mercadopago-module'
			) . "</p>
		</td>
	</tr>
	<tr>
		<td>" . __('Debug mode', 'wpecomm-mercadopago-module') . "</td>
		<td>" .
			debugs_custom() . "
			<p class='description'>" .
			__('Enable to display log messages in browser console (not recommended in production environment)', 'wpecomm-mercadopago-module') . "
			</p>
		</td>
	</tr>\n";
	return $output;
}

/*===============================================================================
	FUNCTIONS TO PROCESS PAYMENT
================================================================================*/

/*function function_mercadopago_basic($seperator, $sessionid) {

	global $wpdb, $wpsc_cart;

	// this grabs the purchase log id from the database that refers to the $sessionid
	$purchase_log = $wpdb->get_row(
		"SELECT * FROM `" . WPSC_TABLE_PURCHASE_LOGS .
		"` WHERE `sessionid`= " . $sessionid . " LIMIT 1"
		, ARRAY_A);

	// this grabs the customer info using the $purchase_log from the previous SQL query
	$usersql = "SELECT `" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.value,
		`" . WPSC_TABLE_CHECKOUT_FORMS . "`.`name`,
		`" . WPSC_TABLE_CHECKOUT_FORMS . "`.`unique_name` FROM
		`" . WPSC_TABLE_CHECKOUT_FORMS . "` LEFT JOIN
		`" . WPSC_TABLE_SUBMITED_FORM_DATA . "` ON
		`" . WPSC_TABLE_CHECKOUT_FORMS . "`.id =
		`" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.`form_id` WHERE
		`" . WPSC_TABLE_SUBMITED_FORM_DATA . "`.`log_id`=" . $purchase_log['id'];
	$userinfo = $wpdb->get_results($usersql, ARRAY_A);
	$arr_info = array();
	foreach ((array)$userinfo as $key => $value){
		$arr_info[$value['unique_name']] = $value['value'];
	}

	// products
	$items = array();
	if (sizeof($wpsc_cart->cart_items) > 0) {
		foreach ($wpsc_cart->cart_items as $i => $item) {
			if ($item->quantity > 0) {
				$product = get_post($item->product_id);
				$picture_url = 'https://www.mercadopago.com/org-img/MP3/home/logomp3.gif';
				if ($item->thumbnail_image) {
					foreach ($item->thumbnail_image as $key => $image) {
						if ($key == 'guid') {
							$picture_url = $image;
							break;
						}
					}
				}
				array_push($items, array(
					'id' => $item->product_id,
					'title' => ( $item->product_name . ' x ' . $item->quantity ),
					'description' => sanitize_file_name( (
						// This handles description width limit of Mercado Pago.
						strlen( $product->post_content ) > 230 ?
						substr( $product->post_content, 0, 230 ) . "..." :
						$product->post_content
					)),
					'picture_url' => $picture_url,
					'category_id' => get_option('mercadopago_custom_category'),
					'quantity' => 1,
					'unit_price' => (
						((float)($item->unit_price)) *
						(float)($item->quantity)
					) * (
						(float)get_option('mercadopago_custom_currencyratio') > 0 ?
						(float)get_option('mercadopago_custom_currencyratio') : 1
					),
					'currency_id' => getCurrencyId(get_option('mercadopago_custom_siteid'))
				));
			}
		}
		// tax fees cost as an item
		array_push($items, array(
			'title' => get_option('mercadopago_custom_checkoutmessage1'),
			'description' => get_option('mercadopago_custom_checkoutmessage1'),
			'category_id' => get_option('mercadopago_custom_category'),
			'quantity' => 1,
			'unit_price' => (
				((float)($wpsc_cart->total_tax))
			) * (
				(float)get_option('mercadopago_custom_currencyratio') > 0 ?
				(float)get_option('mercadopago_custom_currencyratio') : 1
			),
			'currency_id' => getCurrencyId(get_option('mercadopago_custom_siteid'))
		));
		// shipment cost as an item
		array_push($items, array(
			'title' => $wpsc_cart->selected_shipping_option,
			'description' => get_option('mercadopago_custom_checkoutmessage2'),
			'category_id' => get_option('mercadopago_custom_category'),
			'quantity' => 1,
			'unit_price' => (
				((float)($wpsc_cart->base_shipping)+(float)($wpsc_cart->total_item_shipping))
			) * (
				(float)get_option('mercadopago_custom_currencyratio') > 0 ?
				(float)get_option('mercadopago_custom_currencyratio') : 1
			),
			'currency_id' => getCurrencyId(get_option('mercadopago_custom_siteid'))
		));
	}

	// Find excluded payment methods
	$excluded_payment_string = get_option('mercadopago_custom_exmethods');
	if ($excluded_payment_string != '') {
		$excluded_payment_methods = array();
  	$excluded_payment_string = explode(',', $excluded_payment_string);
  	foreach ($excluded_payment_string as $exclude ) {
			if ($exclude != "") {
				array_push( $excluded_payment_methods, array(
    			"id" => $exclude
	    	));
	    }
		}
		$payment_methods = array(
			"installments" => (int)get_option('mercadopago_custom_maxinstallments'),
			"excluded_payment_methods" => $excluded_payment_methods,
			'default_installments' => 1
		);
	} else {
		$payment_methods = array(
			"installments" => (int)get_option('mercadopago_custom_maxinstallments'),
			'default_installments' => 1
		);
	}

	// create Mercado Pago preference
	$billing_details = wpsc_get_customer_meta();
	$order_id = $billing_details['_wpsc_cart.log_id'][0];
	$preferences = array(
		'items' => $items,
		// payer should be filled with billing info because orders can be made with non-logged customers.
		'payer' => array(
			'name' => $arr_info['billingfirstname'],
			'surname' => $arr_info['billinglastname'],
			'email' => $arr_info['billingemail'],
			'phone'	=> array(
				'number' => $arr_info['billingphone']
			),
			'address' => array(
				'street_name' => $arr_info['billingaddress'] . ' / ' .
					$arr_info['billingcity'] . ' ' .
					$arr_info['billingstate'] . ' ' .
					$arr_info['billingcountry'],
				'zip_code' => $arr_info['billingpostcode']
			)
		),
		'back_urls' => array(
			'success' => workaroundAmperSandBug( esc_url( get_site_url() ) ),
			'failure' => workaroundAmperSandBug( esc_url( get_site_url() ) ),
			'pending' => workaroundAmperSandBug( esc_url( get_site_url() ) )
		),
		//'marketplace' =>
    //'marketplace_fee' =>
    'shipments' => array(
    	//'cost' => (float) $order->get_total_shipping(),
    	//'mode' =>
    	'receiver_address' => array(
    		'zip_code' => $arr_info['shippingpostcode'],
    		//'street_number' =>
    		'street_name' => $arr_info['shippingaddress'] . ' ' .
    			$arr_info['shippingcity'] . ' ' .
    			$arr_info['shippingstate'] . ' ' .
    			$arr_info['shippingcountry'],
    		//'floor' =>
    		'apartment' => $arr_info['shippingfirstname']
    	)
    ),
		'payment_methods' => $payment_methods,
		'external_reference' => get_option('mercadopago_custom_invoiceprefix') . $order_id
		//'additional_info' => $order->customer_message
    //'expires' =>
    //'expiration_date_from' =>
    //'expiration_date_to' =>
	);

	// Do not set IPN url if it is a localhost!
  $notification_url = get_site_url() . '/wpecomm-mercadopago-module/?wc-api=WC_WPeCommMercadoPago_Gateway';
  if ( !strrpos( $notification_url, "localhost" ) ) {
      $preferences['notification_url'] = workaroundAmperSandBug( $notification_url );
  }
	// Set sponsor ID
	if ( get_option('mercadopago_custom_istestuser') == "no" ) {
		switch (get_option('mercadopago_custom_siteid')) {
			case 'MLA':
				$sponsor_id = 219693774;
				break;
			case 'MLB':
				$sponsor_id = 219691508;
				break;
			case 'MLC':
				$sponsor_id = 219691655;
				break;
			case 'MCO':
				$sponsor_id = 219695429;
				break;
			case 'MLM':
				$sponsor_id = 219696864;
				break;
			case 'MPE':
				$sponsor_id = 219692012;
				break;
			case 'MLV':
				$sponsor_id = 219696139;
				break;
			default:
				$sponsor_id = null;
		}
		if ($sponsor_id != null)
			$preferences[ 'sponsor_id' ] = $sponsor_id;
	}

	// auto return options
	if ( get_option('mercadopago_custom_autoreturn') == "active" ) {
		$preferences[ 'auto_return' ] = "approved";
	}

	// log created preferences
	if ( get_option('mercadopago_custom_debug') == "Yes" ) {
		debug_to_console_custom(
			"@" . __FUNCTION__ . " - " .
			"Preferences created, now processing it: " .
			json_encode($preferences, JSON_PRETTY_PRINT)
		);
	}

	process_payment($preferences, $wpsc_cart);

}

function process_payment($preferences, $wpsc_cart) {

	$mp = new MP(
		get_option('mercadopago_custom_publickey'),
		get_option('mercadopago_custom_accesstoken')
	);
	$access_token = $mp->get_access_token();
	$isTestUser = get_option('mercadopago_custom_istestuser');

	// trigger API to create payment
	$preferenceResult = $mp->create_preference($preferences);
	if ($preferenceResult['status'] == 201) {
		if (get_option('mercadopago_custom_sandbox') == "active") {
			$link = $preferenceResult['response']['sandbox_init_point'];
		} else {
			$link = $preferenceResult['response']['init_point'];
		}
	} else {
		// TODO: create a better user feedback...
		echo "Error: " . $preferenceResult['status'];
	}

	// build payment banner url
	$banners_mercadopago_standard = array(
	  "MLA" => 'MLA/standard.jpg',
	  "MLB" => 'MLB/standard.jpg',
	  "MCO" => 'MCO/standard.jpg',
	  "MLC" => 'MLC/standard.gif',
		"MPE" => 'MPE/standard.png',
    "MLV" => 'MLV/standard.jpg',
    "MLM" => 'MLM/standard.jpg'
  );
  $html =
		'<img alt="Mercado Pago" title="Mercado Pago" width="468" height="60" src="' .
		plugins_url( 'wpsc-merchants/mercadopago-images/' . $banners_mercadopago_standard[get_option('mercadopago_custom_siteid')], plugin_dir_path( __FILE__ ) ) . '">';

	if ($link) {
		// build payment button html code
		switch (get_option('mercadopago_custom_typecheckout')) {
			case "Redirect":
				// we don't need to build the payment page, as it is a redirection to Mercado Pago
				header("location: " . $link);
				break;
			case "Iframe":
				$html .= '<p></p><p>' . wordwrap(
					get_option('mercadopago_custom_checkoutmessage4'),
					60, '<br>' ) . '</p>';
				$html .=
					'<iframe src="' . $link . '" name="MP-Checkout" ' .
					'width="' . ( is_numeric( (int)
						get_option('mercadopago_custom_iframewidth') ) ?
						get_option('mercadopago_custom_iframewidth') : 640 ) . '" ' .
					'height="' . ( is_numeric( (int)
						get_option('mercadopago_custom_iframeheight') ) ?
						get_option('mercadopago_custom_iframeheight') : 800 ) . '" ' .
					'frameborder="0" scrolling="no" id="checkout_mercadopago"></iframe>';
				break;
			case "Lightbox": default:
					$html .= '<p></p>';
					$html .=
						'<a id="mp-btn" href="' . $link . '" name="MP-Checkout" class="button alt" mp-mode="modal">' .
						get_option('mercadopago_custom_checkoutmessage5') .
						'</a> ';
					$html .=
						'<script type="text/javascript">(function(){function $MPBR_load(){window.$MPBR_loaded !== true && (function(){var s = document.createElement("script");s.type = "text/javascript";s.async = true;s.src = ("https:"==document.location.protocol?"https://www.mercadopago.com/org-img/jsapi/mptools/buttons/":"http://mp-tools.mlstatic.com/buttons/")+"render.js";var x = document.getElementsByTagName("script")[0];x.parentNode.insertBefore(s, x);window.$MPBR_loaded = true;})();}window.$MPBR_loaded !== true ? (window.attachEvent ? window.attachEvent("onload", $MPBR_load) : window.addEventListener("load", $MPBR_load, false)) : null;})();</script>';
					$html .=
						'<style>#mp-btn {background-color: #009ee3; border: 1px solid #009ee3; border-radius: 4px;
							color: #fff; display: inline-block; font-family: Arial,sans-serif; font-size: 18px;
							font-weight: normal; margin: 0; padding: 10px; text-align: center; width: 50%;}
						</style>';
				break;
		}
	} else {
		$html =
			'<p>' . get_option('mercadopago_custom_checkoutmessage6') . '</p>';
	}

	// show page
	get_header();
	$page = '<div style="position: relative; margin: 20px 0;" >';
		$page .= '<div style="margin: 0 auto; width: 1080px; ">';
			$page .= '<h3>' . get_option('mercadopago_custom_checkoutmessage3') . '</h3>';
			$page .= $html;
		$page .= '</div>';
	$page .= '</div>';
	echo $page;
	get_footer();

	exit;

}*/

/*===============================================================================
	FUNCTIONS TO GENERATE VIEWS
================================================================================*/

function category_custom() {
	$category = get_option('mercadopago_custom_category');
	$category = $category === false || is_null($category) ? "others" : $category;
	// category marketplace
	$list_category = MPRestClient::get( array( "uri" => "/item_categories" ) );
	$list_category = $list_category["response"];
	$select_category = '<select name="mercadopago_custom_category" id="category" style="max-width:600px;>';
	foreach ($list_category as $category_arr) :
		$selected = "";
		if ($category_arr['id'] == $category) :
			$selected = 'selected="selected"';
		endif;
		$select_category .=
			'<option value="' . $category_arr['id'] .
			'" id="type-checkout-' . $category_arr['description'] .
			'" ' . $selected . ' >' . $category_arr['description'] .
			'</option>';
	endforeach;
	$select_category .= "</select>";
	return $select_category;
}

function coupom_custom() {
	$coupom = get_option('mercadopago_custom_coupom');
	$coupom = $coupom === false || is_null($coupom) ? "inactive" : $coupom;
	$coupom_options = array(
		array("value" => "active", "text" => "Active"),
		array("value" => "inactive", "text" => "Inactive")
	);
	$select_coupom = '<select name="mercadopago_custom_coupom" id="coupom">';
	foreach ($coupom_options as $op_coupom) :
		$selected = "";
		if ($op_coupom['value'] == $coupom) :
		$selected = 'selected="selected"';
		endif;
		$select_coupom .=
			'<option value="' . $op_coupom['value'] .
			'" id="coupom-' . $op_coupom['value'] .
			'" ' . $selected . '>' . __($op_coupom['text'], "wpecomm-mercadopago-module") .
			'</option>';
	endforeach;
	$select_coupom .= "</select>";
	return $select_coupom;
}

function binary() {
	$binary = get_option('mercadopago_custom_binary');
	$binary = $binary === false || is_null($binary) ? "inactive" : $binary;
	$binary_options = array(
		array("value" => "active", "text" => "Active"),
		array("value" => "inactive", "text" => "Inactive")
	);
	$select_binary = '<select name="mercadopago_custom_binary" id="binary">';
	foreach ($binary_options as $op_binary) :
		$selected = "";
		if ($op_binary['value'] == $binary) :
		$selected = 'selected="selected"';
		endif;
		$select_binary .=
			'<option value="' . $op_binary['value'] .
			'" id="binary-' . $op_binary['value'] .
			'" ' . $selected . '>' . __($op_binary['text'], "wpecomm-mercadopago-module") .
			'</option>';
	endforeach;
	$select_binary .= "</select>";
	return $select_binary;
}

function sandbox_custom() {
	$sandbox = get_option('mercadopago_custom_sandbox');
	$sandbox = $sandbox === false || is_null($sandbox) ? "inactive" : $sandbox;
	$sandbox_options = array(
		array("value" => "active", "text" => "Active"),
		array("value" => "inactive", "text" => "Inactive")
	);
	$select_sandbox = '<select name="mercadopago_custom_sandbox" id="sandbox">';
	foreach ($sandbox_options as $op_sandbox) :
		$selected = "";
		if ($op_sandbox['value'] == $sandbox) :
		$selected = 'selected="selected"';
		endif;
		$select_sandbox .=
			'<option value="' . $op_sandbox['value'] .
			'" id="sandbox-' . $op_sandbox['value'] .
			'" ' . $selected . '>' . __($op_sandbox['text'], "wpecomm-mercadopago-module") .
			'</option>';
	endforeach;
	$select_sandbox .= "</select>";
	return $select_sandbox;
}

function currency_conversion_custom() {
	$currencyconversion = get_option('mercadopago_custom_currencyconversion');
	$currencyconversion = $currencyconversion === false || is_null($currencyconversion) ? "inactive" : $currencyconversion;
	$currencyconversion_options = array(
		array("value" => "active", "text" => "Active"),
		array("value" => "inactive", "text" => "Inactive")
	);
	$select_currencyconversion = '<select name="mercadopago_custom_currencyconversion" id="currencyconversion">';
	foreach ($currencyconversion_options as $op_currencyconversion) :
		$selected = "";
		if ($op_currencyconversion['value'] == $currencyconversion) :
		$selected = 'selected="selected"';
		endif;
		$select_currencyconversion .=
			'<option value="' . $op_currencyconversion['value'] .
			'" id="currencyconversion-' . $op_currencyconversion['value'] .
			'" ' . $selected . '>' . __($op_currencyconversion['text'], "wpecomm-mercadopago-module") .
			'</option>';
	endforeach;
	$select_currencyconversion .= "</select>";
	return $select_currencyconversion;
}

function debugs_custom() {
	if (get_option('mercadopago_custom_debug') == null || get_option('mercadopago_custom_debug') == '') {
		$mercadopago_custom_debug = 'No';
	} else {
		$mercadopago_custom_debug = get_option('mercadopago_custom_debug');
	}
	$debugs = array('No','Yes');
	$showdebugs = '<select name="mercadopago_custom_debug">';
	foreach ($debugs as  $debug ) :
		if ($debug == $mercadopago_custom_debug) {
			$showdebugs .= '<option value="' . $debug . '" selected="selected">' . __($debug, "wpecomm-mercadopago-module") . '</option>';
		} else {
			$showdebugs .= '<option value="' . $debug . '">' . __($debug, "wpecomm-mercadopago-module") . '</option>';
		}
	endforeach;
	$showdebugs .= '</select>';
	return $showdebugs;
}

/*===============================================================================
	AUXILIARY FUNCTIONS
================================================================================*/

// check if we have valid credentials.
function validateCredentials_custom($public_key, $access_token) {
	$result = array();
	if (empty($public_key) || empty($access_token)) {
		$result['site_id'] = null;
		$result['is_valid'] = false;
		$result['is_test_user'] = true;
		$result['currency_ratio'] = -1;
		return $result;
	}
	if (strlen($public_key) > 0 && strlen($access_token) > 0 ) {
		try {
			$mp = new MP($access_token);
			$result['access_token'] = $access_token;
			$get_request = $mp->get( "/users/me?access_token=" . $access_token );
			if (isset($get_request['response']['site_id'])) {
				$result['is_test_user'] = in_array('test_user', $get_request['response']['tags']) ? "yes" : "no";
				$result['site_id'] = $get_request['response']['site_id'];
				// check for auto converstion of currency
				$result['currency_ratio'] = -1;
				if ( get_option('mercadopago_custom_currencyconversion') == "active" ) {
					$currency_obj = MPRestClient::get_ml( array( "uri" =>
						"/currency_conversions/search?from=" .
						WPSC_Countries::get_currency_code(absint(get_option('currency_type'))) .
						"&to=" .
						getCurrencyId( $result['site_id'] )
					) );
					if ( isset( $currency_obj[ 'response' ] ) ) {
						$currency_obj = $currency_obj[ 'response' ];
						if ( isset( $currency_obj['ratio'] ) ) {
							$result['currency_ratio'] = (float) $currency_obj['ratio'];
						} else {
							$result['currency_ratio'] = -1;
						}
					} else {
						$result['currency_ratio'] = -1;
					}
				}
				$result['is_valid'] = true;
				return $result;
			} else {
				$result['site_id'] = null;
				$result['is_valid'] = false;
				$result['is_test_user'] = true;
				$result['currency_ratio'] = -1;
				return $result;
			}
		} catch ( MercadoPagoException $e ) {
			$result['site_id'] = null;
			$result['is_valid'] = false;
			$result['is_test_user'] = true;
			$result['currency_ratio'] = -1;
			return $result;
		}
	}
	$result['site_id'] = null;
	$result['is_valid'] = false;
	$result['is_test_user'] = true;
	$result['currency_ratio'] = -1;
	return $result;
}

function getCurrencyId_custom($site_id) {
	switch ($site_id) {
		case 'MLA': return 'ARS';
		case 'MLB': return 'BRL';
		case 'MCO': return 'COP';
		case 'MLC': return 'CLP';
		case 'MLM': return 'MXN';
		case 'MLV': return 'VEF';
		case 'MPE': return 'PEN';
		default: return '';
	}
}

function getCountryName_custom($site_id) {
	$country = $site_id;
	switch ($site_id) {
		case 'MLA': return __( 'Argentine', 'wpecomm-mercadopago-module' );
		case 'MLB': return __( 'Brazil', 'wpecomm-mercadopago-module' );
		case 'MCO': return __( 'Colombia', 'wpecomm-mercadopago-module' );
		case 'MLC': return __( 'Chile', 'wpecomm-mercadopago-module' );
		case 'MLM': return __( 'Mexico', 'wpecomm-mercadopago-module' );
		case 'MLV': return __( 'Venezuela', 'wpecomm-mercadopago-module' );
		case 'MPE': return __( 'Peru', 'wpecomm-mercadopago-module' );
	}

}

// Return boolean indicating if currency is supported.
function isSupportedCurrency_custom($site_id) {
	$store_currency_code = WPSC_Countries::get_currency_code(absint(get_option('currency_type')));
	return $store_currency_code == getCurrencyId($site_id);
}

// Fix to URL Problem : #038; replaces & and breaks the navigation
function workaroundAmperSandBug_custom( $link ) {
	return str_replace('&#038;', '&', $link);
}

function debug_to_console_custom($data) {
	// TODO: review debug function as it causes header to be sent
  /*$output  = "<script>console.log( '[WPeComm-Mercado-Pago-Module Logger] => ";
  $output .= json_encode(print_r($data, true), JSON_PRETTY_PRINT);
  $output .= "' );</script>";
  echo $output;*/
}

/*===============================================================================
	INSTANTIATIONS
================================================================================*/

new WPSC_Merchant_MercadoPago_Custom();

?>
