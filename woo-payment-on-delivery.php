<?php
/*---------------------------------------------------------
Plugin Name: Woo Payment On Delivery 
Plugin URI: https://wordpress.org/plugins/woo-payment-on-delivery/
Author: carlosramosweb
Author URI: http://www.criacaocriativa.com.br/plugins/
Donate link: http://www.criacaocriativa.com.br/
Description: Receba em dinheiro, cheque, no cartão de crédito, débito e ou cartão alimentação (voucher) no ato da entrega.
Text Domain: woo-payment-on-delivery
Domain Path: /languages/
Version: 1.1.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html 
------------------------------------------------------------*/

/*
 * Exit if file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Cash On Delivery Gateway for WooCommerce
 */
function init_woo_payment_on_delivery_gateway_class() {	
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
	
	class Woo_Payment_On_Delivery extends WC_Payment_Gateway {		
		public function __construct() {	
		
			// Loads the plugin text on the website.
			add_action( 'plugins_loaded', array( $this, 'woo_load_plugin_textdomain' ) );
			
			// Initialize order
			$this->order = new WC_Order( absint( get_query_var( 'order-pay' ) ) );	
					
			// Global variables
			$this->id                 = 'woo_payment_on_delivery';
			$this->has_fields         = true;
			$this->enabled 			=  "no";
			$this->method_title       = __( 'Delivery', 'woo-payment-on-delivery' );
			$this->method_description = __( 'Add new form of payment on delivery.', 'woo-payment-on-delivery' );
			$this->supports           = array(
				'products',
			);
			
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();	
					
			// Define user set variables
			$this->title          	= $this->get_option( 'title' );
			$this->description    	= $this->get_option( 'description' );
			$this->paymenttypes   	= $this->get_option( 'paymenttypes' );
			
			// Notices
			$this->notice_money    	= $this->get_option( 'notice_money' );
			$this->notice_card    	= $this->get_option( 'notice_card' );
			$this->notice_paycheck	= $this->get_option( 'notice_paycheck' );
			$this->notice_voucher	= $this->get_option( 'notice_voucher' );
			
			// Active if delivery
       		$this->enable_for_methods = $this->get_option( 'enable_for_methods', array() );	
			
			// Save settings
			if ( is_admin() ) {				
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );				
			}		
				
			// Add hooks
			add_action( 'woocommerce_card_delivery', array( $this, 'payment_page' ) );	
		}
		
		/**
		 * Load the text field plugin for translation.
		 */
		public function woo_load_plugin_textdomain() {
			load_plugin_textdomain( 'woo-payment-on-delivery', false, plugins_url(plugin_basename( dirname(__FILE__))) . '/languages/' );
		}
		
		// Admin options
		public function admin_options() {				
			echo '<h3> ' . __( 'Payment on Delivery', 'woo-payment-on-delivery' ) . ' </h3>';	
			echo '<table class="form-table" id="settings-block">';							
			$this->generate_settings_html();
			echo '</table>';
		}
		
		// Initialize fields
		public function init_form_fields() {			
			$this->form_fields = include( 'includes/settings-form-fields.php' );
		}
		
		// Process payment
		public function process_payment( $order_id ) {
			global $woocommerce;
			
			// Get variables
			$card_machine_indicated = $_REQUEST['card_machine_indicated'];
			$flagcard_indicated = $_REQUEST['flagcard_indicated'];
			$woo_cash_delivery = $_REQUEST['woo_cash_delivery'];
			
			// If Empty
			if ( empty($card_machine_indicated) or $card_machine_indicated == '' ) {
							
				wc_add_notice(__('Error! To place your order needs to select a type.', 'woo-payment-on-delivery' ) . $error_message, 'error' );
				return;
				
			} else if ( $card_machine_indicated == 'money' ) { // If for Money
			
				if ($woo_cash_delivery < $woocommerce->cart->total && $woo_cash_delivery != '0'
				or $woo_cash_delivery < $woocommerce->cart->total && $woo_cash_delivery == '') {
										
					wc_add_notice( sprintf(__('The value reported in the change should be greater than the total of your order is <strong>%s %s.</strong>', 'woo-payment-on-delivery', 'woo-payment-on-delivery' ), get_woocommerce_currency_symbol(), number_format($woocommerce->cart->total, 2, ',', '')) . $error_message, 'error' );
					return;
					
				} else {
					
					$order = new WC_Order( $order_id );	
					$current_user = wp_get_current_user();
					$order->update_status('on-hold', __('Awaiting money payment.', 'woo-payment-on-delivery'));
					
					// Add Order Note
					if($woo_cash_delivery == 0) {
						$order_note .= __( 'No change was reported.', 'woo-payment-on-delivery' );
						$order->add_order_note( $order_note, $current_user->display_name );
					} else {
						$order_note .= __( 'Bring change for: ', 'woo-payment-on-delivery' );
						$order_note .= esc_attr($woo_cash_delivery);
						$order->add_order_note( $order_note, $current_user->display_name );
					}
					
					// Reduce stock levels
					//$order->reduce_order_stock();
					
					// Empty Cart WooCommerce
					$woocommerce->cart->empty_cart();
							
					return array(
						'result'    => 'success',
						'redirect'  => $this->get_return_url( $order )
					);
				}
				
			} else if ( $card_machine_indicated == 'paycheck' ) {// If for Paycheck
			
				$order = new WC_Order( $order_id );	
				$current_user = wp_get_current_user();
				$order->update_status('on-hold', __('Awaiting cheque payment.', 'woo-payment-on-delivery'));
				
				// Add Order Note
				if( !empty($card_machine_indicated) ) {
					$order_note .= __( 'Payment will be by paycheck!', 'woo-payment-on-delivery' );
					$order->add_order_note( $order_note, $current_user->display_name );
				}
				
				// Reduce stock levels
				//$order->reduce_order_stock();
				
				// Empty Cart WooCommerce
				$woocommerce->cart->empty_cart();
						
				return array(
					'result'    => 'success',
					'redirect'  => $this->get_return_url( $order )
				);
				
			} else if ( !empty($card_machine_indicated) && $card_machine_indicated == 'debitcard'
			or !empty($card_machine_indicated) && $card_machine_indicated == 'creditcard'
			or !empty($card_machine_indicated) && $card_machine_indicated == 'voucher' ) { // If for Card
				
				if (empty($flagcard_indicated)) {
					wc_add_notice( __('Error! To place your order needs to select a flag.', 'woo-payment-on-delivery') . $error_message, 'error' );
					return;
				} else {
			
					$order = new WC_Order( $order_id );	
					$current_user = wp_get_current_user();
					$order->update_status('on-hold', __('Awaiting card payment.', 'woo-payment-on-delivery'));
					
					// Add Order Note Card
					if( !empty($card_machine_indicated) ) {
						$order_note .= __( 'Take the machine indicated card: ', 'woo-payment-on-delivery' );
						$order_note .= esc_attr(woo_check_the_name_card_on($card_machine_indicated));
						$order_note .= ' -> ';
						$order_note .= esc_attr($flagcard_indicated);
						$order->add_order_note( $order_note, $current_user->display_name );
					} else {
						$order_note .= __( 'The type of card is not indicated! ', 'woo-payment-on-delivery' );
						$order->add_order_note( $order_note, $current_user->display_name );
					}
					
					// Reduce stock levels
					//$order->reduce_order_stock();
					
					// Empty Cart WooCommerce
					$woocommerce->cart->empty_cart();
							
					return array(
						'result'    => 'success',
						'redirect'  => $this->get_return_url( $order )
					);	
				}
				
			} else {		
				// Error 
				wc_add_notice( __('There was an unexpected error, please contact the manager of this store.', 'woo-payment-on-delivery') . $error_message, 'error' );
				return;	
				
			}
		}			
		
		// Icon
		public function get_icon() {
			$icon .= '<img src="' . plugins_url( '/woo-payment-on-delivery/images/icon-types-delivery.png', dirname(__FILE__) ) . '" alt="'.__( 'Payment on Delivery', 'woo-payment-on-delivery' ).'" /> ';	
				
			return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
		}
		/**
		 * Return the gateway's description.
		 *
		 * @return string
		 */
		public function get_description() {
			
			$select_flag = __( 'Select a flag', 'woo-payment-on-delivery' );			
			?>
<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("select[id=card_machine_indicated]").change(function() {
					jQuery("p.woo_payment_indicated").empty();
					switch(jQuery("#card_machine_indicated option:selected").val()) {
						case "money":
							jQuery("p.woo_payment_indicated").append("<input id='woo_cash_delivery' class='input-text' type='number' style='padding:10px;' maxlength='20' placeholder='0,00' pattern='-?\d+(,\d{2})?' step='1.00' name='woo_cash_delivery' value='0'/><span><?php echo __('*Enter 0 to inform you that do not need to change.', 'woo-payment-on-delivery'); ?></span><hr/><span><?php echo $this->notice_money; ?></span>");
							break;
						case "paycheck":
							jQuery("p.woo_payment_indicated").append("<input id='woo_paycheck_delivery' type='hidden' name='woo_paycheck_delivery' value='paycheck'/><?php if(!empty($this->notice_paycheck)) { ?><hr/><span><?php echo $this->notice_paycheck; ?></span><?php } ?>");
							break;
						case "debitcard":
							jQuery("p.woo_payment_indicated").append("<select style='padding:10px;' id='flagcard_indicated' name='flagcard_indicated'><option value=''><?php echo $select_flag; ?></option><option value='Mastercard'>Mastercard</option><option value='Visa'>Visa</option><option value='American Express'>American Express</option><option value='Elo'>Elo</option><option value='Discover Network'>Discover Network</option><option value='Cabal'>Cabal</option><option value='Sicredi'>Sicredi</option></select><?php if(!empty($this->notice_card)) { ?><hr/><span><?php echo $this->notice_card; ?></span><?php } ?>");
							break;
						case "creditcard":
							jQuery("p.woo_payment_indicated").append("<select style='padding:10px;' id='flagcard_indicated' name='flagcard_indicated'><option value=''><?php echo $select_flag; ?></option><option value='Mastercard'>Mastercard</option><option value='Visa'>Visa</option><option value='Hipercard'>Hipercard</option><option value='Hiper'>Hiper</option><option value='Diners Club'>Diners Club</option><option value='Hipercard'>Hipercard</option><option value='Discover'>Discover</option><option value='Aura'>Aura</option><option value='Cabal'>Cabal</option><option value='Soro Cred'>Soro Cred</option><option value='Sicredi'>Sicredi</option><option value='Cooper Card'>Cooper Card</option><option value='Avista'>Avista</option><option value='Mais!'>Mais!</option><option value='UnionPay'>UnionPay</option></select><hr/><span><?php echo $this->notice_card; ?></span>");
							break;
						case "voucher":
							jQuery("p.woo_payment_indicated").append("<select style='padding:10px;' id='flagcard_indicated' name='flagcard_indicated'><option value=''><?php echo $select_flag; ?></option><option value='Ticket'>Ticket</option><option value='Sodexo'>Sodexo</option><option value='GreenCard'>GreenCard</option><option value='Planvale'>Planvale</option><option value='Nutricash'>Nutricash</option><option value='Verocheque'>Verocheque</option><option value='Cooper card'>Cooper card</option><option value='Soro Cred'>Soro Cred</option><option value='Cabal'>Cabal</option><option value='BNB Clube'>BNB Clube</option><option value='VB'>VB</option></select><hr/><span><?php echo $this->notice_voucher; ?></span>");
							break;
						default: "";
					}
				});
			});
			</script>
            <?php	
			$select_the_type = 	__( 'Select the type', 'woo-payment-on-delivery' );
			
			$default_fields .= '<p class="form-row form-row-wide hide-if-token">
			<select style="padding:8px 10px; margin-bottom:10px;" id="card_machine_indicated" name="card_machine_indicated">';
			$default_fields .= '<option value="">'.$select_the_type.'</option>';
			foreach ($this->paymenttypes as $paymenttypes) {
				$default_fields .= '<option value="'.$paymenttypes.'">'.woo_check_the_name_card_on($paymenttypes).'</option>';
			}
			$default_fields .= '</select></p>';			
			$default_fields .= '<p class="form-row woo_payment_indicated"></p>';			
			$description = apply_filters( 'woocommerce_gateway_description', $this->description, $this->id );			
			return $description . $default_fields;
		}
		// =>
	}
	
	// check the name of the delivery
	function woo_check_the_name_card_on( $paymenttypes ) {			
		$paymenttypes = strtolower($paymenttypes);
		switch ($paymenttypes) {
			case 'money':
				return __( 'Money', 'woo-payment-on-delivery' );
				break;
			case 'paycheck':
				return __( 'Paycheck', 'woo-payment-on-delivery' );
				break;
			case 'debitcard':
				return __( 'Debit Card', 'woo-payment-on-delivery' );
				break;
			case 'creditcard':
				return __( 'Credit Card', 'woo-payment-on-delivery' );
				break;
			case 'voucher':
				return __( 'Voucher', 'woo-payment-on-delivery' );
				break;
		}
	}
	
	// Add custom payment gateway
	function add_woo_payment_on_delivery_gateway_class( $methods ) {
		
		// Search forms of deliveries
		if ( !is_admin() ) {
			$cash_on_delivery_settings = get_option( 'woocommerce_woo_payment_on_delivery_settings' );
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );				
			$chosen_methods_meta = $chosen_methods[0];	

		} else {
			$methods[] = 'Woo_Payment_On_Delivery'; 		
			return $methods;
		}
		
		// Valid if it is advanced deliveries
		if (is_numeric($chosen_methods_meta)) { 
			$chosen_methods_REQUEST = get_REQUEST( $chosen_methods_meta );
			if (!empty($chosen_methods_REQUEST->post_type) && $chosen_methods_REQUEST->post_type == "was") {
				$chosen_methods_meta = "advanced_shipping";
			} else {
				$chosen_methods_meta;	
			}
		}
		
		// Validate and compare the strings to open the payment method
		if ($cash_on_delivery_settings[enabled] == "yes") {
			if( is_array($cash_on_delivery_settings[enable_for_methods]) ) {
				foreach ($cash_on_delivery_settings[enable_for_methods] as $enable_for_methods ) {
					
					if( strstr($chosen_methods[0], $enable_for_methods) ) {	
						$methods[] = 'Woo_Payment_On_Delivery';
						return $methods;
					}
					
				}
			} else {
				return $methods;
			}
			
		} else {
			return $methods;
		}
		
	}
	add_filter( 'woocommerce_payment_gateways', 'add_woo_payment_on_delivery_gateway_class' );
}
add_action( 'plugins_loaded', 'init_woo_payment_on_delivery_gateway_class' );