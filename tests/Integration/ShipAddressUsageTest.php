<?php
/**
 * Reproduction tests for S-095: the 3DS2 shipping-address usage that S-092
 * started sending to Redsys was computed wrong — the age in days divided by
 * 216000 seconds instead of 86400, and the "first use" query matched any
 * customer's order and the current order itself. Written and seen failing
 * against the code as it was before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class ShipAddressUsageTest extends WP_UnitTestCase {

	public function tear_down() {
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	public function test_the_first_use_is_this_customer_s_earliest_earlier_order_with_the_address() {
		$customer = self::factory()->user->create();
		$stranger = self::factory()->user->create();
		$this->order_to_the_address( $stranger, 100 );
		$this->order_to_the_address( $customer, 45 );
		wp_set_current_user( $customer );
		$order = $this->order_to_the_address( $customer, 0 );

		$acct_info = json_decode( WCPSD2L()->get_acctinfo( $order ), true )['acctInfo'];

		$this->assertSame( gmdate( 'Ymd', time() - 45 * DAY_IN_SECONDS ), $acct_info['shipAddressUsage'] );
		$this->assertSame( '03', $acct_info['shipAddressUsageInd'] );
	}

	public function test_an_address_used_more_than_60_days_ago_is_04() {
		$customer = self::factory()->user->create();
		$this->order_to_the_address( $customer, 61 );
		wp_set_current_user( $customer );
		$order = $this->order_to_the_address( $customer, 0 );

		$acct_info = json_decode( WCPSD2L()->get_acctinfo( $order ), true )['acctInfo'];

		$this->assertSame( '04', $acct_info['shipAddressUsageInd'] );
	}

	public function test_an_address_never_used_before_is_used_for_the_first_time_now() {
		$customer = self::factory()->user->create();
		$stranger = self::factory()->user->create();
		$this->order_to_the_address( $stranger, 10 );
		wp_set_current_user( $customer );
		$order = $this->order_to_the_address( $customer, 0 );

		$acct_info = json_decode( WCPSD2L()->get_acctinfo( $order ), true )['acctInfo'];

		$this->assertSame( '01', $acct_info['shipAddressUsageInd'] );
		$this->assertSame( wp_date( 'Ymd' ), $acct_info['shipAddressUsage'] );
	}

	/**
	 * An order shipped to the same address, created some days ago.
	 *
	 * @param int $user_id  Customer.
	 * @param int $days_ago Age of the order in days.
	 * @return WC_Order
	 */
	private function order_to_the_address( $user_id, $days_ago ) {
		$order = wc_create_order( array( 'customer_id' => $user_id ) );
		$order->set_billing_email( 'buyer@example.test' );
		$order->set_shipping_address_1( 'Calle Falsa 1' );
		$order->set_shipping_city( 'Valencia' );
		$order->set_shipping_postcode( '46001' );
		$order->set_shipping_country( 'ES' );
		$order->set_total( 10 );
		$order->set_date_created( time() - $days_ago * DAY_IN_SECONDS );
		$order->save();
		return $order;
	}
}
