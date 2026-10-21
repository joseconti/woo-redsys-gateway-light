<?php
/**
 * Reproduction test for S-094: get_acctinfo() subtracted the customer's
 * `last_update` user meta from a timestamp. A customer without that meta
 * (an account WooCommerce never saved, paying an order an administrator
 * created) made PHP 7 warn and PHP 8 throw a TypeError at the card checkout.
 * Written and seen failing against the code as it was before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class AcctInfoWithoutLastUpdateTest extends WP_UnitTestCase {

	public function tear_down() {
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	public function test_acct_info_of_a_customer_without_last_update_meta_is_built() {
		$user_id = self::factory()->user->create();
		delete_user_meta( $user_id, 'last_update' );
		wp_set_current_user( $user_id );
		$order = wc_create_order( array( 'customer_id' => $user_id ) );
		$order->set_billing_email( 'buyer@example.test' );
		$order->set_total( 10 );
		$order->save();

		$emv3ds = json_decode( WCPSD2L()->get_acctinfo( $order ), true );

		// Registered today. The age is counted from midnight of the registration
		// day, so "just created" (02) or "under 30 days" (03) by time of day.
		$this->assertContains( $emv3ds['acctInfo']['chAccAgeInd'], array( '02', '03' ) );
		// Never changed since it was created a moment ago: the change is the creation.
		$this->assertSame( gmdate( 'Ymd' ), $emv3ds['acctInfo']['chAccChange'] );
		$this->assertSame( '01', $emv3ds['acctInfo']['chAccChangeInd'] );
	}
}
