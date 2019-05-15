<?php

/**
 * Plugin Name: WooCommerce User Shipping
 * Plugin URI:  https://
 * Description: Create Seperate Shipping Options based on user role
 * Version:     1.0
 * Author:      Matthaus Klute
 * Author URI:  https://mattha.us/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wc-user-shipping
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check to make sure WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

	function user_shipping_init() {
    // only run if there's no other class with this name
	    if ( ! class_exists('WC_User_Shipping')){
	        class WC_User_Shipping extends WC_Shipping_Method{
				/**
				 * Create new shipping method
				 *
				 * @access public
				 * 
				 */
	            public function __construct(){
	            	$this->id 					= 'WC_User_Shipping';
	            	$this->method_title			= __('User Shipping');
	            	$this->method_description 	= __( 'Flat rate shipping option based on user role' );	
	            	$this->title 				= __( 'User Shipping' ); //Forcing Title
	            	$this->init();

	            	// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

	            }
	            /**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				public function init() {
					$this->init_form_fields();
					$this->init_settings();

	            	$this->enabled	= $this->get_option( 'enabled' );
	            	$this->user_role = $this->get_option( 'user_role' );

				}
				/**
				 * Initialise Gateway Settings Form Fields
				 */
				public function init_form_fields() {
					$user_roles = array_keys( get_editable_roles() );
					$user_roles = array_combine( $user_roles, $user_roles );

					$this->form_fields = array(
						'enabled' => array(
							'title'		=> __( 'Enable/Disable', 'woocommerce' ),
							'type'		=> 'checkbox',
							'label'		=> __( 'Enable User Role Shipping rates' ),
							'default'	=> 'yes'
						),
						'user_role' => array(
							'title'		=> __( 'Select User Role', 'woocommerce' ),
							'type'		=> 'select',
							'label'		=> __( 'Add shipping for which User Role' ),
							'default'	=> '',
							'options' 	=> $user_roles
						)
					);
				}




				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = array() ) {
					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => '19.99',
						'calc_tax' => 'false'
					);
					// Register the rate
					$this->add_rate( $rate );
				}
	        }
	    }
    }
    //Call the user shipping function
  	add_action( 'woocommerce_shipping_init', 'user_shipping_init' );


	/**
	 * Restister Shipping Method
	 * Need to move the conditional 
	 *
	 * 
	 * 
	 */
  	function add_user_shipping( $methods ) {
  		$user = wp_get_current_user();
  		if ( in_array( 'um_custom_role_2', (array) $user->roles)) {
	  		$methods['wc_user_shipping'] = 'WC_User_Shipping';
	  		return $methods;
	  	} else {
	  		$methods['wc_user_shipping'] = 'WC_User_Shipping';
	  		return $methods;
	  	}
  	}

  		add_filter( 'woocommerce_shipping_methods', 'add_user_shipping');

}
