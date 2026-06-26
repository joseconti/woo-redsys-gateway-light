<?php
/**
 * Plugin Name: INESPAY payment
 * Plugin URI: https://es.inespay.com/transferenciaonline/empresas
 * Description: This plugin allows to accept transfer payments through the Online banking.
 * Version: 1.6.10
 * Author: INESPAY
 * Author URI: https://www.inespay.com
 * License: GPLv2 or later
 *
 * Text Domain: inespay
 * Domain Path: /languages/
 * Requires at least: 4.6
 * Tested up to: 6.9
 * Requires PHP: 8.0
 * WC tested up to: 10.4.3
 */

// Your plugin code goes here
define('PLUGIN_VERSION', '1.6.10');

add_action('plugins_loaded', 'woocommerce_myplugin', 0);
function woocommerce_myplugin()
{
    if (!class_exists('WC_Payment_Gateway'))
        return; // if the WC payment gateway class

    include(plugin_dir_path(__FILE__) . 'inespay.php');
}

add_action('plugins_loaded', 'inespay_run_migrations', 1);

add_action('admin_notices', 'inespay_migration_admin_notice');
function inespay_migration_admin_notice()
{
    if (get_option('inespay_migration_notice') === '1.6.0') {
        $settings_url = admin_url('admin.php?page=wc-settings&tab=checkout&section=inespay');
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo '<strong>INESPAY:</strong> ';
        printf(
            __('Tus credenciales anteriores se han migrado automáticamente a los nuevos campos de entorno. Por favor, <a href="%s">revisa la configuración del gateway</a> para confirmar que todo es correcto.', 'inespay'),
            esc_url($settings_url)
        );
        echo '</p></div>';
        delete_option('inespay_migration_notice');
    }
}

function inespay_run_migrations()
{
    $installed_version = get_option('inespay_version');

    if ($installed_version === PLUGIN_VERSION) {
        return;
    }

    // Migración 1.6.0: separar credenciales por entorno
    if ($installed_version === false || version_compare($installed_version, '1.6.0', '<')) {
        inespay_migrate_credentials();
    }

    update_option('inespay_version', PLUGIN_VERSION);
}

function inespay_migrate_credentials()
{
    $settings = get_option('woocommerce_inespay_settings', array());

    $old_api_key     = isset($settings['INESPAY_API_KEY'])     ? $settings['INESPAY_API_KEY']     : '';
    $old_api_token   = isset($settings['INESPAY_API_TOKEN'])   ? $settings['INESPAY_API_TOKEN']   : '';
    $old_environment = isset($settings['INESPAY_ENVIRONMENT']) ? $settings['INESPAY_ENVIRONMENT'] : '';

    if (empty($old_api_key) && empty($old_api_token) && empty($old_environment)) {
        return;
    }

    $env_map = array(
        'T' => 'TEST',
        'R' => 'PRO',
    );

    $target_env = null;
    $updated    = false;

    if (isset($env_map[$old_environment])) {
        $target_env                      = $env_map[$old_environment];
        $settings['INESPAY_ENVIRONMENT'] = $target_env;
        $updated                         = true;
    }

    if ($target_env === 'TEST') {
        if (!empty($old_api_key) && empty($settings['INESPAY_API_KEY_TEST'])) {
            $settings['INESPAY_API_KEY_TEST'] = $old_api_key;
            $updated = true;
        }
        if (!empty($old_api_token) && empty($settings['INESPAY_API_TOKEN_TEST'])) {
            $settings['INESPAY_API_TOKEN_TEST'] = $old_api_token;
            $updated = true;
        }
    } elseif ($target_env === 'PRO') {
        if (!empty($old_api_key) && empty($settings['INESPAY_API_KEY_PRO'])) {
            $settings['INESPAY_API_KEY_PRO'] = $old_api_key;
            $updated = true;
        }
        if (!empty($old_api_token) && empty($settings['INESPAY_API_TOKEN_PRO'])) {
            $settings['INESPAY_API_TOKEN_PRO'] = $old_api_token;
            $updated = true;
        }
    }

    if ($updated) {
        update_option('woocommerce_inespay_settings', $settings);
        update_option('inespay_migration_notice', '1.6.0');
    }
}


add_filter('woocommerce_payment_gateways', 'add_my_custom_gateway');

function add_my_custom_gateway($gateways)
{
    $gateways[] = 'inespay';
    return $gateways;
}

/**
 * Custom function to declare compatibility with cart_checkout_blocks feature
 */
function declare_cart_checkout_blocks_compatibility()
{
    // Check if the required class exists
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        // Declare compatibility for 'cart_checkout_blocks'
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
    }
}

// Hook the custom function to the 'before_woocommerce_init' action
add_action('before_woocommerce_init', 'declare_cart_checkout_blocks_compatibility');

// Hook the custom function to the 'woocommerce_blocks_loaded' action
add_action('woocommerce_blocks_loaded', 'oawoo_register_order_approval_payment_method_type');

/**
 * Custom function to register a payment method type
 */
function oawoo_register_order_approval_payment_method_type()
{
    // Check if the required class exists
    if (!class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
        return;
    }

    // Include the custom Blocks Checkout class
    require_once plugin_dir_path(__FILE__) . 'inespay-block.php';

    // Hook the registration function to the 'woocommerce_blocks_payment_method_type_registration' action
    add_action(
        'woocommerce_blocks_payment_method_type_registration',
        function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
            // Register an instance of My_Custom_Gateway_Blocks
            $payment_method_registry->register(new Inespay_block);
        }
    );
}

/**
 * ============================================================================
 * REGISTRO DE ESTADOS PERSONALIZADOS DE PEDIDO PARA INESPAY
 * ============================================================================
 */

/**
 * Registrar estados personalizados de pedido
 */
function inespay_register_custom_order_statuses()
{
    register_post_status('wc-inespay-pending', array(
        'label' => __('Transferencia Online: pendiente confirmación', 'inespay'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop(
            'Transferencia Online: pendiente confirmación <span class="count">(%s)</span>',
            'Transferencia Online: pendiente confirmación <span class="count">(%s)</span>',
            'inespay'
        ),
    ));

    register_post_status('wc-inespay-confirmed', array(
        'label' => __('Transferencia Online: pago confirmado', 'inespay'),
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop(
            'Transferencia Online: pago confirmado <span class="count">(%s)</span>',
            'Transferencia Online: pago confirmado <span class="count">(%s)</span>',
            'inespay'
        ),
    ));
}
add_action('init', 'inespay_register_custom_order_statuses');

/**
 * Agregar estados personalizados al dropdown de estados de WooCommerce
 */
function inespay_add_custom_order_statuses($order_statuses)
{
    $new_order_statuses = array();

    // Insertar los estados personalizados después de "processing"
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;

        if ('wc-processing' === $key) {
            $new_order_statuses['wc-inespay-pending'] = __('Transferencia Online: pendiente confirmación', 'inespay');
            $new_order_statuses['wc-inespay-confirmed'] = __('Transferencia Online: pago confirmado', 'inespay');
        }
    }

    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'inespay_add_custom_order_statuses');

/**
 * Agregar íconos/colores personalizados para los estados (opcional pero recomendado)
 */
function inespay_custom_order_status_styles()
{
    ?>
    <style>
        .order-status.status-inespay-pending {
            background: #0F2C58;
            color: #ffffff;
        }

        .order-status.status-inespay-confirmed {
            background: #3567FE;
            color: #ffffff;
        }

        mark.inespay-pending {
            background: #0F2C58;
            color: #ffffff;
            font-weight: bold;
        }

        mark.inespay-confirmed {
            background: #3567FE;
            color: #ffffff;
            font-weight: bold;
        }
    </style>
    <?php
}
add_action('admin_head', 'inespay_custom_order_status_styles');

/**
 * Configurar emails para los estados personalizados (opcional)
 * Esto permite que WooCommerce envíe emails cuando el pedido cambie a estos estados
 */
function inespay_register_custom_order_status_emails($email_classes)
{
    // Puedes agregar clases de email personalizadas aquí si lo deseas
    return $email_classes;
}
add_filter('woocommerce_email_classes', 'inespay_register_custom_order_status_emails');

/**
 * Hacer que los pedidos con estado "inespay-confirmed" se consideren pagados
 * Esto es importante para que WooCommerce los trate correctamente
 */
function inespay_custom_status_is_paid($statuses)
{
    $statuses[] = 'inespay-confirmed';
    return $statuses;
}
add_filter('woocommerce_order_is_paid_statuses', 'inespay_custom_status_is_paid');

/**
 * Permitir que payment_complete() pueda avanzar el pedido desde "inespay-pending"
 * Por defecto WooCommerce solo permite la transición desde: pending, failed, on-hold
 */
function inespay_valid_statuses_for_payment_complete($statuses)
{
    $statuses[] = 'inespay-pending';
    return $statuses;
}
add_filter('woocommerce_valid_order_statuses_for_payment_complete', 'inespay_valid_statuses_for_payment_complete');

?>