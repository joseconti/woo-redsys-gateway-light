<?php
/**
 * Reproduction test for S-096: get_acctinfo() formatted the registration
 * date to Ymd before counting minutes, so the account age ran from midnight
 * of the registration day and "just created" (chAccAgeInd 02) was reached
 * only shortly after midnight. Written and seen failing against the code as
 * it was before the fix.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class AccountAgeIndicatorTest extends WP_UnitTestCase {

	public function tear_down() {
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * @return array[]
	 */
	public function data_ages() {
		return array(
			'registered 5 minutes ago' => array( 5 * MINUTE_IN_SECONDS, '02' ),
			'registered 2 hours ago'   => array( 2 * HOUR_IN_SECONDS, '03' ),
			'registered 45 days ago'   => array( 45 * DAY_IN_SECONDS, '04' ),
			'registered 61 days ago'   => array( 61 * DAY_IN_SECONDS, '05' ),
		);
	}

	/**
	 * @dataProvider data_ages
	 * @param int    $age      Seconds since registration.
	 * @param string $expected chAccAgeInd.
	 */
	public function test_the_account_age_counts_from_the_registration_time( $age, $expected ) {
		$user_id = self::factory()->user->create( array( 'user_registered' => gmdate( 'Y-m-d H:i:s', time() - $age ) ) );
		wp_set_current_user( $user_id );
		$order = wc_create_order( array( 'customer_id' => $user_id ) );
		$order->set_billing_email( 'buyer@example.test' );
		$order->set_total( 10 );
		$order->save();

		$acct_info = json_decode( WCPSD2L()->get_acctinfo( $order ), true )['acctInfo'];

		$this->assertSame( $expected, $acct_info['chAccAgeInd'] );
		$this->assertSame( gmdate( 'Ymd', time() - $age ), $acct_info['chAccDate'] );
	}
}
