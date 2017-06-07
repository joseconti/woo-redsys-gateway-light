<?php

if( !defined('ABSPATH') ){
	exit;
}

	class WC_Settings_Tab_Redsys_Order_Export {

    	/**
		* Bootstraps the class and hooks required actions & filters.
		*
     	*/
	 	public static function init() {
        	add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
			add_action( 'woocommerce_settings_tabs_settings_tab_redsys_export_csv', __CLASS__ . '::settings_tab' );
    	}


		/**
		* Add a new settings tab to the WooCommerce settings tabs array.
		*
		* @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
		* @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     	*/
	 	public static function add_settings_tab( $settings_tabs ) {
        	$settings_tabs['settings_tab_redsys_export_csv'] = __( 'Order Export', 'woo-redsys-gateway-light' );
			return $settings_tabs;
    	}


		/**
		* Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
		*
		* @uses woocommerce_admin_fields()
		* @uses self::get_settings()
     	*/
	 	public static function settings_tab() {
        	woocommerce_admin_fields( self::get_settings() );
    	}
		public function get_settings() {

			$settings = array(

				'section_title' => array(
					'name'     => __( 'WooCommerce Order Export', 'woo-redsys-gateway-light' ),
					'type'     => 'title',
					'desc'     => '',
					'id'       => 'wc_settings_tab_orderexport_section_title'
				),
				'redsys_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_orderexport_section_end'
				 					)
			);
			return $settings;
		}
	}
	WC_Settings_Tab_Redsys_Order_Export::init();

	function redsys_text_export_csv(){ ?>
		 <div class="updated woocommerce-message inline">
			<p>
				<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="Consigue la versión Pro en WooCommerce.com" src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/banner.png' ?>" alt="Consigue la versión Pro en WooCommerce.com" width="800" height="150" /></a>
			</p>
		</div>
	<?php
		echo '<p>';
		echo __( 'You can get Order Export with Pro version. Click on the banner above', 'woo-redsys-gateway-light');
		echo '</p>';
	}
	add_action('woocommerce_settings_wc_settings_tab_orderexport_section_end_after', 'redsys_text_export_csv');