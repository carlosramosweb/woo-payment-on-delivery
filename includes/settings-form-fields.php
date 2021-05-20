<?php
/*
 * Sair se o arquivo for acessado diretamente
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shipping_methods = array();

foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
	$title = empty( $method->method_title ) ? ucfirst( $method->id ) : $method->method_title; 
    $shipping_methods[ strtolower( $method->id ) ] = esc_html( $title ); 
}

return array(
	// Campos nativos
	'enabled' 		=> array(
		'title' 	=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 		=> 'checkbox',
		'label' 	=> __( 'Habilitar o pagamento no Delivery', 'woo-payment-on-delivery' ),
		'default' 	=> 'yes'
	),
	'title' 			=> array(
		'title' 		=> __( 'Title', 'woo-payment-on-delivery' ),
		'type' 			=> 'text',
		'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woo-payment-on-delivery' ),
		'default' 		=> __( 'Delivery', 'woo-payment-on-delivery' ),
		'desc_tip'   	=> true,
	),
	'description' 		=> array(
		'title'       	=> __( 'Description', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Escreva aqui alguma informação pertinente para o cliente.', 'woo-payment-on-delivery' ),
		'default'     	=> __( 'Escolha uma forma de pagamento abaixo.', 'woo-payment-on-delivery' ),
		'desc_tip'    	=> true,
	),
	// Padrão para o status dos pedidos
	'note_enabled' 	=> array(
		'title' 	=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 		=> 'checkbox',
		'label' 	=> __( 'Exibição o detalhe de pagamento na nota do pedido.', 'woo-payment-on-delivery' ),
		'default' 	=> 'yes'
	),
	'status' 		=> array(
		'title'    	=> __( 'Status do Pedido', 'woo-payment-on-delivery' ),
		'type'     	=> 'select',
		'class'    	=> 'chosen_select',
		'css'      	=> 'width: 400px;',
		'desc_tip' 	=> __( 'Selecione um status para ser gerado no ato da compra online.', 'woo-payment-on-delivery' ),
		'options'  			=> array(
			'on-hold'		=>  __( 'Aguardando Pagamento (Padrão)', 'woo-payment-on-delivery' ),
			'processing'	=>  __( 'Processando', 'woo-payment-on-delivery' ),
			'completed'		=>  __( 'Concluído', 'woo-payment-on-delivery' ),
		),
		'default' 			=> 'on-hold',
	),
	// Tipos de pagamento
	'paymenttypes' => array(
		'title'    => __( 'Tipos aceitos', 'woo-payment-on-delivery' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 400px;',
		'desc_tip' => __( 'Selecione um tipo de pagamento aceito.', 'woo-payment-on-delivery' ),
		'options'  			=> array(
			'money'			=>  __( 'Money', 'woo-payment-on-delivery' ),
			'pix'			=>  __( 'Pix', 'woo-payment-on-delivery' ),
			'paycheck'		=>  __( 'Paycheck', 'woo-payment-on-delivery' ),
			'debitcard'		=>  __( 'Debit Card', 'woo-payment-on-delivery' ),
			'creditcard'	=>  __( 'Credit Card', 'woo-payment-on-delivery' ),
			'voucher'		=>  __( 'Voucher', 'woo-payment-on-delivery' ),
			'multibanco'	=>  __( 'MultiBanco', 'woo-payment-on-delivery' ),
		),
		'default' 			=> array( 'money', 'paycheck', 'debitcard', 'creditcard', 'voucher', 'multibanco' ),
	),
	// Pix
	'pix_key' 			=> array(
		'title' 		=> __( 'A Chave do Pix', 'woo-payment-on-delivery' ),
		'type' 			=> 'text',
		'description' 	=> __( 'Escreva aqui a chave do PIX.', 'woo-payment-on-delivery' ),
		'default' 		=> __( 'Ex: PIX: 999.999.999-99', 'woo-payment-on-delivery' ),
		'desc_tip'   	=> true,
	),
	'pix_description' 	=> array(
		'title'       	=> __( 'Mais Informações', 'woo-payment-on-delivery' ),
		'type'       	=> 'textarea',
		'description' 	=> __( 'Escreva aqui demais informações para o cliente.', 'woo-payment-on-delivery' ),
		'default'     	=> __( 'Ex: Após finalizar a compra vamos disponibilizar a Chave do PIX para o pagamento.', 'woo-payment-on-delivery' ),
		'desc_tip'    	=> true,
	),
	// Cartão de Débito
	'debit_card_disable' 	=> array(
		'title' 			=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 				=> 'checkbox',
		'label' 			=> __( '(Cartão de Débito) Se habilitado não será exibido a lista de bandeiras no checkout.', 'woo-payment-on-delivery' ),
		'default' 			=> 'no'
	),
	'debit_card' 	=> array(
		'title'    	=> __( 'Debit Card', 'woo-payment-on-delivery' ),
		'type'     	=> 'multiselect',
		'class'    	=> 'chosen_select',
		'css'      	=> 'width: 400px;',
		'desc_tip' 	=> __( 'Selecione um tipo de pagamento aceito.', 'woo-payment-on-delivery' ),
		'options'  				=> array(
			'Mastercard'		=>  __( 'Mastercard', 'woo-payment-on-delivery' ),			
			'Visa Electron'		=>  __( 'Visa Electron', 'woo-payment-on-delivery' ),
			'Elo'				=>  __( 'Elo', 'woo-payment-on-delivery' ),
			'Discover Network'	=>  __( 'Discover Network', 'woo-payment-on-delivery' ),
			'Cabal'				=>  __( 'Cabal', 'woo-payment-on-delivery' ),
			'Sicredi'			=>  __( 'Sicredi', 'woo-payment-on-delivery' ),
			'Redeshop'			=>  __( 'Redeshop', 'woo-payment-on-delivery' ),
		),
		'default' 				=> array( 'Mastercard', 'Visa', 'American Express', 'Elo', 'Discover Network', 'Cabal', 'Sicredi' ),
	),
	// Cartão de Crédito
	'credit_card_disable' 	=> array(
		'title' 			=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 				=> 'checkbox',
		'label' 			=> __( '(Cartão de Crédito) Se habilitado não será exibido a lista de bandeiras no checkout.', 'woo-payment-on-delivery' ),
		'default' 			=> 'no'
	),
	'credit_card' 	=> array(
		'title'    	=> __( 'Credit Card', 'woo-payment-on-delivery' ),
		'type'     	=> 'multiselect',
		'class'    	=> 'chosen_select',
		'css'      	=> 'width: 400px;',
		'desc_tip' 	=> __( 'Selecione um tipo de pagamento aceito.', 'woo-payment-on-delivery' ),
		'options'  				=> array(
			'Mastercard'		=>  __( 'Mastercard', 'woo-payment-on-delivery' ),
			'Visa'				=>  __( 'Visa', 'woo-payment-on-delivery' ),
			'American Express'	=>  __( 'American Express', 'woo-payment-on-delivery' ),
			'Hipercard'			=>  __( 'Hipercard', 'woo-payment-on-delivery' ),
			'Hiper'				=>  __( 'Hiper', 'woo-payment-on-delivery' ),
			'Diners Club'		=>  __( 'Diners Club', 'woo-payment-on-delivery' ),
			'Discover'			=>  __( 'Discover', 'woo-payment-on-delivery' ),
			'Aura'				=>  __( 'Aura', 'woo-payment-on-delivery' ),
			'Cabal'				=>  __( 'Cabal', 'woo-payment-on-delivery' ),
			'Soro Cred'			=>  __( 'Soro Cred', 'woo-payment-on-delivery' ),
			'Sicredi'			=>  __( 'Sicredi', 'woo-payment-on-delivery' ),
			'Cooper Card'		=>  __( 'Cooper Card', 'woo-payment-on-delivery' ),
			'Avista'			=>  __( 'Avista', 'woo-payment-on-delivery' ),
			'Mais!'				=>  __( 'Mais!', 'woo-payment-on-delivery' ),
			'UnionPay'			=>  __( 'UnionPay', 'woo-payment-on-delivery' ),
		),
		'default' 				=> array( 'Mastercard', 'Visa', 'American Express', 'Hipercard', 'Hiper', 'Diners Club', 'Discover', 'Aura', 'Cabal', 'Soro Cred', 'Sicredi', 'Cooper Card', 'Avista', 'Mais!', 'UnionPay' ),
	),
	// Cartão de Voucher
	'voucher_card_disable' 	=> array(
		'title' 			=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 				=> 'checkbox',
		'label' 			=> __( '(Voucher) Se habilitado não será exibido a lista de bandeiras no checkout.', 'woo-payment-on-delivery' ),
		'default' 			=> 'no'
	),
	'voucher_card' => array(
		'title'    => __( 'Voucher Card', 'woo-payment-on-delivery' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'css'      => 'width: 400px;',
		'desc_tip' => __( 'Selecione um tipo de pagamento aceito.', 'woo-payment-on-delivery' ),
		'options'  			=> array(
			'Ticket'		=>  __( 'Ticket', 'woo-payment-on-delivery' ),
			'Sodexo'		=>  __( 'Sodexo', 'woo-payment-on-delivery' ),
			'GreenCard'		=>  __( 'GreenCard', 'woo-payment-on-delivery' ),
			'Planvale'		=>  __( 'Planvale', 'woo-payment-on-delivery' ),
			'Nutricash'		=>  __( 'Nutricash', 'woo-payment-on-delivery' ),
			'Verocheque'	=>  __( 'Verocheque', 'woo-payment-on-delivery' ),
			'Cooper card'	=>  __( 'Cooper card', 'woo-payment-on-delivery' ),
			'Soro Cred'		=>  __( 'Soro Cred', 'woo-payment-on-delivery' ),
			'Cabal'			=>  __( 'Cabal', 'woo-payment-on-delivery' ),
			'BNB Clube'		=>  __( 'BNB Clube', 'woo-payment-on-delivery' ),
			'VB'			=>  __( 'VB', 'woo-payment-on-delivery' ),
			'VR'			=>  __( 'VR', 'woo-payment-on-delivery' ),
		),
		'default' 			=> array( 'Ticket', 'Sodexo', 'GreenCard', 'Planvale', 'Nutricash', 'Verocheque', 'Cooper card', 'Soro Cred', 'Cabal', 'BNB Clube', 'VB' ),
	),
	// Habilitar
	'enable_for_methods' 	=> array(
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
	// Habilitar
	'enable_for_methods_all' => array(
		'title' 			=> __( 'Enable/Disable', 'woo-payment-on-delivery' ),
		'type' 				=> 'checkbox',
		'label' 			=> __( 'Para todos os métodos de entrega ou para produtos virtuais.', 'woo-payment-on-delivery' ),
		'default' 			=> 'no'
	),
);
