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

include_once "mercadopago-lib/mercadopago.php";

if (isset($_REQUEST['mercadopago_custom'])) {

	$purchase_log = new WPSC_Purchase_Log($_REQUEST['mercadopago_custom']['purchase_id']);
	$preference = unserialize(base64_decode($_REQUEST['mercadopago_custom']['preference']));

	$mp = new MP( // Create MP object and checks for sandbox mode
		get_option('mercadopago_custom_accesstoken')
	);
	$isTestUser = get_option('mercadopago_custom_istestuser');
	if ( 'active' == get_option('mercadopago_custom_sandbox') ) {
		$mp->sandbox_mode( true );
	} else {
		$mp->sandbox_mode( false );
	}

	//$payment = $mp->create_payment($preference);

	//Now to do actions once the payment has been attempted
	/*switch ( $purchase_log->get( 'processed' ) ) {
		case WPSC_Purchase_Log::ACCEPTED_PAYMENT:
			// payment worked
			do_action('wpsc_payment_successful');
			break;
		case WPSC_Purchase_Log::INCOMPLETE_SALE:
			// payment declined
			do_action('wpsc_payment_failed');
			break;
		case WPSC_Purchase_Log::ORDER_RECEIVED:
			// something happened with the payment
			do_action('wpsc_payment_incomplete');
			break;
	}*/

	/*$transaction_url_with_sessionid = add_query_arg(
		'sessionid', $_REQUEST['mercadopago_custom']['session_id'],
		get_option( 'transact_url' )
	);
	wp_redirect( $transaction_url_with_sessionid );*/
	//$preference = json_encode(array_values($preference));
	//echo print_r($preference, true);
	echo $preference['additional_info']['shipments']['receiver_address']['street_name'];

	//header( 'HTTP/1.1 200 OK' );
  //header( 'Content-Type: application/json' );
  //echo json_encode( $response );
  /*echo json_decode(stripslashes(
		preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', stripslashes(
			$_REQUEST['mercadopago_custom']['arr_info']
		))
	), true);*/

	//$exported_wpsc_cart = unserialize($_REQUEST['mercadopago_custom']['wpsc_cart']);

	/*$exported_wpsc_cart = (stripslashes(
		preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', stripslashes(
			$_REQUEST['mercadopago_custom']['wpsc_cart']
		))
	));*/

	//$exported_wpsc_cart = html_entity_decode($_REQUEST['mercadopago_custom']['wpsc_cart']);
	//$exported_wpsc_cart = unserialize(base64_decode($_REQUEST['mercadopago_custom']['wpsc_cart']));
	//$exported_wpsc_cart = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $exported_wpsc_cart);
	/*$exported_wpsc_cart = preg_replace_callback(
    	"/\{([<>])([a-zA-Z0-9_]*)(\?{0,1})([a-zA-Z0-9_]*)\}(.*)\{\\1\/\\2\}/isU",
	    function($m) { return CallFunction($m[1], $m[2], $m[3], $m[4], $m[5]); },
	    $exported_wpsc_cart
	);*/
	//$exported_wpsc_cart = str_replace('"', "'", $exported_wpsc_cart);
	//findSerializeError($exported_wpsc_cart);

	//$exported_wpsc_cart = unserialize($exported_wpsc_cart);
	//$exported_wpsc_cart = print_r_reverse(stripslashes(trim(preg_replace('/\s\s+/', ' ', $exported_wpsc_cart))));
	/*$wpsc_cart = json_encode( print_r_reverse(
		stripslashes(
			preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', stripslashes(
				$exported_wpsc_cart->scalar
			))
		))
	);*/

	//echo "exported value = " . $exported_wpsc_cart->selected_shipping_option;
		//->selected_shipping_method;

	exit();
}

$nzshpcrt_gateways[$num] = array(
	'name' =>  __( 'Mercado Pago - Credit Card', 'wpecomm-mercadopago-module' ),
	'api_version' => 2.0,
	//'image' => WPSC_URL . '/wpsc-merchants/mercadopago-images/mercadopago.png',
	'class_name' => 'WPSC_Merchant_MercadoPago_Custom',
	'has_recurring_billing' => true,
	'display_name' => __( 'Mercado Pago - Credit Card', 'wpecomm-mercadopago-module' ),
	'wp_admin_cannot_cancel' => false,
	'requirements' => array(
		/// so that you can restrict merchant modules to PHP 5, if you use PHP 5 features
		'php_version' => 5.6,
		 /// for modules that may not be present, like curl
		'extra_modules' => array()
	),
	// All array members below here are legacy, and use the code in mercadopago_multiple.php
	'form' => 'form_mercadopago_custom',
	'submit_function' => 'submit_mercadopago_custom',
	//'function' => 'function_mercadopago_custom',
	'internalname' => 'WPSC_Merchant_MercadoPago_Custom'
);

class WPSC_Merchant_MercadoPago_Custom extends wpsc_merchant {

	var $name = '';
	var $purchase_id = null;

	function __construct( $purchase_id = null, $is_receiving = false ) {
		add_action( 'init', array( $this, 'load_plugin_textdomain_wpecomm' ) );
		$this->purchase_id = $purchase_id;
		$this->name = __( 'Mercado Pago - Credit Card', 'wpecomm-mercadopago-module' );
		parent::__construct( $purchase_id, $is_receiving );
	}

	function submit() {

		global $wpdb, $wpsc_cart;

		// labels
		$form_labels = json_decode(stripslashes(
			preg_replace('/u([\da-fA-F]{4})/', '&#x\1;', stripslashes(
				get_option('mercadopago_custom_checkoutmessage1')
			))
		), true);

		// this grabs the purchase log id from the database that refers to the $sessionid
		$purchase_log = $wpdb->get_row(
			"SELECT * FROM `" . WPSC_TABLE_PURCHASE_LOGS .
			"` WHERE `sessionid`= " . $this->cart_data['session_id'] . " LIMIT 1"
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

		$mp = new MP( // Create MP object and checks for sandbox mode
			get_option('mercadopago_custom_accesstoken')
		);
		$isTestUser = get_option('mercadopago_custom_istestuser');
		if ( 'active' == get_option('mercadopago_custom_sandbox') ) {
			$mp->sandbox_mode( true );
		} else {
			$mp->sandbox_mode( false );
		}

		// get customer cards
		$customerId = null;
		$customer_cards = array();
		try { // find logged user
			$logged_user_email = $arr_info['billingemail'];
			$customer = $mp->get_or_create_customer( $logged_user_email );
			$customer_cards = $customer[ 'cards' ];
			$parameters[ 'customerId' ] = $customer[ 'id' ];
			$parameters[ 'customer_cards' ] = $customer_cards;
		} catch ( Exception $e ) {
			// TODO: handle exception
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

	  	// header
		$payment_header =
			'<div width="100%" style="margin:0px; padding:16px 36px 16px 36px; background:white;
				border-style:solid; border-color:#DDDDDD" border-radius:1.0px;">
				<img class="logo" src=' .
					plugins_url( 'wpsc-merchants/mercadopago-images/mplogo.png', plugin_dir_path( __FILE__ ) ) . '
					" width="156" height="40" />
				<img alt="Mercado Pago" title="Mercado Pago" class="mp-creditcard-banner" src="' .
					$this->getImagePath($banners_mercadopago_standard[get_option('mercadopago_custom_siteid')]) . '
					" width="312" height="40" />
			</div>';
		// payment method
		$mercadopago_form =
			'<form action="' . //plugins_url('wpsc-merchants/mercadopago-lib/cst_post.php', plugin_dir_path(__FILE__)) .
				//htmlspecialchars($_SERVER["PHP_SELF"]) .
				'" method="post"><fieldset style="background:white;">
				<div id="mercadopago-form-customer-and-card" style="padding:0px 36px 0px 36px;">
					<div class="mp-box-inputs mp-line">
	      				<label for="paymentMethodIdSelector">' . $form_labels['form']['payment_method'] . ' <em>*</em></label>
	        			<select id="paymentMethodSelector" name="mercadopago_custom[paymentMethodSelector]" data-checkout="cardId">
	          				<optgroup label=' . $form_labels['form']['your_card'] . ' id="payment-methods-for-customer-and-cards">' .
	          					payment_methods_customer_cards($customer_cards, $form_labels) .
	          				'</optgroup>
	          				<optgroup label="' . $form_labels['form']['other_cards'] . '" id="payment-methods-list-other-cards">
	            				<option value="-1">' . $form_labels['form']['other_card'] . '</option>
	          				</optgroup>
	        			</select>
	    			</div>
	    			<div class="mp-box-inputs mp-line" id="mp-securityCode-customer-and-card">
	      				<div class="mp-box-inputs mp-col-45">
		      				<label for="customer-and-card-securityCode">' . $form_labels['form']['security_code'] . ' <em>*</em></label>
		        			<input type="text" id="customer-and-card-securityCode" data-checkout="securityCode" autocomplete="off"
			        			maxlength="4" style="padding: 8px; background: url( ' . $this->getImagePath('cvv.png') .
		        				' ) 98% 50% no-repeat;"/>
		          			<span class="mp-error" id="mp-error-224" data-main="#customer-and-card-securityCode"> ' .
		          				$form_labels['error']['224'] . ' </span>
		          			<span class="mp-error" id="mp-error-E302" data-main="#customer-and-card-securityCode"> ' .
			         			$form_labels['error']['E302'] . ' </span>
	      				</div>
	    			</div>
				</div>

				<div id="mercadopago-form" style="padding:0px 36px 0px 36px;">
    				<div class="mp-box-inputs mp-col-100">
        				<label for="cardNumber">' . $form_labels['form']['credit_card_number'] . ' <em>*</em></label>
        				<input type="text" id="cardNumber" data-checkout="cardNumber" autocomplete="off" maxlength="19"/>
        				<span class="mp-error" id="mp-error-205" data-main="#cardNumber"> ' .
        					$form_labels['error']['205'] . ' </span>
        				<span class="mp-error" id="mp-error-E301" data-main="#cardNumber"> ' .
        					$form_labels['error']['E301'] . ' </span>
    				</div>
    				<div class="mp-box-inputs mp-line">
        				<div class="mp-box-inputs mp-col-45">
		            		<label for="cardExpirationMonth">' . $form_labels['form']['expiration_month'] . ' <em>*</em></label>
		            		<select id="cardExpirationMonth" data-checkout="cardExpirationMonth" name="mercadopago_custom[cardExpirationMonth]">
								<option value="-1"> ' . $form_labels['form']['month'] . ' </option>' .
								'<option value="1"> 1 </option>' . '<option value="2"> 2 </option>' . '<option value="3"> 3 </option>' .
								'<option value="4"> 4 </option>' . '<option value="5"> 5 </option>' . '<option value="6"> 6 </option>' .
								'<option value="7"> 7 </option>' . '<option value="8"> 8 </option>' . '<option value="9"> 9 </option>' .
								'<option value="10"> 10 </option>' . '<option value="11"> 11 </option>' . '<option value="12"> 12 </option>' .
		          			'</select>
		        		</div>
		        		<div class="mp-box-inputs mp-col-10">
		            		<div id="mp-separete-date">
		            		/
		            		</div>
		        		</div>
						<div class="mp-box-inputs mp-col-45">
		          			<label for="cardExpirationYear">' . $form_labels['form']['expiration_year'] . ' <em>*</em></label>
	          				<select id="cardExpirationYear" data-checkout="cardExpirationYear" name="mercadopago_custom[cardExpirationYear]">
		          				<option value="-1"> ' . $form_labels['form']['year'] . ' </option>' .
		          				build_years_for_date() .
	          				'</select>
      					</div>
						<span class="mp-error" id="mp-error-208" data-main="#cardExpirationMonth"> ' .
							$form_labels['error']['208'] . ' </span>
						<span class="mp-error" id="mp-error-209" data-main="#cardExpirationYear"> </span>
						<span class="mp-error" id="mp-error-325" data-main="#cardExpirationMonth"> ' .
							$form_labels['error']['325'] . ' </span>
						<span class="mp-error" id="mp-error-326" data-main="#cardExpirationYear"> </span>
					</div>
		    		<div class="mp-box-inputs mp-col-100">
		        		<label for="cardholderName">' . $form_labels['form']['card_holder_name'] . ' <em>*</em></label>
		        		<input type="text" id="cardholderName" name="mercadopago_custom[cardholderName]" data-checkout="cardholderName" autocomplete="off" />
		        		<span class="mp-error" id="mp-error-221" data-main="#cardholderName"> ' .
		        			$form_labels['error']['221'] . ' </span>
		        		<span class="mp-error" id="mp-error-316" data-main="#cardholderName"> ' .
		        			$form_labels['error']['316'] . ' </span>
		    		</div>
		    		<div class="mp-box-inputs mp-line">
		        		<div class="mp-box-inputs mp-col-45">
		            		<label for="securityCode">' . $form_labels['form']['security_code'] . ' <em>*</em></label>
		            		<input type="text" id="securityCode" data-checkout="securityCode" autocomplete="off"' .
		            			'maxlength="4" style="padding: 8px; background: url( ' . $this->getImagePath('cvv.png') .
	            					' ) 98% 50% no-repeat;" />
		            		<span class="mp-error" id="mp-error-224" data-main="#securityCode"> ' .
		            			$form_labels['error']['224'] . ' </span>
		            		<span class="mp-error" id="mp-error-E302" data-main="#securityCode"> ' .
		            			$form_labels['error']['E302'] . ' </span>
		        		</div>
		    		</div>
					<div class="mp-box-inputs mp-col-100 mp-doc">
		        		<div class="mp-box-inputs mp-col-35 mp-docType">
		            		<label for="docType">' . $form_labels['form']['document_type'] . ' <em>*</em></label>
		            		<select id="docType" data-checkout="docType" name="mercadopago_custom[docType]"></select>
	            			<span class="mp-error" id="mp-error-212" data-main="#docType"> ' .
	            				$form_labels['error']['212'] . ' </span>
		            		<span class="mp-error" id="mp-error-322" data-main="#docType"> ' .
		            			$form_labels['error']['322'] . ' </span>
		        		</div>
		      			<div class="mp-box-inputs mp-col-65 mp-docNumber">
			          		<label for="docNumber">' . $form_labels['form']['document_number'] . ' <em>*</em></label>
			          		<input type="text" id="docNumber" data-checkout="docNumber" name="mercadopago_custom[docNumber]" autocomplete="off" />
		          			<span class="mp-error" id="mp-error-214" data-main="#docNumber"> ' .
		          				$form_labels['error']['214'] . ' </span>
		          			<span class="mp-error" id="mp-error-324" data-main="#docNumber"> ' .
		          				$form_labels['error']['324'] . ' </span>
			        	</div>
		  			</div>
					<div class="mp-box-inputs mp-col-100 mp-issuer">
		        		<label for="issuer">' . $form_labels['form']['issuer'] . ' <em>*</em></label>
		        		<select id="issuer" data-checkout="issuer" name="mercadopago_custom[issuer]"></select>
						<span class="mp-error" id="mp-error-220" data-main="#issuer"> ' . $form_labels['error']['220'] . ' </span>
		  			</div>
				</div>

				<div class="mp-box-inputs mp-col-100" style="padding:0px 36px 0px 36px;">
			  		<label for="installments">' . $form_labels['form']['installments'] . (
			  			(get_option('mercadopago_custom_currencyconversion') == "active") > 0 ?
			  				" (" . $form_labels['form']['payment_converted'] . ")" : ""
			  			) . ' <em>*</em>
			  		</label>
			      	<select id="installments" data-checkout="installments" name="mercadopago_custom[installments]"></select>
			  	</div>
			  	<div class="mp-box-inputs mp-line" style="padding:0px 36px 0px 36px;">
					<div class="mp-box-inputs mp-col-45">
		      			<input type="submit" id="submit" value="' . $form_labels['form']['pay_with_mp'] . '">
					</div>
					<div class="mp-box-inputs mp-col-45" style="background: #00FFFFFF; float: right;">
		          		<div id="mp-box-loading" style="margin: 4px 4px 4px 0px;"></div>
					</div>
		    	</div>

		    	<div class="mp-box-inputs mp-col-100" id="mercadopago-utilities" style="padding:0px 36px 0px 36px;">
					<input type="hidden" id="site_id" name="mercadopago_custom[site_id]"/>
					<input type="hidden" id="amount" value="' . $wpsc_cart->calculate_total_price() . '" name="mercadopago_custom[amount]"/>
					<input type="hidden" id="paymentMethodId" name="mercadopago_custom[paymentMethodId]"/>
					<input type="hidden" id="token" name="mercadopago_custom[token]"/>
					<input type="hidden" id="cardTruncated" name="mercadopago_custom[cardTruncated]"/>
					<input type="hidden" id="CustomerAndCard" name="mercadopago_custom[CustomerAndCard]"/>
					<input type="hidden" id="CustomerId" value="' . $customerId . '" name="mercadopago_custom[CustomerId]"/>

					<input type="hidden" id="session_id" value="' . $this->cart_data['session_id'] . '" name="mercadopago_custom[session_id]"/>
					<input type="hidden" id="purchase_id" value="' . $this->purchase_id . '" name="mercadopago_custom[purchase_id]"/>
					<input type="hidden" id="preference" value="' . base64_encode(serialize($this->create_preference($wpdb, $wpsc_cart))) .
						'" name="mercadopago_custom[preference]"/>
			  	</div>

			</fieldset></form>';

		$page_js = '
			<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
			<script src="' . plugins_url( 'wpsc-merchants/mercadopago-lib/MPv1.js?no_cache=' .
				time(), plugin_dir_path( __FILE__ ) ) . '"></script>
			<script type="text/javascript">
				var mercadopago_site_id = "' . get_option('mercadopago_custom_siteid') . '";
				var mercadopago_public_key = "' . get_option('mercadopago_custom_publickey') . '";
				MPv1.text.choose = "' . $form_labels["form"]["label_choose"] . '";
			  	MPv1.text.other_bank = "' . $form_labels["form"]["label_other_bank"] . '";
			    MPv1.paths.loading = "' . $this->getImagePath('loading.gif') . '";
			  	MPv1.setForm = function() {
					if (MPv1.customer_and_card.status) {
				        document.querySelector(MPv1.selectors.form).style.display = "none";
				        document.querySelector(MPv1.selectors.mpSecurityCodeCustomerAndCard).removeAttribute("style");
		      		} else {
				        document.querySelector(MPv1.selectors.mpSecurityCodeCustomerAndCard).style.display = "none";
				        document.querySelector(MPv1.selectors.form).removeAttribute("style");
				        document.querySelector(MPv1.selectors.form).style.padding = "0px 36px 0px 36px";
			      	}
			      	Mercadopago.clearSession();
			      	if (MPv1.create_token_on.event) {
				        MPv1.createTokenByEvent();
				        MPv1.validateInputsCreateToken();
			      	}
			      	document.querySelector(MPv1.selectors.CustomerAndCard).value = MPv1.customer_and_card.status;
			  	}
			  	MPv1.getAmount = function() {
			      	return document.querySelector(MPv1.selectors.amount).value;
			  	}
			  	MPv1.showErrors = function(response) {
					var $form = MPv1.getForm();
					for (var x = 0; x < response.cause.length; x++) {
				        var error = response.cause[x];
				        var $span = $form.querySelector("#mp-error-" + error.code);
				        var $input = $form.querySelector($span.getAttribute("data-main"));
			        	$span.style.display = "inline-block";
			        	$input.classList.add("mp-error-input");
			    	}
			    	return;
				}
			  	MPv1.Initialize(mercadopago_site_id, mercadopago_public_key);
			</script>';

		$page_html =
			'<head>' .
				'<link rel="stylesheet" id="twentysixteen-style-css"
					href="https://modules-mercadopago.rhcloud.com/wp-content/themes/twentysixteen/style.css?ver=4.5.3" type="text/css" media="all">' .
				'<link rel="stylesheet" id="custom-checkout-mercadopago" href="' .
					plugins_url( 'wpsc-merchants/mercadopago-lib/custom_checkout_mercadopago.css', plugin_dir_path( __FILE__ ) ) .
					'?ver=4.5.3" type="text/css" media="all">' .
				'<script src="' . plugins_url( 'wpsc-merchants/mercadopago-lib/MPv1.js?no_cache=' . time(), plugin_dir_path( __FILE__ ) ) . '"></script>' .
			'</head>';

		$page_html .= '<div style="width: 600px;">' . $payment_header . '</div>';
		$page_html .= '<div style="width: 600px;">' . $mercadopago_form . '</div>';
		$page_html .= $page_js;
		echo $page_html;
			
		exit();
	}

	// Process the payment of Mercado Pago
	function create_preference($wpdb, $wpsc_cart) {

		// this grabs the purchase log id from the database that refers to the $sessionid
		$purchase_log = $wpdb->get_row(
			"SELECT * FROM `" . WPSC_TABLE_PURCHASE_LOGS .
			"` WHERE `sessionid`= " . $this->cart_data['session_id'] . " LIMIT 1"
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

		// Here we build the array that contains ordered itens, from customer cart
		$items = array();
		$purchase_description = "";
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
					$purchase_description =
						$purchase_description . ' ' .
						( $item->product_name . ' x ' . $item->quantity );
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
						)
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
				)
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
				)
			));
		}

		// Build additional information from the customer data
		$billing_details = wpsc_get_customer_meta();
		$order_id = $billing_details['_wpsc_cart.log_id'][0];
	  	$payer_additional_info = array(
		    'first_name' => $arr_info['billingfirstname'],
		    'last_name' => $arr_info['billinglastname'],
		    //'registration_date' =>
		    'phone'	=> array(
	  		//'area_code' =>
				'number' => $arr_info['billingphone']
			),
	      	'address' => array(
				'zip_code' => $arr_info['billingpostcode'],
				//'street_number' =>
				'street_name' => $arr_info['billingaddress'] . ' / ' .
					$arr_info['billingcity'] . ' ' .
					$arr_info['billingstate'] . ' ' .
					$arr_info['billingcountry']
			)
	  	);

	  	// Create the shipment address information set
	  	$shipments = array(
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
	  	);

	  	// The payment preference
	  	$payment_preference = array (
	  		'transaction_amount' => (float) number_format( $wpsc_cart->calculate_total_price() * (
				(float)get_option('mercadopago_custom_currencyratio') > 0 ?
				(float)get_option('mercadopago_custom_currencyratio') : 1
			), 2 ),
		  	//'token' => $post_from_form[ 'mercadopago_custom' ][ 'token' ],
		  	'description' => $purchase_description,
		  	//'installments' => (int) $post_from_form[ 'mercadopago_custom' ][ 'installments' ],
		    //'payment_method_id' => $post_from_form[ 'mercadopago_custom' ][ 'paymentMethodId' ],
		    'payer' => array(
		    	'email' => $arr_info['billingemail']
		    ),
		    'external_reference' => get_option('mercadopago_custom_invoiceprefix') . $order_id,
		    'statement_descriptor' => get_option('mercadopago_custom_statementdescriptor'),
		    'binary_mode' => (get_option('mercadopago_custom_binary') == "active"),
		    'additional_info' => array(
		        'items' => $items,
		        'payer' => $payer_additional_info,
		        'shipments' => $shipments
		    )
	  	);

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
				$payment_preference[ 'sponsor_id' ] = $sponsor_id;
		}

		return $payment_preference;

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

	function getImagePath($image_name) {
		return plugins_url( 'wpsc-merchants/mercadopago-images/' . $image_name, plugin_dir_path( __FILE__ ) );
	}

}

function _wpsc_filter_mercadopago_customer_notification_raw_message( $message, $notification ) {
	$purchase_log = $notification->get_purchase_log();

	if ( $purchase_log->get( 'gateway' ) == 'WPSC_Merchant_MercadoPago_Custom' )
		$message = get_option( 'mercadopago_custom_description') . "\r\n" . $message;

	return $message;
}

add_filter(
	'wpsc_purchase_log_customer_notification_raw_message',
	'_wpsc_filter_mercadopago_customer_notification_raw_message',
	10, 2
);
add_filter(
	'wpsc_purchase_log_customer_html_notification_raw_message',
	'_wpsc_filter_mercadopago_customer_notification_raw_message',
	10, 2
);

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
	if (isset($_POST['mercadopago_custom_checkoutmessage1'])) {
		update_option('mercadopago_custom_checkoutmessage1', trim($_POST['mercadopago_custom_checkoutmessage1']));
	}
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
	/*if (isset($_POST['mercadopago_custom_coupom'])) {
		update_option('mercadopago_custom_coupom', trim($_POST['mercadopago_custom_coupom']));
	}*/
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

	// labels
	$form_labels = array(
		"form" => array(
			"pay_with_mp" => __( 'Pay With Mercado Pago', 'wpecomm-mercadopago-module' ),
			"tax_fees_message" => __( 'Tax fees applicable in store', 'wpecomm-mercadopago-module' ),
			"shipment_message" => __( 'Shipping service used by store', 'wpecomm-mercadopago-module' ),
			"payment_converted" => __('Payment with converted currency', 'wpecomm-mercadopago-module' ),
			"to" => __('to', 'wpecomm-mercadopago-module' ),
			"label_other_bank" => __( "Other Bank", "wpecomm-mercadopago-module" ),
			"label_choose" => __( "Choose", "wpecomm-mercadopago-module" ),
			"your_card" => __( "Your Card", 'wpecomm-mercadopago-module' ),
			"other_cards" => __( "Other Cards", 'wpecomm-mercadopago-module' ),
	      	"other_card" => __( "Other Card", 'wpecomm-mercadopago-module' ),
	      	"ended_in" => __( "ended in", 'wpecomm-mercadopago-module' ),
			"card_holder_placeholder" => __( " as it appears in your card ...", 'wpecomm-mercadopago-module' ),
			"payment_method" => __( "Payment Method", 'wpecomm-mercadopago-module' ),
			"credit_card_number" => __( "Credit card number", 'wpecomm-mercadopago-module' ),
			"expiration_month" => __( "Expiration month", 'wpecomm-mercadopago-module' ),
			"expiration_year" => __( "Expiration year", 'wpecomm-mercadopago-module' ),
			"year" => __( "Year", 'wpecomm-mercadopago-module' ),
			"month" => __( "Month", 'wpecomm-mercadopago-module' ),
			"card_holder_name" => __( "Card holder name", 'wpecomm-mercadopago-module' ),
			"security_code" => __( "Security code", 'wpecomm-mercadopago-module' ),
			"document_type" => __( "Document Type", 'wpecomm-mercadopago-module' ),
			"document_number" => __( "Document number", 'wpecomm-mercadopago-module' ),
			"issuer" => __( "Issuer", 'wpecomm-mercadopago-module' ),
			"installments" => __( "Installments", 'wpecomm-mercadopago-module' )
		),
		"error" => array(
			//card number
			"205" => __( "Parameter cardNumber can not be null/empty", 'wpecomm-mercadopago-module' ),
			"E301" => __( "Invalid Card Number", 'wpecomm-mercadopago-module' ),
			//expiration date
			"208" => __( "Invalid Expiration Date", 'wpecomm-mercadopago-module' ),
			"209" => __( "Invalid Expiration Date", 'wpecomm-mercadopago-module' ),
			"325" => __( "Invalid Expiration Date", 'wpecomm-mercadopago-module' ),
			"326" => __( "Invalid Expiration Date", 'wpecomm-mercadopago-module' ),
			//card holder name
			"221" => __( "Parameter cardholderName can not be null/empty", 'wpecomm-mercadopago-module' ),
			"316" => __( "Invalid Card Holder Name", 'wpecomm-mercadopago-module' ),
			//security code
			"224" => __( "Parameter securityCode can not be null/empty", 'wpecomm-mercadopago-module' ),
			"E302" => __( "Invalid Security Code", 'wpecomm-mercadopago-module' ),
			//doc type
			"212" => __( "Parameter docType can not be null/empty", 'wpecomm-mercadopago-module' ),
			"322" => __( "Invalid Document Type", 'wpecomm-mercadopago-module' ),
			//doc number
			"214" => __( "Parameter docNumber can not be null/empty", 'wpecomm-mercadopago-module' ),
			"324" => __( "Invalid Document Number", 'wpecomm-mercadopago-module' ),
			//doc sub type
			"213" => __( "The parameter cardholder.document.subtype can not be null or empty", 'wpecomm-mercadopago-module' ),
			"323" => __( "Invalid Document Sub Type", 'wpecomm-mercadopago-module' ),
			//issuer
			"220" => __( "Parameter cardIssuerId can not be null/empty", 'wpecomm-mercadopago-module' )
		)
	);

	$result = validateCredentials_custom(
		get_option('mercadopago_custom_publickey'),
		get_option('mercadopago_custom_accesstoken')
	);
	$store_currency = WPSC_Countries::get_currency_code(absint(get_option('currency_type')));

	// Trigger API to get payment methods and site_id, also validates Public_key/Access_token.
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
						' ' . getCurrencyId_custom( $result['site_id'] ) . ' (' . getCountryName_custom( $result['site_id'] ) . ').' .
						' ' . __( 'Currency conversions should be made outside this module.', 'wpecomm-mercadopago-module' );
				} else if (get_option('mercadopago_custom_currencyconversion') == 'active' && $result['currency_ratio'] != -1 ) {
					$currency_message .= '<img width="12" height="12" src="' .
						plugins_url( 'wpsc-merchants/mercadopago-images/check.png', plugin_dir_path( __FILE__ ) ) . '">' .
						' ' . __( 'CURRENCY CONVERTED: The currency conversion ratio from', 'wpecomm-mercadopago-module' )  . ' ' . $store_currency .
						' ' . __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . getCurrencyId_custom( $result['site_id'] ) .
						__( ' is: ', 'wpecomm-mercadopago-module' ) . $result['currency_ratio'] . ".";
				} else {
					$result['currency_ratio'] = -1;
					$currency_message .= '<img width="12" height="12" src="' .
						plugins_url( 'wpsc-merchants/mercadopago-images/error.png', plugin_dir_path( __FILE__ ) ) . '">' .
						' ' . __( 'ERROR: It was not possible to convert the unsupported currency', 'wpecomm-mercadopago-module' ) . ' ' . $store_currency .
						' '	. __( 'to', 'wpecomm-mercadopago-module' ) . ' ' . getCurrencyId_custom( $result['site_id'] ) . '.' .
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
			<input type='hidden' size='60' value='" .
				json_encode( $form_labels ) .
				"' name='mercadopago_custom_checkoutmessage1' />
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
		<td>" . __('Resume Message', 'wpecomm-mercadopago-module' ) . "</td>
		<td>
			<input type='textarea' size='60' value='" .
			(get_option( 'mercadopago_custom_description') == "" ?
				__( 'Your order has been made with Mercado Pago.', 'wpecomm-mercadopago-module' ) :
				get_option( 'mercadopago_custom_description')) .
			"' name='mercadopago_custom_description' />
			<p class='description'>" .
				__( "Message shown to client in transaction resume.", 'wpecomm-mercadopago-module' ) . "
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


  //=============================================================================

  /*
  // Customer's Card Feature, add only if it has issuer id
  if ( array_key_exists( 'token', $post_from_form[ 'mercadopago_custom' ] ) ) {
  	$payment_preference[ 'metadata' ][ 'token' ] = $post_from_form[ 'mercadopago_custom' ][ 'token' ];
      if ( array_key_exists( 'issuer', $post_from_form[ 'mercadopago_custom' ] ) ) {
      	if ( !empty( $post_from_form[ 'mercadopago_custom' ][ 'issuer' ] ) ) {
      		$payment_preference[ 'issuer_id' ] = (integer) $post_from_form[ 'mercadopago_custom' ][ 'issuer' ];
      	}
      }
      if ( !empty( $post_from_form[ 'mercadopago_custom' ][ 'CustomerId' ] ) ) {
  		$payment_preference[ 'payer' ][ 'id' ] = $post_from_form[ 'mercadopago_custom' ][ 'CustomerId' ];
  	}
	}
  */

	// create Mercado Pago preference
	/*$billing_details = wpsc_get_customer_meta();
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
	);*/

	// Do not set IPN url if it is a localhost!
  /*$notification_url = get_site_url() . '/wpecomm-mercadopago-module/?wc-api=WC_WPeCommMercadoPago_Gateway';
  if ( !strrpos( $notification_url, "localhost" ) ) {
      $preferences['notification_url'] = workaroundAmperSandBug( $notification_url );
  }*/

	// log created preferences
	/*if ( get_option('mercadopago_custom_debug') == "Yes" ) {
		debug_to_console_custom(
			"@" . __FUNCTION__ . " - " .
			"Preferences created, now processing it: " .
			json_encode($payment_preference, JSON_PRETTY_PRINT)
		);
	}

	draw_payment_form_wpecomm_mp_custom($payment_preference, $wpsc_cart);
}*/

function draw_payment_form_wpecomm_mp_custom($preferences, $wpsc_cart) {




  /*

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
	}*/

	// show page
	//get_header();
		//$page = '<div style="position: relative; margin: 20px 0;" >';
		//$page .= '<div style="margin: 0 auto; width: 1080px; ">';
			//$page .= '<h3>' . get_option('mercadopago_custom_checkoutmessage3') . '</h3>';
			//$page .= $html;
		//$page .= '</div>';
		//$page .= '</div>';
		//echo $page;
		echo $html;
		//'<link rel="stylesheet" type="text/css" href="' . plugins_url( 'wpsc-merchants/mercadopago-libs/custom_checkout_mercadopago.css', plugin_dir_path( __FILE__ ) ) . '" />' .
	//get_footer();

	exit;
}

/*===============================================================================
	FUNCTIONS TO GENERATE VIEWS
================================================================================*/

function build_years_for_date() {
	$years_options = "";
	for ($x=date("Y"); $x<= date("Y") + 10; $x++) {
		$years_options .= '<option value="' . $x . '"> ' . $x . ' </option>';
	}
	return $years_options;
}

function payment_methods_customer_cards($customer_cards, $form_labels) {
	$select_customer_cards = "";
	foreach ($customer_cards as $card) {
		$select_customer_cards .= '
		<option value="' . $card["id"] . '"
			first_six_digits="' . $card["first_six_digits"] . '"
			last_four_digits="' . $card["last_four_digits"] . '"
			security_code_length="' . $card["security_code"]["length"] . '"
			type_checkout="customer_and_card"
			payment_method_id="' . $card["payment_method"]["id"] . '">' .
    		ucfirst($card["payment_method"]["name"]) . ' ' .
    		$form_labels['form']['ended_in'] . ' ' . $card["last_four_digits"] . '
  	</option>';
  }
	return $select_customer_cards;
}

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

/*function coupom() {
	$coupom = get_option('mercadopago_custom_coupom');
	$coupom = $coupom === false || is_null($coupom) ? "inactive" : $coupom;
	$coupom_options = array(
		array("value" => "active", "text" => "Active"),
		array("value" => "inactive", "text" => "Inactive")
	);
	$select_binary = '<select name="mercadopago_custom_coupom" id="coupom">';
	foreach ($coupom_options as $op_coupom) :
		$selected = "";
		if ($op_coupom['value'] == $coupom) :
		$selected = 'selected="selected"';
		endif;
		$select_binary .=
			'<option value="' . $op_coupom['value'] .
			'" id="coupom-' . $op_coupom['value'] .
			'" ' . $selected . '>' . __($op_coupom['text'], "wpecomm-mercadopago-module") .
			'</option>';
	endforeach;
	$select_binary .= "</select>";
	return $select_binary;
}*/

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
						getCurrencyId_custom( $result['site_id'] )
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
	return $store_currency_code == getCurrencyId_custom($site_id);
}

// Fix to URL Problem : #038; replaces & and breaks the navigation
function workaroundAmperSandBug_custom( $link ) {
	return str_replace('&#038;', '&', $link);
}

function print_r_reverse($in) {
  $lines = explode("\n", trim($in));
  if (trim($lines[0]) != 'Array') {
    // bottomed out to something that isn't an array
    return $in;
  } else {
    // this is an array, lets parse it
    if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
      // this is a tested array/recursive call to this function
      // take a set of spaces off the beginning
      $spaces = $match[1];
      $spaces_length = strlen($spaces);
      $lines_total = count($lines);
      for ($i = 0; $i < $lines_total; $i++) {
        if (substr($lines[$i], 0, $spaces_length) == $spaces) {
          $lines[$i] = substr($lines[$i], $spaces_length);
        }
      }
    }
    array_shift($lines); // Array
    array_shift($lines); // (
    array_pop($lines); // )
    $in = implode("\n", $lines);
    // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
    preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
    $pos = array();
    $previous_key = '';
    $in_length = strlen($in);
    // store the following in $pos:
    // array with key = key of the parsed array's item
    // value = array(start position in $in, $end position in $in)
    foreach ($matches as $match) {
      $key = $match[1][0];
      $start = $match[0][1] + strlen($match[0][0]);
      $pos[$key] = array($start, $in_length);
      if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
        $previous_key = $key;
    }
    $ret = array();
    foreach ($pos as $key => $where) {
      // recursively see if the parsed out value is an array too
      $ret[$key] = print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
    }
    return $ret;
	}
}

function debug_to_console_custom($data) {
	// TODO: review debug function as it causes header to be sent
  /*$output  = "<script>console.log( '[WPeComm-Mercado-Pago-Module Logger] => ";
  $output .= json_encode(print_r($data, true), JSON_PRETTY_PRINT);
  $output .= "' );</script>";
  echo $output;*/
}

?>
