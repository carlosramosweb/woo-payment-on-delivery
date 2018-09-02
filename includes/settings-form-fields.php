<?php
/*
 * Exit if file accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shipping_methods = array();

if ( is_admin() ) {
	foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
		$title = empty( $method->method_title ) ? ucfirst( $method->id ) : $method->method_title; 
        $shipping_methods[ strtolower( $method->id ) ] = esc_html( $title ); 
	}
}

return array(
	// Fields Native
	'enabled' => array(
		'title' 	=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 		=> 'checkbox',
		'label' 	=> __( 'Enable Cash on Delivery', 'woo-payment-on-delivery' ),
		'default' 	=> 'yes'
	),
	'title' => array(
		'title' 		=> __( 'Title', 'woo-payment-on-delivery' ),
		'type' 			=> 'text',
		'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woo-payment-on-delivery' ),
		'default' 		=> __( 'Delivery', 'woo-payment-on-delivery' ),
		'desc_tip'   	=> true,
	),
	'description' => array(
		'title'       	=> __( 'Description', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Payment method description that the customer will see on your checkout.', 'woo-payment-on-delivery' ),
		'default'     	=> __( 'Information provided for payment...', 'woo-payment-on-delivery' ),
		'desc_tip'    	=> true,
	),
	// Payment Types
	'paymenttypes' => array(
		'title'    => __( 'Accepted Types', 'woo-payment-on-delivery' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 400px;',
		'desc_tip' => __( 'Select the payment types to accept.', 'woo-payment-on-delivery' ),
		'options'  			=> array(
			'money'			=>  __( 'Money', 'woo-payment-on-delivery' ),
			'paycheck'		=>  __( 'Paycheck', 'woo-payment-on-delivery' ),
			'debitcard'		=>  __( 'Debit Card', 'woo-payment-on-delivery' ),
			'creditcard'	=>  __( 'Credit Card', 'woo-payment-on-delivery' ),
			'voucher'		=>  __( 'Voucher', 'woo-payment-on-delivery' ),
		),
		'default' => array( 'money', 'paycheck', 'debitcard', 'creditcard', 'foodcard' ),
	),
	// Notice
	'notice_money' => array(
		'title'       	=> __( 'Notice Money', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Select your payment method.', 'woo-payment-on-delivery' ),
		'default'       => '',
		'desc_tip'    	=> true,
	),
	'notice_paycheck' 	=> array(
		'title'       	=> __( 'Notice Paycheck', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Indicate whether the requirements.', 'woo-payment-on-delivery' ),
		'default'       => '',
		'desc_tip'    	=> true,
	),
	'notice_card' => array(
		'title'       	=> __( 'Notice Card', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Description inform the client cards/accepted flags.', 'woo-payment-on-delivery' ),
		'default'       => '',
		'desc_tip'    	=> true,
	),
	'notice_voucher' => array(
		'title'       	=> __( 'Voucher', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Indicate whether the requirements.', 'woo-payment-on-delivery' ),
		'default'       => '',
		'desc_tip'    	=> true,
	),
	// Enable
	'enable_for_methods' => array(
		'title'             => __( 'Enable for shipping methods', 'woo-payment-on-delivery' ),
		'type'              => 'multiselect',
		'class'             => 'wc-enhanced-select',
		'css'               => 'width: 450px;',
		'default'           => '',
		'description'       => __( 'If Payment on Delivery is only available for certain methods, set it up here.', 'woo-payment-on-delivery' ),
		'options'           => $shipping_methods,
		'desc_tip'          => true,
		'custom_attributes' => array(
		'data-placeholder' 	=> __( 'Select shipping methods', 'woo-payment-on-delivery' )
		)
	),
);