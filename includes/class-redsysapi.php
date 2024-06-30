<?php
/**
 * Class RedsysAPI
 *
 * @package WooCommerce Redsys Gateway Ligth
 *
 * @version 2.0.0
 */

/**
 * NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
 *
 * El uso de este software está sujeto a las Condiciones de uso de software que
 * se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
 * obtener una copia en la siguiente url:
 * http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
 *
 * Redsys es titular de todos los derechos de propiedad intelectual e industrial
 * del software.
 *
 * Quedan expresamente prohibidas la reproducción, la distribución y la
 * comunicación pública, incluida su modalidad de puesta a disposición con fines
 * distintos a los descritos en las Condiciones de uso.
 *
 * Redsys se reserva la posibilidad de ejercer las acciones legales que le
 * correspondan para hacer valer sus derechos frente a cualquier infracción de
 * los derechos de propiedad intelectual y/o industrial.
 *
 * Redsys Servicios de Procesamiento, S.L., CIF B85955367
 */
class RedsysAPI {

	/**
	 * Array de DatosEntrada.
	 *
	 * @var array
	 */
	protected $vars_pay = array();

	/**
	 * Set parameter.
	 *
	 * @param string $key  Nombre del parámetro.
	 * @param mixed  $value Valor del parámetro.
	 */
	public function set_parameter( $key, $value ) {
		$this->vars_pay[$key] = $value; // phpcs:ignore WordPress.Arrays.ArrayKeySpacingRestrictions.NoSpacesAroundArrayKeys
	}
	/**
	 * Get parameter.
	 *
	 * @param string $key Nombre del parámetro.
	 * @return mixed
	 */
	public function get_parameter( $key ) {
		return array_key_exists( $key, $this->vars_pay ) ? $this->vars_pay[ $key ] : null;
	}
	/**
	 * FUNCIONES AUXILIARES:
	 */
	/**
	 * Cifra el mensaje con 3DES.
	 *
	 * @param string $message Mensaje a cifrar.
	 * @param string $key     Clave para cifrar.
	 */
	public function encrypt_3des( $message, $key ) {
		// Se establece un IV por defecto.
		$bytes = array( 0, 0, 0, 0, 0, 0, 0, 0 );
		$iv    = implode( array_map( 'chr', $bytes ) );

		// Se cifra.
		$long       = ceil( strlen( $message ) / 16 ) * 16;
		$ciphertext = substr( openssl_encrypt( $message . str_repeat( "\0", $long - strlen( $message ) ), 'des-ede3-cbc', $key, OPENSSL_RAW_DATA, $iv ), 0, $long );

		return $ciphertext;
	}
	/**
	 * Descifra el mensaje con 3DES.
	 *
	 * @param string $input Mensaje a descifrar.
	 */
	public function base64_url_encode( $input ) {
		return strtr( base64_encode( $input ), '+/', '-_' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
	/**
	 * Encodes data to base64.
	 *
	 * @param mixed $data Data to encode.
	 * @return string
	 */
	public function encode_base64( $data ) {
		$data = base64_encode( $data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return $data;
	}
	/**
	 * Decodes base64-encoded data.
	 *
	 * @param string $input Base64-encoded input.
	 * @return string
	 */
	public function base64_url_decode( $input ) {
		return base64_decode( strtr( $input, '-_', '+/' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
	}
	/**
	 * Decodes data from base64.
	 *
	 * @param string $data Data to decode.
	 * @return string
	 */
	public function decode_base64( $data ) {
		$data = base64_decode( $data ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		return $data;
	}
	/**
	 *  MAC Function
	 *
	 * @param string $ent Mensaje a cifrar.
	 * @param string $key Clave para cifrar.
	 *
	 * @return string
	 */
	public function mac256( $ent, $key ) {
		$res = hash_hmac( 'sha256', $ent, $key, true );
		return $res;
	}
	/**
	 *  FUNCIONES PARA LA GENERACIÓN DEL FORMULARIO DE PAGO.
	 */
	/**
	 * Obtener Número de pedido.
	 *
	 * @return string Número de pedido.
	 */
	public function get_order() {
		$num_pedido = '';
		if ( empty( $this->vars_pay['DS_MERCHANT_ORDER'] ) ) {
			$num_pedido = $this->vars_pay['Ds_Merchant_Order'];
		} else {
			$num_pedido = $this->vars_pay['DS_MERCHANT_ORDER'];
		}
		return $num_pedido;
	}
	/**
	 * Convertir Array en Objeto JSON
	 */
	public function array_to_json() {
		$json = wp_json_encode( $this->vars_pay );
		return $json;
	}
	/**
	 * Crear parámetro Ds_MerchantParameters.
	 *
	 * @return string
	 */
	public function create_merchant_parameters() {
		// Se transforma el array de datos en un objeto Json.
		$json = $this->array_to_json();
		// Se codifican los datos Base64.
		return $this->encode_base64( $json );
	}
	/**
	 * Crear firma del comercio.
	 *
	 * @param string $key Clave para cifrar.
	 *
	 * @return string
	 */
	public function create_merchant_signature( $key ) {
		// Se decodifica la clave Base64.
		$key = $this->decode_base64( $key );
		// Se genera el parámetro Ds_MerchantParameters.
		$ent = $this->create_merchant_parameters();
		// Se diversifica la clave con el Número de Pedido.
		$key = $this->encrypt_3des( $this->get_order(), $key );
		// MAC256 del parámetro Ds_MerchantParameters.
		$res = $this->mac256( $ent, $key );
		// Se codifican los datos Base64.
		return $this->encode_base64( $res );
	}
	/**
	 * FUNCIONES PARA LA RECEPCIÓN DE DATOS DE PAGO (Notif, URLOK y URLKO)
	 */
	/**
	 * Obtener Número de pedido
	 */
	public function get_order_notif() {
		$num_pedido = '';
		if ( empty( $this->vars_pay['Ds_Order'] ) ) {
			$num_pedido = $this->vars_pay['DS_ORDER'];
		} else {
			$num_pedido = $this->vars_pay['Ds_Order'];
		}
		return $num_pedido;
	}
	/**
	 * Get Request Notif SOAP
	 *
	 * @param string $datos Datos de la notificación.
	 */
	public function get_order_notif_soap( $datos ) {
		$pos_pedido_ini = strrpos( $datos, '<Ds_Order>' );
		$tam_pedido_ini = strlen( '<Ds_Order>' );
		$pos_pedido_fin = strrpos( $datos, '</Ds_Order>' );
		return substr( $datos, $pos_pedido_ini + $tam_pedido_ini, $pos_pedido_fin - ( $pos_pedido_ini + $tam_pedido_ini ) );
	}
	/**
	 * Get Request Notif SOAP
	 *
	 * @param string $datos Datos de la notificación.
	 */
	public function get_request_notif_soap( $datos ) {
		$pos_req_ini = strrpos( $datos, '<Request' );
		$pos_req_fin = strrpos( $datos, '</Request>' );
		$tam_req_fin = strlen( '</Request>' );
		return substr( $datos, $pos_req_ini, ( $pos_req_fin + $tam_req_fin ) - $pos_req_ini );
	}
	/**
	 * Get Response Notif SOAP
	 *
	 * @param string $datos Datos de la notificación.
	 */
	public function get_response_notif_soap( $datos ) {
		$pos_req_ini = strrpos( $datos, '<Response' );
		$pos_req_fin = strrpos( $datos, '</Response>' );
		$tam_req_fin = strlen( '</Response>' );
		return substr( $datos, $pos_req_ini, ( $pos_req_fin + $tam_req_fin ) - $pos_req_ini );
	}
	/**
	 * Convertir String en Array
	 *
	 * @param string $datos_decod Datos decodificados.
	 */
	public function string_to_array( $datos_decod ) {
		$this->vars_pay = json_decode( $datos_decod, true );
	}
	/**
	 * Decodificar parámetro Ds_MerchantParameters.
	 *
	 * @param string $datos Datos a decodificar.
	 */
	public function decode_merchant_parameters( $datos ) {
		// Se decodifican los datos Base64.
		$decodec = $this->base64_url_decode( $datos );
		// Los datos decodificados se pasan al array de datos.
		$this->string_to_array( $decodec );
		return $decodec;
	}
	/**
	 * Crear firma del comercio para Notif, URLOK y URLKO.
	 *
	 * @param string $key   Clave para cifrar.
	 * @param string $datos Datos a firmar.
	 */
	public function create_merchant_signature_notif( $key, $datos ) {
		// Se decodifica la clave Base64.
		$key = $this->decode_base64( $key );
		// Se decodifican los datos Base64.
		$decodec = $this->base64_url_decode( $datos );
		// Los datos decodificados se pasan al array de datos.
		$this->string_to_array( $decodec );
		// Se diversifica la clave con el Número de Pedido.
		$key = $this->encrypt_3des( $this->get_order_notif(), $key );
		// MAC256 del parámetro Ds_Parameters que envía Redsys.
		$res = $this->mac256( $datos, $key );
		// Se codifican los datos Base64.
		return $this->base64_url_encode( $res );
	}
	/**
	 * Crear firma del comercio para Notif, URLOK y URLKO.
	 *
	 * @param string $key   Clave para cifrar.
	 * @param string $datos Datos a firmar.
	 */
	public function create_merchant_signature_notif_soap_request( $key, $datos ) {
		// Se decodifica la clave Base64.
		$key = $this->decode_base64( $key );
		// Se obtienen los datos del Request.
		$datos = $this->get_request_notif_soap( $datos );
		// Se diversifica la clave con el Número de Pedido.
		$key = $this->encrypt_3des( $this->get_order_notif_soap( $datos ), $key );
		// MAC256 del parámetro Ds_Parameters que envía Redsys.
		$res = $this->mac256( $datos, $key );
		// Se codifican los datos Base64.
		return $this->encode_base64( $res );
	}
	/**
	 * Crear firma del comercio para Notif, URLOK y URLKO.
	 *
	 * @param string $key   Clave para cifrar.
	 * @param string $datos Datos a firmar.
	 * @param string $num_pedido Número de pedido.
	 */
	public function create_merchant_signature_notif_soap_response( $key, $datos, $num_pedido ) {
		// Se decodifica la clave Base64.
		$key = $this->decode_base64( $key );
		// Se obtienen los datos del Request.
		$datos = $this->get_response_notif_soap( $datos );
		// Se diversifica la clave con el Número de Pedido.
		$key = $this->encrypt_3des( $num_pedido, $key );
		// MAC256 del parámetro Ds_Parameters que envía Redsys.
		$res = $this->mac256( $datos, $key );
		// Se codifican los datos Base64.
		return $this->encode_base64( $res );
	}
}
