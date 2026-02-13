<?php
/**
 * Class WC_Gateway_Inespay_Redsys
 *
 * @package WooCommerce Redsys Gateway Light (by Jose Conti).
 * @link https://plugins.joseconti.com
 * @since 7.0.0
 * @author José Conti.
 * @license GNU General Public License v3.0
 * @license URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @copyright 2013-2026 José Conti.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Gateway_Inespay_Redsys' ) ) :

	/**
	 * Inespay payment gateway (simple payment only).
	 */
	class WC_Gateway_Inespay_Redsys extends WC_Payment_Gateway {

		/**
		 * Gateway ID.
		 *
		 * @var string
		 */
		public $id = 'inespayredsys';

		/**
		 * Whether the gateway has fields.
		 *
		 * @var bool
		 */
		public $has_fields = false;

		/**
		 * API key.
		 *
		 * @var string
		 */
		protected $api_key;

		/**
		 * API token.
		 *
		 * @var string
		 */
		protected $api_token;

		/**
		 * Optional creditor account (IBAN).
		 *
		 * @var string
		 */
		protected $creditor_account;

		/**
		 * Optional expiration in minutes.
		 *
		 * @var string
		 */
		protected $expiration;

		/**
		 * Debug flag.
		 *
		 * @var string
		 */
		protected $debug;

		/**
		 * Test mode flag.
		 *
		 * @var string
		 */
		protected $testmode;

		/**
		 * Current environment (san|pro).
		 *
		 * @var string
		 */
		protected $environment;

		/**
		 * Notification URL for callbacks.
		 *
		 * @var string
		 */
		public $notify_url;

		/**
		 * Logger instance.
		 *
		 * @var WC_Logger
		 */
		public $log;

		/**
		 * What to do after payment.
		 *
		 * @var string
		 */
		public $orderdo;

		/**
		 * Transaction limit.
		 *
		 * @var string
		 */
		public $transactionlimit;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->method_title       = __( 'Inespay Lite (by José Conti)', 'woo-redsys-gateway-light' );
			$this->method_description = __( 'Instant bank transfer via Inespay.', 'woo-redsys-gateway-light' );

			$logo_url = $this->get_option( 'logo' );
			if ( ! empty( $logo_url ) ) {
				$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', $logo_url );
			} else {
				$this->icon = apply_filters( 'woocommerce_' . $this->id . '_icon', REDSYS_PLUGIN_URL . 'assets/images/inespay.svg' );
			}

			$this->supports = array(
				'products',
				'refunds',
			);

			$this->notify_url = add_query_arg( 'wc-api', 'wc_gateway_' . $this->id, home_url( '/' ) );

			$this->init_form_fields();
			$this->init_settings();

			$this->title            = $this->get_option( 'title' );
			$this->description      = $this->get_option( 'description' );
			$this->debug            = $this->get_option( 'debug' );
			$this->testmode         = $this->get_option( 'testmode' );
			$this->api_key          = $this->get_option( 'api_key' );
			$this->api_token        = $this->get_option( 'api_token' );
			$this->environment      = ( 'yes' === $this->testmode ) ? 'san' : 'pro';
			$this->creditor_account = $this->get_option( 'creditor_account' );
			$this->expiration       = $this->get_option( 'expiration' );
			$this->orderdo          = $this->get_option( 'orderdo' );
			$this->transactionlimit = $this->get_option( 'transactionlimit' );
			$this->log              = new WC_Logger();

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_api_wc_gateway_' . $this->id, array( $this, 'handle_callback' ) );
			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'disable_inespay' ) );
		}

		/**
		 * Check if the customer billing/shipping country is allowed.
		 *
		 * @return bool
		 */
		protected function is_allowed_country() {
			$allowed  = array( 'ES', 'PT', 'IT' );
			$customer = WC()->customer;
			$billing  = $customer ? $customer->get_billing_country() : '';
			$shipping = $customer ? $customer->get_shipping_country() : '';
			$country  = $shipping ? $shipping : $billing;

			if ( empty( $country ) ) {
				$base    = wc_get_base_location();
				$country = isset( $base['country'] ) ? $base['country'] : '';
			}

			return in_array( strtoupper( $country ), $allowed, true );
		}

		/**
		 * Only available in allowed countries.
		 *
		 * @return bool
		 */
		public function is_available() {
			if ( 'yes' !== $this->enabled ) {
				return false;
			}

			if ( ! $this->is_allowed_country() ) {
				return false;
			}

			return true;
		}

		/**
		 * Disable gateway based on transaction limit.
		 *
		 * @param array $available_gateways Available gateways.
		 * @return array
		 */
		public function disable_inespay( $available_gateways ) {
			if ( ! is_admin() && is_checkout() ) {
				$total = (int) WC()->cart->total;
				$limit = (int) $this->transactionlimit;
				if ( ! empty( $limit ) && $limit > 0 ) {
					if ( $total > $limit ) {
						unset( $available_gateways['inespayredsys'] );
					}
				}
			}
			return $available_gateways;
		}

		/**
		 * Admin options panel.
		 */
		public function admin_options() {
			?>
			<h3><?php esc_html_e( 'Inespay Bank Transfer', 'woo-redsys-gateway-light' ); ?></h3>
			<p><?php esc_html_e( 'Inespay redirects customers to authorize an instant bank transfer.', 'woo-redsys-gateway-light' ); ?></p>
			<div class="updated woocommerce-message inline">
				<p>
					<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="Consigue la versión Pro en WooCommerce.com" src="<?php echo esc_url( REDSYS_PLUGIN_URL ) . 'assets/images/banner.png'; ?>" alt="Consigue la versión Pro en WooCommerce.com" width="800" height="150" /></a>
				</p>
			</div>
			<div class="redsysnotice">
				<span class="dashicons dashicons-welcome-learn-more redsysnotice-dash"></span>
				<span class="redsysnotice__content">
				<?php
				$allowed_html = array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				);

				$faq_url     = esc_url( 'https://plugins.joseconti.com/redsys-for-woocommerce/' );
				$support_url = esc_url( 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/' );
				$review_url  = esc_url( 'https://wordpress.org/support/plugin/woo-redsys-gateway-light/reviews/?rate=5#new-post' );

				$raw_html = sprintf(
					// Translators: %1$s is the FAQ page URL, %2$s is the support thread URL, %3$s is the review URL.
					__( 'Check <a href="%1$s" target="_blank" rel="noopener">FAQ page</a> for working problems, or open a <a href="%2$s" target="_blank" rel="noopener">thread on WordPress.org</a> for support. Please, add a <a href="%3$s" target="_blank" rel="noopener">review on WordPress.org</a>', 'woo-redsys-gateway-light' ),
					$faq_url,
					$support_url,
					$review_url
				);
				echo wp_kses( $raw_html, $allowed_html );
				?>
				</span>
			</div>
			<table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
			<?php
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'          => array(
					'title'   => __( 'Enable/Disable', 'woo-redsys-gateway-light' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Inespay Bank Transfer', 'woo-redsys-gateway-light' ),
					'default' => 'no',
				),
				'title'            => array(
					'title'       => __( 'Title', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Title shown at checkout.', 'woo-redsys-gateway-light' ),
					'default'     => __( 'Inespay Bank Transfer', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'description'      => array(
					'title'       => __( 'Description', 'woo-redsys-gateway-light' ),
					'type'        => 'textarea',
					'description' => __( 'Description shown at checkout.', 'woo-redsys-gateway-light' ),
					'default'     => __( 'Pay securely via your online banking with Inespay.', 'woo-redsys-gateway-light' ),
				),
				'logo'             => array(
					'title'       => __( 'Logo', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Add link to image logo for Gateway at checkout.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'api_key'          => array(
					'title'       => __( 'API Key', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'API Key from your Inespay project.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'api_token'        => array(
					'title'       => __( 'API Token', 'woo-redsys-gateway-light' ),
					'type'        => 'password',
					'description' => __( 'API Token (Bearer) from your Inespay project.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'creditor_account' => array(
					'title'       => __( 'Creditor IBAN (optional)', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Set a specific creditor IBAN if you manage multiple accounts in Inespay.', 'woo-redsys-gateway-light' ),
				),
				'expiration'       => array(
					'title'             => __( 'Link expiration (minutes)', 'woo-redsys-gateway-light' ),
					'type'              => 'number',
					'description'       => __( 'Optional timeout for the payment link. Leave empty for default.', 'woo-redsys-gateway-light' ),
					'custom_attributes' => array(
						'min' => 1,
					),
				),
				'orderdo'          => array(
					'title'       => __( 'What to do after payment?', 'woo-redsys-gateway-light' ),
					'type'        => 'select',
					'description' => __( 'Chose what to do after the customer pay the order.', 'woo-redsys-gateway-light' ),
					'default'     => 'processing',
					'options'     => array(
						'processing' => __( 'Mark as Processing (default & recomended)', 'woo-redsys-gateway-light' ),
						'completed'  => __( 'Mark as Complete', 'woo-redsys-gateway-light' ),
					),
				),
				'transactionlimit' => array(
					'title'       => __( 'Transaction Limit', 'woo-redsys-gateway-light' ),
					'type'        => 'text',
					'description' => __( 'Maximum transaction price for the cart.', 'woo-redsys-gateway-light' ),
					'desc_tip'    => true,
				),
				'testmode'         => array(
					'title'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
					'type'        => 'checkbox',
					'label'       => __( 'Running in test mode', 'woo-redsys-gateway-light' ),
					'default'     => 'yes',
					'description' => __( 'Select this option for the initial testing required by your bank, deselect this option once you pass the required test phase and your production environment is active.', 'woo-redsys-gateway-light' ),
				),
				'debug'            => array(
					'title'       => __( 'Debug Log', 'woo-redsys-gateway-light' ),
					'type'        => 'checkbox',
					'label'       => __( 'Enable logging', 'woo-redsys-gateway-light' ),
					'default'     => 'no',
					'description' => __( 'Log Inespay events, such as notifications requests, inside <code>WooCommerce > Status > Logs</code> (search for logs named "inespayredsys").', 'woo-redsys-gateway-light' ),
				),
			);
		}

		/**
		 * Process payment.
		 *
		 * @param int $order_id Order ID.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $this->api_key || ! $this->api_token ) {
				wc_add_notice( __( 'Payment error: Inespay credentials are missing.', 'woo-redsys-gateway-light' ), 'error' );
				return array(
					'result'   => 'failure',
					'redirect' => wc_get_checkout_url(),
				);
			}

			$success_url = $order->get_checkout_order_received_url();
			$abort_url   = wc_get_checkout_url();

			$payload = array(
				'amount'                    => WCRedL()->redsys_amount_format( $order->get_total() ),
				'currency'                  => 'EUR',
				'description'               => sprintf(
					/* translators: 1: Order number, 2: Store name. */
					__( 'Order %1$s - %2$s', 'woo-redsys-gateway-light' ),
					$order->get_order_number(),
					wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES )
				),
				'reference'                 => (string) WCRedL()->prepare_order_number( $order->get_id() ),
				'successLinkRedirect'       => $success_url,
				'successLinkRedirectMethod' => 'GET',
				'abortLinkRedirect'         => $abort_url,
				'abortLinkRedirectMethod'   => 'GET',
				'notifUrl'                  => $this->notify_url,
				'notifUrlContentType'       => 'json',
			);

			if ( ! empty( $this->expiration ) ) {
				$payload['expiration'] = absint( $this->expiration );
			}

			if ( ! empty( $this->creditor_account ) ) {
				$payload['creditorAccount'] = $this->creditor_account;
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'process_payment payload (single): ' . print_r( $payload, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			$response = wp_remote_post(
				$this->get_api_url( 'v22/payins/single/init' ),
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'X-Api-Key'     => $this->api_key,
						'Authorization' => $this->api_token,
					),
					'body'    => wp_json_encode( $payload ),
					'timeout' => 30,
				)
			);

			if ( is_wp_error( $response ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'inespayredsys', 'process_payment error: ' . $response->get_error_message() );
				}
				wc_add_notice( __( 'Could not start payment with Inespay. Please try again or use another method.', 'woo-redsys-gateway-light' ), 'error' );
				return array(
					'result'   => 'failure',
					'redirect' => wc_get_checkout_url(),
				);
			}

			$code = wp_remote_retrieve_response_code( $response );
			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'process_payment response (' . $code . '): ' . print_r( $body, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( 200 !== $code || empty( $body['singlePayinLink'] ) || empty( $body['singlePayinId'] ) ) {
				wc_add_notice( __( 'Could not start payment with Inespay. Please try again or use another method.', 'woo-redsys-gateway-light' ), 'error' );
				return array(
					'result'   => 'failure',
					'redirect' => wc_get_checkout_url(),
				);
			}

			WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_single_payin_id' => sanitize_text_field( $body['singlePayinId'] ) ) );
			WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_status' => 'initiated' ) );
			WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_reference' => sanitize_text_field( $payload['reference'] ) ) );

			if ( ! empty( $payload['creditorAccount'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_creditor_account' => sanitize_text_field( $payload['creditorAccount'] ) ) );
			}

			return array(
				'result'   => 'success',
				'redirect' => esc_url_raw( $body['singlePayinLink'] ),
			);
		}

		/**
		 * Handle callback from Inespay.
		 */
		public function handle_callback() {
			if ( 'yes' === $this->debug ) {
				$remote_ip = '';
				if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
					$remote_ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
					$remote_ip = $remote_ip ? $remote_ip : '';
				}
				$this->log->add( 'inespayredsys', 'Callback received from IP: ' . $remote_ip );
			}

			$raw_body = file_get_contents( 'php://input' );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'Callback raw body: ' . $raw_body );
			}

			$data = json_decode( $raw_body, true );

			// Handle form-encoded payloads.
			if ( empty( $data ) && ! empty( $raw_body ) ) {
				$parsed = array();
				parse_str( $raw_body, $parsed );
				if ( ! empty( $parsed ) && is_array( $parsed ) ) {
					$data = $parsed;
				}
			}

			if ( empty( $data ) && ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			}

			// Inespay sends dataReturn (base64 JSON) + signatureDataReturn. Decode it when present.
			if ( empty( $data['singlePayinId'] ) && ! empty( $data['dataReturn'] ) ) {
				$decoded_json = base64_decode( sanitize_text_field( $data['dataReturn'] ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'inespayredsys', 'Decoded dataReturn JSON: ' . $decoded_json );
				}
				$decoded_arr = json_decode( $decoded_json, true );
				if ( is_array( $decoded_arr ) ) {
					$data = array_merge( $data, $decoded_arr );
				}
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'Callback payload: ' . print_r( $data, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( empty( $data['singlePayinId'] ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'inespayredsys', 'Callback missing singlePayinId, returning OK to avoid retries.' );
				}
				wp_die( 'OK', '', array( 'response' => 200 ) );
			}

			$order = $this->get_order_by_payin_id( sanitize_text_field( $data['singlePayinId'] ) );

			if ( ! $order ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'inespayredsys', 'Order not found for singlePayinId: ' . $data['singlePayinId'] . '. Returning 200 to avoid retries.' );
				}
				wp_die( 'OK', '', array( 'response' => 200 ) );
			}

			if ( ! empty( $data['codStatus'] ) && in_array( $data['codStatus'], array( 'OK', 'SETTLED' ), true ) ) {
				$order->payment_complete();
				$order->add_order_note(
					sprintf(
						/* translators: 1: Inespay ID, 2: Status */
						__( 'Inespay payment completed. ID: %1$s, Status: %2$s', 'woo-redsys-gateway-light' ),
						esc_html( $data['singlePayinId'] ),
						esc_html( $data['codStatus'] )
					)
				);

				if ( 'completed' === $this->orderdo ) {
					$order->update_status( 'completed', __( 'Order marked as complete per gateway settings.', 'woo-redsys-gateway-light' ) );
				}

				// Store payment meta.
				WCRedL()->update_order_meta( $order->get_id(), array( '_payment_method' => $this->id ) );
				WCRedL()->update_order_meta( $order->get_id(), array( '_redsys_done' => 'yes' ) );

				do_action( 'inespay_post_payment_complete', $order->get_id() );
			} else {
				$order->add_order_note(
					sprintf(
						/* translators: %s: Inespay status code. */
						__( 'Inespay callback received with status: %s', 'woo-redsys-gateway-light' ),
						isset( $data['codStatus'] ) ? esc_html( $data['codStatus'] ) : __( 'unknown', 'woo-redsys-gateway-light' )
					)
				);
			}

			// Persist meta with transaction data.
			if ( isset( $data['singlePayinId'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_single_payin_id' => sanitize_text_field( $data['singlePayinId'] ) ) );
			}
			if ( isset( $data['codStatus'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_status' => sanitize_text_field( $data['codStatus'] ) ) );
			}
			if ( isset( $data['debtorAccount'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_debtor_account' => sanitize_text_field( $data['debtorAccount'] ) ) );
			}
			if ( isset( $data['debtorName'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_debtor_name' => sanitize_text_field( $data['debtorName'] ) ) );
			}
			if ( isset( $data['reference'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_reference' => sanitize_text_field( $data['reference'] ) ) );
			}
			if ( isset( $data['creditorAccount'] ) ) {
				WCRedL()->update_order_meta( $order->get_id(), array( '_inespay_creditor_account' => sanitize_text_field( $data['creditorAccount'] ) ) );
			}

			wp_die( 'OK', '', array( 'response' => 200 ) );
		}

		/**
		 * Process refund through Inespay.
		 *
		 * @param int    $order_id Order ID.
		 * @param float  $amount Amount.
		 * @param string $reason Reason.
		 * @return bool|WP_Error
		 */
		public function process_refund( $order_id, $amount = null, $reason = '' ) {
			$order    = wc_get_order( $order_id );
			$payin_id = $order ? WCRedL()->get_order_meta( $order_id, '_inespay_single_payin_id', true ) : '';

			if ( empty( $payin_id ) ) {
				return new WP_Error( 'inespay_refund_missing_payin', __( 'No Inespay payment ID found for this order.', 'woo-redsys-gateway-light' ) );
			}

			$refund_amount = $amount ? $amount : $order->get_total();
			/* translators: 1: Order number, 2: Refund reason. */
			$description = sprintf( __( 'Refund order %1$s %2$s', 'woo-redsys-gateway-light' ), $order->get_order_number(), $reason );
			$payload     = array(
				'singlePayinId'            => $payin_id,
				'amount'                   => WCRedL()->redsys_amount_format( $refund_amount ),
				'description'              => $description,
				'reference'                => (string) WCRedL()->prepare_order_number( $order->get_id() ) . '-refund',
				'okNotifUrl'               => $this->notify_url,
				'errorNotifUrl'            => $this->notify_url,
				'okNotifUrlContentType'    => 'json',
				'errorNotifUrlContentType' => 'json',
			);

			if ( ! empty( $this->creditor_account ) ) {
				$payload['collectingIBAN'] = $this->creditor_account;
			}

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'process_refund payload: ' . print_r( $payload, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			$response = wp_remote_post(
				$this->get_api_url( 'v22/refunds/init' ),
				array(
					'headers' => array(
						'Content-Type'  => 'application/json',
						'X-Api-Key'     => $this->api_key,
						'Authorization' => $this->api_token,
					),
					'body'    => wp_json_encode( $payload ),
					'timeout' => 30,
				)
			);

			if ( is_wp_error( $response ) ) {
				if ( 'yes' === $this->debug ) {
					$this->log->add( 'inespayredsys', 'process_refund error: ' . $response->get_error_message() );
				}
				return $response;
			}

			$code = wp_remote_retrieve_response_code( $response );
			$body = json_decode( wp_remote_retrieve_body( $response ), true );

			if ( 'yes' === $this->debug ) {
				$this->log->add( 'inespayredsys', 'process_refund response (' . $code . '): ' . print_r( $body, true ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
			}

			if ( 200 !== $code || empty( $body['status'] ) || '200' !== (string) $body['status'] ) {
				return new WP_Error( 'inespay_refund_failed', __( 'Inespay refund failed.', 'woo-redsys-gateway-light' ) );
			}

			$order->add_order_note(
				sprintf(
					/* translators: 1: Amount, 2: Payin ID. */
					__( 'Inespay refund initiated for %1$s EUR. Payin ID: %2$s', 'woo-redsys-gateway-light' ),
					esc_html( $payload['amount'] ),
					esc_html( $payin_id )
				)
			);
			return true;
		}

		/**
		 * Build API URL for endpoint.
		 *
		 * @param string $path Path.
		 * @return string
		 */
		protected function get_api_url( $path ) {
			$base = 'san' === $this->environment ? 'https://apiflow.inespay.com/san/' : 'https://apiflow.inespay.com/pro/';
			return $base . ltrim( $path, '/' );
		}

		/**
		 * Find order by payin id.
		 *
		 * @param string $payin_id Payin ID.
		 * @return WC_Order|false
		 */
		protected function get_order_by_payin_id( $payin_id ) {
			$orders = wc_get_orders(
				array(
					'limit'        => 1,
					'meta_key'     => '_inespay_single_payin_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'   => $payin_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
					'meta_compare' => '=',
				)
			);
			if ( ! empty( $orders ) && is_a( $orders[0], 'WC_Order' ) ) {
				return $orders[0];
			}
			return false;
		}

		/**
		 * Display warning when test mode is active.
		 */
		public function warning_checkout_test_mode_inespay() {
			if ( 'yes' === $this->testmode ) {
				echo '<div class="checkout-message" style="
					background-color: #f39c12;
					padding: 1em 1.618em;
					margin-bottom: 2.617924em;
					margin-left: 0;
					border-radius: 2px;
					color: #fff;
					clear: both;
					border-left: 0.6180469716em solid rgb(228, 120, 51);
					">';
				echo esc_html__( 'Warning: Inespay is in test mode', 'woo-redsys-gateway-light' );
				echo '</div>';
			}
		}
	}

endif;
