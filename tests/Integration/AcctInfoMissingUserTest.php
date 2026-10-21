<?php
/**
 * Reproduction test for S-098: get_acctinfo() read get_userdata()'s result
 * as an object. Called with the id of a user that does not exist, it read a
 * property of false — a notice on PHP 7, a warning on PHP 8 — and built
 * the account age from it. No caller passes such an id today; the
 * method is public. Written and seen failing against the code as it was
 * before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class AcctInfoMissingUserTest extends WP_UnitTestCase {

	public function test_acct_info_for_a_user_that_does_not_exist_is_a_guest() {
		wp_set_current_user( 0 );
		$order = wc_create_order();
		$order->set_billing_email( 'buyer@example.test' );
		$order->set_total( 10 );
		$order->save();

		$emv3ds = json_decode( WCPSD2L()->get_acctinfo( $order, false, PHP_INT_MAX ), true );

		$this->assertSame( '01', $emv3ds['acctInfo']['chAccAgeInd'] );
	}
}
