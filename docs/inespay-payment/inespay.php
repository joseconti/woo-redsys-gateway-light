<?php


require_once(dirname(__FILE__) . '/lib/InespayApiPublic.php');

class Inespay extends WC_Payment_Gateway
{

    const PREFIX_SUBJECT_TRANSFER = 'INE-';
    
    // Tipos de callback
    const CALLBACK_TYPE_SUCCESS = 'success';
    const CALLBACK_TYPE_NOTIFICATION = 'notification';
    const CALLBACK_TYPE_ABORT = 'abort';

    // Declarar propiedades de la clase para PHP 8.2+
    public $environment;
    public $api_key;
    public $api_token;
    public $mark_as_processing;
    
    /**
     * Logger para debugging
     * @var WC_Logger
     */
    private $logger;

    /**
     * Log a message to WooCommerce logs
     * Los logs se pueden ver en: WooCommerce > Estado > Registros
     *
     * @param string $message El mensaje a registrar
     * @param string $level Nivel de log: 'debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'
     */
    private function log($message, $level = 'info') {
        if (!$this->logger) {
            $this->logger = wc_get_logger();
        }
        
        // El contexto 'source' define el nombre del archivo de log
        $context = array('source' => 'inespay');
        
        // Llamar al método correcto según el nivel
        $this->logger->log($level, $message, $context);
    }

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        global $woocommerce;

        $this->id = 'inespay';
        $this->icon = apply_filters('inespay_icon', plugins_url('/images/inespay.png', __FILE__));

        $this->has_fields = false;

        // Set up localisation
        $this->load_plugin_textdomain();

        $this->method_title = __('Bank Transfer PSD2', 'inespay');
        $this->method_description = '<img src="' . plugins_url('/images/logo-inespay.png', __FILE__) . '" alt="Transferencia Online" style="float:none; margin-bottom:20px;">';
        $this->method_description .= '- ' . __('You will be redirected to your bank to authorize the payment in real time.', 'inespay') . '<br><br>';

        // Load the form fields.
        $this->init_form_fields();

        // Load the settings.
        $this->init_settings();

        // Define user set variables
        $this->title = $this->method_title;
        $this->description = $this->method_description;
        $this->environment = isset($this->settings['INESPAY_ENVIRONMENT']) ? $this->settings['INESPAY_ENVIRONMENT'] : 'TEST';
        $this->mark_as_processing = isset($this->settings['INESPAY_MARK_AS_PROCESSING']) ? $this->settings['INESPAY_MARK_AS_PROCESSING'] : 'no';
        
        // Seleccionar credenciales según el entorno
        if ($this->environment == 'PRO') {
            // Entorno Real - usar credenciales de producción
            $this->api_key = isset($this->settings['INESPAY_API_KEY_PRO']) ? $this->settings['INESPAY_API_KEY_PRO'] : '';
            $this->api_token = isset($this->settings['INESPAY_API_TOKEN_PRO']) ? $this->settings['INESPAY_API_TOKEN_PRO'] : '';
        } else {
            // Entorno Test/Sandbox - usar credenciales de test
            $this->api_key = isset($this->settings['INESPAY_API_KEY_TEST']) ? $this->settings['INESPAY_API_KEY_TEST'] : '';
            $this->api_token = isset($this->settings['INESPAY_API_TOKEN_TEST']) ? $this->settings['INESPAY_API_TOKEN_TEST'] : '';
        }
        
        add_action('plugins_loaded', 'check_woocommerce_version');

        // Actions
        if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.0', '<')) {
            // Check for gateway messages using WC 1.X format
            add_action('woocommerce_update_options_payment_gateways', array(&$this, 'process_admin_options'));
        } else {
            // Payment listener/API hook (WC 2.X)
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        add_action('woocommerce_receipt_inespay', array($this, 'receipt_page'));
        add_action('woocommerce_api_wc_gateway_inespay', array($this, 'call_back_from_inespay'));

        if (!$this->is_valid_for_use()) {
            $this->enabled = false;
        }
    }

    function call_back_from_inespay()
    {
        global $woocommerce;

        $error = true;
        $error_message = '';
        $order = null;

        // Detectar el tipo de callback - intentar de múltiples fuentes
        $callback_type = '';
        if (isset($_GET['callback_type'])) {
            $callback_type = sanitize_text_field($_GET['callback_type']);
        } elseif (isset($_POST['callback_type'])) {
            $callback_type = sanitize_text_field($_POST['callback_type']);
        } elseif (isset($_REQUEST['callback_type'])) {
            $callback_type = sanitize_text_field($_REQUEST['callback_type']);
        }
        
        // Limpiar espacios y convertir a minúsculas para comparación robusta
        $callback_type = trim(strtolower($callback_type));
        
        // Log detallado para debugging
        $this->log('========================================');
        $this->log('INESPAY CALLBACK RECEIVED');
        $this->log('Callback Type: "' . $callback_type . '"');
        $this->log('$_GET: ' . print_r($_GET, true));
        $this->log('$_POST keys: ' . print_r(array_keys($_POST), true));
        $this->log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
        $this->log('========================================');

        if (!empty($_REQUEST)) {
            if (!empty($_POST) && (array_key_exists('dataReturn', $_POST)) && (array_key_exists('signatureDataReturn', $_POST))) {
                @ob_clean();
                $apiInespay = new InespayApiPublic();
                $apiInespay->setApiKeyInespay($this->api_key);
                $dataReturn = sanitize_text_field($_POST['dataReturn']);
                $apiInespay->setDataReturn($dataReturn);

                $signatureDataReturn = sanitize_text_field($_POST['signatureDataReturn']);
                $apiInespay->setSignatureDataReturn($signatureDataReturn);
                
                if ($apiInespay->isDataReturnValid()) {

                    $status = $apiInespay->getStatusFromDataReturn();
                    $reference = $apiInespay->getReferenceFromDataReturn();

                    //Remove prefix to get idCart from reference
                    $idCart = preg_replace('/^' . self::PREFIX_SUBJECT_TRANSFER . '/', '', $reference);
                    $idCart = (int)$idCart;
                    $order = new WC_Order($idCart);
                    
                    $current_status = $order->get_status();
                    $this->log('INESPAY - Order #' . $idCart . ' - Current status: ' . $current_status);

                    if ($status == InespayApiBase::STATUS_CODE_OK) {
                        
                        $error = false;
                        
                        $this->log('INESPAY - Order #' . $idCart . ' - Payment status OK');
                        
                        // GESTIÓN SEGÚN EL TIPO DE CALLBACK
                        if ($callback_type === self::CALLBACK_TYPE_SUCCESS) {
                            // ============================================================
                            // SUCCESS REDIRECT: El usuario vuelve del banco
                            // Solo establecer como PENDIENTE de confirmación
                            // ============================================================
                            
                            $this->log('INESPAY - Entering SUCCESS branch');

                            $current_status = $order->get_status();

                            $already_confirmed = in_array($current_status, array('processing', 'inespay-confirmed', 'completed'));

                            if (!$already_confirmed) {
                                $order->update_status(
                                    'inespay-pending',
                                    __('Payment made at the bank, pending confirmation by Inespay.', 'inespay')
                                );

                                // Reducir stock ahora (reservar productos)
                                wc_reduce_stock_levels($idCart);

                                $this->log('INESPAY - Order #' . $idCart . ' set to PENDING - Waiting for notification');
                            } else {
                                $this->log('INESPAY - Order #' . $idCart . ' already in status "' . $current_status . '", skipping PENDING update');
                            }

                            $order->add_order_note(
                                sprintf(
                                    __('The user has completed the payment at their bank. Reference: %s. Waiting for confirmation from the Inespay server.', 'inespay'),
                                    $reference
                                )
                            );
                            
                            // Redirigir a página de agradecimiento
                            if (wp_redirect($this->get_return_url($order))) {
                                exit;
                            }
                            
                        } elseif ($callback_type === self::CALLBACK_TYPE_NOTIFICATION) {
                            // ============================================================
                            // NOTIFICATION URL: Confirmación del servidor de Inespay
                            // Cambiar a estado CONFIRMADO definitivamente
                            // ============================================================
                            
                            $this->log('INESPAY - Entering NOTIFICATION branch');
                            
                            // Solo confirmar si está en estado pendiente
                            if ($order->get_status() === 'inespay-pending' || $order->get_status() === 'pending') {
                                
                                $order->add_order_note(
                                    sprintf(
                                        __('Payment definitively confirmed by Inespay. Reference: %s', 'inespay'),
                                        $reference
                                    )
                                );

                                $settings = get_option('woocommerce_inespay_settings', array());
                                $mark_as_processing = isset($settings['INESPAY_MARK_AS_PROCESSING']) ? $settings['INESPAY_MARK_AS_PROCESSING'] : 'no';
                                $this->log('INESPAY - mark_as_processing value: "' . $mark_as_processing . '"');

                                if ($mark_as_processing === 'yes') {
                                    // payment_complete() marca el pedido como "processing" nativamente
                                    $order->payment_complete();
                                    $this->log('INESPAY - Order #' . $idCart . ' set to PROCESSING via payment_complete()');
                                } else {
                                    // Estado personalizado: "Transferencia Online: pago confirmado"
                                    $order->update_status(
                                        'inespay-confirmed',
                                        __('Payment confirmed by the Inespay server.', 'inespay')
                                    );
                                    $this->log('INESPAY - Order #' . $idCart . ' set to inespay-confirmed (mark_as_processing disabled)');
                                }
                                
                                $this->log('INESPAY - Order #' . $idCart . ' CONFIRMED successfully');
                                
                            } else {
                                // Ya estaba confirmado o en otro estado
                                $order->add_order_note(
                                    sprintf(
                                        __('Inespay notification received but the order is already in status: %s', 'inespay'),
                                        $order->get_status()
                                    )
                                );
                                
                                $this->log('INESPAY - Order #' . $idCart . ' already in status: ' . $order->get_status());
                            }
                            
                            // Responder con HTTP 200 al servidor de Inespay
                            status_header(200);
                            echo 'OK';
                            exit;
                            
                        } elseif ($callback_type === self::CALLBACK_TYPE_ABORT) {
                            // ============================================================
                            // ABORT: Usuario canceló el pago
                            // ============================================================
                            
                            $this->log('INESPAY - Entering ABORT branch');
                            
                            $order->update_status(
                                'failed',
                                __('Payment cancelled by the user at the bank.', 'inespay')
                            );
                            
                            $order->add_order_note(
                                sprintf(
                                    __('User cancelled the payment at the bank. Reference: %s', 'inespay'),
                                    $reference
                                )
                            );
                            
                            $this->log('INESPAY - Order #' . $idCart . ' marked as FAILED (user aborted)');
                            
                            // Redirigir a página de cancelación
                            if (wp_redirect($order->get_cancel_order_url())) {
                                exit;
                            }
                            
                        } else {
                            // ============================================================
                            // CALLBACK SIN TIPO RECONOCIDO O VACÍO
                            // NO HACER NADA - dejar el pedido en su estado actual
                            // ============================================================
                            
                            $this->log('INESPAY WARNING - Order #' . $idCart . ' - Unknown or empty callback type: "' . $callback_type . '"', 'warning');
                            $this->log('INESPAY - Order remains in current status: ' . $order->get_status(), 'warning');
                            
                            // Agregar nota pero NO cambiar estado
                            $order->add_order_note(
                                sprintf(
                                    __('WARNING: Callback received with unrecognized type (type: "%s"). Order remains in status: %s. Reference: %s. Check URL configuration.', 'inespay'),
                                    $callback_type,
                                    $order->get_status(),
                                    $reference
                                )
                            );
                            
                            // Intentar responder apropiadamente según el contexto
                            // Si parece un redirect del navegador, redirigir
                            if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'inespay') !== false) {
                                $this->log('INESPAY - Looks like a user redirect, redirecting to return URL');
                                if (wp_redirect($this->get_return_url($order))) {
                                    exit;
                                }
                            } else {
                                // Si parece una notificación del servidor, responder con OK
                                $this->log('INESPAY - Looks like a server notification, responding with HTTP 200');
                                status_header(200);
                                echo 'OK';
                                exit;
                            }
                        }

                    } else {
                        $error_message = __('No completed transaction', 'inespay');
                        $this->log('INESPAY ERROR - Status not OK: ' . $status, 'error');
                    }
                } else {
                    $error_message = __('Signature no matching', 'inespay');
                    $this->log('INESPAY ERROR - Invalid signature', 'error');
                }
            } else {
                $error_message = __('Invalid params', 'inespay');
                $this->log('INESPAY ERROR - Missing dataReturn or signatureDataReturn', 'error');
            }

        } else {
            $error_message = __('Invalid paramteres', 'inespay');
            $this->log('INESPAY ERROR - Empty REQUEST', 'error');
        }

        if ($error) {
            wc_add_notice(__('Payment error:', 'inespay') . $error_message, 'error');

            if ($order != null) {
                // Marcar el pedido como fallido
                $order->update_status('failed', $error_message);
                $this->log('INESPAY - Order #' . $order->get_id() . ' marked as FAILED: ' . $error_message, 'error');
                
                if (wp_redirect($order->get_cancel_order_url())) {
                    exit;
                }
            }
        }
    }

    /**
     * Localisation.
     *
     * @access public
     * @return void
     */
    function load_plugin_textdomain()
    {
        load_plugin_textdomain('inespay', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * Check if this gateway is enabled and available in the user's country
     *
     * @access public
     * @return bool
     */
    function is_valid_for_use()
    {
        $available_currencies = array('EUR');

        return in_array(get_woocommerce_currency(), apply_filters('woocommerce_inespay_supported_currencies', $available_currencies));
    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     *
     * @since 1.0.0
     */
    public function admin_options()
    {
        ?>

        <h3><?php _e('Inespay', 'inespay'); ?></h3>
        <p>
            <strong><?php _e('The new way to accept payments by bank transfer. Authorized and supervised by Bank of Spain.', 'inespay'); ?></strong>
        </p>
        <p><?php _e('Redirect your customer to their bank to authorize a transfer in real time.', 'inespay'); ?></p>
        <p><?php _e('Instant notification to confirm payment to your customer and release orders without delay.', 'inespay'); ?></p>
        <p><?php _e('Sign up and get your credentials at <a href="https://www.transferenciabancariapsd2.com/" target="_blank"> www.transferenciabancariapsd2.com </a>', 'inespay'); ?> <a href="https://clients.inespay.com/build/signup" target="_blank"> https://clients.inespay.com/build/signup</a></p>

        <?php if ($this->is_valid_for_use()) : ?>
        <table class="form-table">
            <?php
            // Generate the HTML For the settings form.
            $this->generate_settings_html();
            ?>
        </table>
        <!--/.form-table-->
    <?php else : ?>
        <div class="inline error">
            <p>
                <strong><?php _e('Gateway Disabled', 'inespay'); ?></strong>: <?php _e('inespay does not support your store currency.', 'inespay'); ?>
            </p>
        </div>
    <?php
    endif;
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields()
    {

        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'inespay'),
                'type' => 'checkbox',
                'description' => __('Enable/Disable payment method.', 'inespay'),
                'desc_tip' => true,
                'label' => __('Enable INESPAY', 'inespay'),
                'default' => 'yes'
            ),
            'INESPAY_ENVIRONMENT' => array(
                'title' => __('Environment', 'inespay'),
                'type' => 'select',
                'label' => __('Mode', 'inespay'),
                'options' => array(
                    'PRO' => __('Real', 'inespay'),
                    'TEST' => __('Test/Sandbox', 'inespay'),

                ),
                'description' => __('To use this payment method in your production online store, you must use the Real Environment credentials.', 'inespay'),
                'desc_tip' => true,
                'default' => 'TEST'
            ),
            'INESPAY_MARK_AS_PROCESSING' => array(
                'title'       => __('Mark order as Processing', 'inespay'),
                'type'        => 'checkbox',
                'label'       => __('Activate', 'inespay'),
                'description' => __('Enable this option to automatically set orders to Processing status when a payment is successfully confirmed by Inespay. If disabled, orders will be marked with the custom status "Transferencia Online: pago confirmado" and you will need to change the status manually after verifying receipt of funds in your bank.', 'inespay'),
                'default'     => 'no'
            ),
            
            // Sección de credenciales de Test/Sandbox
            'test_credentials_title' => array(
                'title' => __('Test/Sandbox Credentials', 'inespay'),
                'type' => 'title',
                'description' => __('You can fill in both groups at the same time: the plugin will automatically use the credentials of the environment selected above.', 'inespay'),
            ),
            'INESPAY_API_KEY_TEST' => array(
                'title' => __('API Key (Test/Sandbox)', 'inespay'),
                'type' => 'text',
                'description' => __('Paste here the Test API Key provided by INESPAY', 'inespay'),
                'desc_tip' => true,
                'default' => __('', 'inespay')
            ),
            'INESPAY_API_TOKEN_TEST' => array(
                'title' => __('API Token (Test/Sandbox)', 'inespay'),
                'type' => 'textarea',
                'description' => __('Paste here the Test API Token provided by INESPAY', 'inespay'),
                'desc_tip' => true,
                'default' => __('', 'inespay')
            ),
            
            // Sección de credenciales de Producción
            'prod_credentials_title' => array(
                'title' => __('Production Credentials', 'inespay'),
                'type' => 'title',
                'description' => __('These fields are REQUIRED when the selected environment is Real. You do not need to delete the Test credentials to use Production, or vice versa.', 'inespay'),
            ),
            'INESPAY_API_KEY_PRO' => array(
                'title' => __('API Key (Production)', 'inespay'),
                'type' => 'text',
                'description' => __('Paste here the Production API Key provided by INESPAY', 'inespay'),
                'desc_tip' => true,
                'default' => __('', 'inespay')
            ),
            'INESPAY_API_TOKEN_PRO' => array(
                'title' => __('API Token (Production)', 'inespay'),
                'type' => 'textarea',
                'description' => __('Paste here the Production API Token provided by INESPAY', 'inespay'),
                'desc_tip' => true,
                'default' => __('', 'inespay')
            )
        );
    }

    /**
     * Validate settings before saving
     * Ensures production credentials are provided when Real environment is selected
     *
     * @access public
     * @return bool
     */
    public function process_admin_options()
    {
        $post_data = $this->get_post_data();
        
        // Validar que si el entorno es Real, las credenciales de producción estén rellenas
        if (isset($post_data['woocommerce_inespay_INESPAY_ENVIRONMENT']) && 
            $post_data['woocommerce_inespay_INESPAY_ENVIRONMENT'] === 'PRO') {
            
            $prod_api_key = isset($post_data['woocommerce_inespay_INESPAY_API_KEY_PRO']) 
                ? trim($post_data['woocommerce_inespay_INESPAY_API_KEY_PRO']) 
                : '';
            $prod_api_token = isset($post_data['woocommerce_inespay_INESPAY_API_TOKEN_PRO']) 
                ? trim($post_data['woocommerce_inespay_INESPAY_API_TOKEN_PRO']) 
                : '';
            
            if (empty($prod_api_key) || empty($prod_api_token)) {
                // Mostrar error y prevenir que se guarde la configuración
                WC_Admin_Settings::add_error(
                    __('Error: When using Real (Production) environment, you must provide both Production API Key and Production API Token.', 'inespay')
                );
                return false;
            }
        }
        
        // Si la validación pasa, proceder con el guardado normal
        return parent::process_admin_options();
    }

    /**
     * Generate the inespay URL
     *
     * @access public
     * @param mixed $order_id
     * @return string
     */
    function generate_inespay_url($order_id)
    {
        global $woocommerce;

        $order = new WC_Order($order_id);

        $environmentApiInespay = InespayApiPublic::ENV_PRO; //Production
        if ($this->environment == 'TEST') {
            $environmentApiInespay = InespayApiPublic::ENV_SAN; //Sandbox
        }
        $apiInespay = new InespayApiPublic();
        $apiInespay->setEnvironmentInespay($environmentApiInespay);
        $apiInespay->setApiKeyInespay($this->api_key);
        $apiInespay->setTokenInespay($this->api_token);

        if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.7', '<')) {
            $internalReference = $order->id; // Usa $order->id si la versión es menor a 2.7
        } else {
            $internalReference = $order->get_id(); // Usa $order->get_id() si la versión es 2.7 o mayor
        }
        $reference = self::PREFIX_SUBJECT_TRANSFER . $internalReference;
        $subject = $reference;

        $totalAmount = $order->get_total();
        
        // Crear URLs diferentes para distinguir entre success redirect y notification
        // IMPORTANTE: No usar esc_url() con segundo parámetro porque escapa & como &#038;
        $urlSuccess = add_query_arg([
            'wc-api' => 'wc_gateway_inespay',
            'callback_type' => self::CALLBACK_TYPE_SUCCESS
        ], home_url('/'));
        
        $urlNotification = add_query_arg([
            'wc-api' => 'wc_gateway_inespay',
            'callback_type' => self::CALLBACK_TYPE_NOTIFICATION
        ], home_url('/'));
        
        $urlAbort = add_query_arg([
            'wc-api' => 'wc_gateway_inespay',
            'callback_type' => self::CALLBACK_TYPE_ABORT
        ], home_url('/'));

        $singleInitRequest = new SingleInitRequest();
        $singleInitRequest->setAmount($totalAmount); //Importe 2 decimales separados por .
        $singleInitRequest->setDescription($subject); //Concepto del pago
        $singleInitRequest->setReference($internalReference); //Identificador interno del pago

        // Success: cuando el usuario vuelve después de pagar
        $singleInitRequest->setSuccessLinkRedirect($urlSuccess);
        $singleInitRequest->setSuccessLinkRedirectMethod('POST');
        
        // Notification: webhook del servidor de Inespay
        $singleInitRequest->setNotifUrl($urlNotification);

        // Abort: cuando el usuario cancela
        $singleInitRequest->setAbortLinkRedirect($urlAbort);
        $singleInitRequest->setAbortLinkRedirectMethod('POST');
        $singleInitRequest->setCustomData($this->getCustomData());

        $response = $apiInespay->generateSimplePaymentUrl($singleInitRequest); //Llamada síncrona Api Inespay

        //Success url INESPAY
        if ($response->getStatus() == InespayApiBase::STATUS_CODE_SUCCESS) {

            $urlInespay = $response->getSinglePayinLink();
            
            // Verificar que la URL se generó correctamente
            if (empty($urlInespay)) {
                // SIEMPRE loguear el error completo
                $this->log('INESPAY ERROR - Payment URL is empty', 'error');
                
                // Error genérico (no hay detalles técnicos que mostrar en este caso)
                return '<div class="woocommerce-error" style="margin: 20px 0; padding: 15px; background: #e2401c; color: white; border-radius: 3px;">' . 
                       '<strong>' . __('Payment system connection error.', 'inespay') . '</strong><br><br>' .
                       __('The payment could not be processed at this time. Please try again or contact support if the problem persists.', 'inespay') . 
                       '</div>' .
                       '<p><a href="' . esc_url(wc_get_checkout_url()) . '" class="button">' . __('Return to checkout', 'inespay') . '</a></p>';
            }
            
            // Sanitizar la URL pero sin escapar el ampersand
            $urlInespay = esc_url_raw($urlInespay);
            
            return '<button class="button" onclick="window.location.href=\'' . esc_js($urlInespay) . '\'">' . __('Make payment', 'inespay') . '</button>';

        } else {
            // Error en la respuesta de Inespay
            $errorStatus = $response->getStatus();
            $errorMessage = $response->getStatusDesc();
            
            // SIEMPRE loguear el error completo (para debugging)
            $this->log('INESPAY ERROR - Failed to generate payment URL. Status: ' . $errorStatus . ', Message: ' . $errorMessage, 'error');
            
            // Decidir qué mensaje mostrar al usuario
            if (defined('WP_DEBUG') && WP_DEBUG) {
                // Modo DEBUG: mostrar error detallado
                $displayMessage = '<strong>' . __('Error al conectar con Inespay:', 'inespay') . '</strong><br>' . 
                                 esc_html($errorMessage) . '<br>' .
                                 '<small style="opacity: 0.9;">' . __('Code:', 'inespay') . ' ' . esc_html($errorStatus) . '</small><br><br>' .
                                 __('Please try again or contact support.', 'inespay');
            } else {
                // Modo PRODUCCIÓN: mostrar error genérico
                $displayMessage = '<strong>' . __('Payment system connection error.', 'inespay') . '</strong><br><br>' .
                                 __('The payment could not be processed at this time. Please try again or contact support if the problem persists.', 'inespay');
            }
            
            // Retornar HTML de error visible directamente en la página
            return '<div class="woocommerce-error" style="margin: 20px 0; padding: 15px; background: #e2401c; color: white; border-radius: 3px;">' . 
                   $displayMessage . 
                   '</div>' .
                   '<p><a href="' . esc_url(wc_get_checkout_url()) . '" class="button">' . __('Return to checkout', 'inespay') . '</a></p>';
        }
    }

    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int $order_id
     * @return array
     */
    function process_payment($order_id)
    {

        $order = new WC_Order($order_id);

        if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.1', '<')) {
            $redirect_url = add_query_arg('order', $order->get_id(), add_query_arg('key', $order->get_order_key(), get_permalink(woocommerce_get_page_id('pay'))));
        } else {
            $redirect_url = $order->get_checkout_payment_url(true);
        }

        return array(
            'result' => 'success',
            'redirect' => $redirect_url
        );
    }

    /**
     * Output for the order received page.
     *
     * @access public
     * @return void
     */
    function receipt_page($order)
    {
        static $has_run = false; // Variable estática para controlar la ejecución

        if ($has_run) {
            return; // Si ya ha sido ejecutada, salir de la función
        }

        $has_run = true; // Marcar como ejecutada

        $receiptText = '<img src="' . plugins_url('/images/logo-inespay.png', __FILE__) . '" style=""> <br>';
        $receiptText .= '- ' . __('You will be redirected to your bank to authorize the payment in real time.', 'inespay') . '<br>';
        echo $receiptText;
        echo $this->generate_inespay_url($order);
    }

    /**
     * Get gateway icon.
     * @return string
     */
    public function get_icon()
    {
        $icon_html = '';

        return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
    }

    public function get_return_url($order = null)
    {
        if ($order) {
            $return_url = $order->get_checkout_order_received_url();
        } else {
            $return_url = wc_get_endpoint_url('order-received', '', wc_get_page_permalink('checkout'));
        }

        if (is_ssl() || get_option('woocommerce_force_ssl_checkout') == 'yes') {
            $return_url = str_replace('http:', 'https:', $return_url);
        }

        return apply_filters('woocommerce_get_return_url', $return_url, $order);
    }

    private function getCustomData()
    {
        return wp_json_encode([
            'platform' => [
                'name' => 'woocommerce',
                'version' => WC()->version,
                'wordpress_version' => get_bloginfo('version')
            ],
            'plugin' => [
                'name' => $this->id,
                'version' => PLUGIN_VERSION
            ],
            'shop' => [
                'url' => get_site_url()
            ],
        ], JSON_UNESCAPED_SLASHES);
    }
}