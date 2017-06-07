<?php
	if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	class WC_Settings_Tab_Redsys_Sort_Invoices {

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_tab_redsys_invoices', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_tab_redsys_invoices', __CLASS__ . '::update_settings' );
    }


    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_redsys_invoices'] = __( 'Sequential Invoice Numbers', 'woo-redsys-gateway-light' );
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


    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {

        $settings = array(
             'title' => array(
                'name'     => __( 'Sequential Invoice Numbers', 'woo-redsys-gateway-light' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_settings_tab_redsys_sort_invoices_title'
            ),
            'redsys_section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_redsys_sort_invoices_section_end'
				 					)
        );

        return apply_filters( 'wc_settings_tab_redsys_sort_invoices_settings', $settings );
    }

}

	WC_Settings_Tab_Redsys_Sort_Invoices::init();


	function redsys_text_sort_invoice(){ ?>
		 <div class="updated woocommerce-message inline">
			<p>
				<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="Consigue la versión Pro en WooCommerce.com" src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/banner.png' ?>" alt="Consigue la versión Pro en WooCommerce.com" width="800" height="150" /></a>
			</p>
		</div>
	<?php
		echo '<p>';
		echo __( 'You can get Sequential Invoice Numbers with Pro version. Click on the banner above', 'woo-redsys-gateway-light');
		echo '</p>';
	}
	add_action('woocommerce_settings_wc_settings_tab_redsys_sort_invoices_section_end_after', 'redsys_text_sort_invoice');

?>
