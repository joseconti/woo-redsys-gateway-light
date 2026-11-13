<?php
/**
 * Integration tests: the two admin notices of the plugin and the redirect
 * to its About page are for users who manage the store (S-047, S-048).
 *
 * Both notices were printed for every user who can open an admin screen,
 * with a dismiss link whose nonce is the user's own, and dismissing wrote a
 * site-wide option without asking who was dismissing. The welcome redirect
 * ran on every admin_init, which also fires for admin-ajax.php and
 * admin-post.php requests from visitors who are not logged in.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

/**
 * Thrown in place of the redirect, which would otherwise end the test run.
 */
class Redsyslite_Test_Redirect extends Exception {}

class AdminNoticesCapabilityTest extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();
		delete_option( 'hide-new-version-redsys-notice' );
		delete_option( 'telegram-redsys-notice' );
		add_filter( 'wp_redirect', array( $this, 'stop_at_the_redirect' ) );
	}

	public function tear_down() {
		unset( $_REQUEST['redsys-hide-new-version'], $_REQUEST['_redsys_hide_new_version_nonce'], $_REQUEST['redsys-telegram'], $_REQUEST['_redsys_telegram_nonce'] );
		delete_option( 'hide-new-version-redsys-notice' );
		delete_option( 'telegram-redsys-notice' );
		delete_option( 'woocommerce-redsys-version' );
		delete_option( 'woocommerce-redsys-rate' );
		remove_all_filters( 'wp_doing_ajax' );
		wp_set_current_user( 0 );
		parent::tear_down();
	}

	/**
	 * @param string $location Redirect target.
	 * @throws Redsyslite_Test_Redirect Always.
	 */
	public function stop_at_the_redirect( $location ) {
		throw new Redsyslite_Test_Redirect( $location );
	}

	/**
	 * The two notices: callback, dismiss parameter and value, nonce field
	 * and action, option written and the value it gets.
	 *
	 * @return array[]
	 */
	public function notices() {
		return array(
			'new version' => array( 'redsys_lite_add_notice_new_version', 'redsys-hide-new-version', 'hide-new-version-redsys', '_redsys_hide_new_version_nonce', 'redsys_hide_new_version_nonce', 'hide-new-version-redsys-notice', REDSYS_WOOCOMMERCE_VERSION ),
			'Telegram'    => array( 'redsys_lite_ask_for_telegram', 'redsys-telegram', 'telegram-redsys', '_redsys_telegram_nonce', 'redsys_telegram_nonce', 'telegram-redsys-notice', 'yes' ),
		);
	}

	/**
	 * Runs a notice callback as the given role and returns what it printed.
	 *
	 * @param string $role     Role of the current user.
	 * @param string $callback Notice callback.
	 * @param array  $request  Request parameters to set, name => value; the
	 *                         nonce is created for that user when the value is true.
	 * @return string
	 */
	private function run_notice_as( $role, $callback, $request = array() ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => $role ) ) );
		foreach ( $request as $name => $value ) {
			$_REQUEST[ $name ] = $value;
		}
		ob_start();
		$callback();
		return ob_get_clean();
	}

	/**
	 * @dataProvider notices
	 */
	public function test_a_user_who_does_not_manage_the_store_cannot_dismiss_a_notice_for_everyone( $callback, $param, $value, $nonce_field, $nonce_action, $option, $written ) {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'contributor' ) ) );
		$_REQUEST[ $param ]       = $value;
		$_REQUEST[ $nonce_field ] = wp_create_nonce( $nonce_action );

		ob_start();
		$callback();
		ob_end_clean();

		$this->assertFalse( get_option( $option ), 'A contributor hid a store notice for every administrator.' );
	}

	/**
	 * @dataProvider notices
	 */
	public function test_a_user_who_does_not_manage_the_store_is_not_shown_the_notice( $callback ) {
		$this->assertSame( '', $this->run_notice_as( 'contributor', $callback ) );
	}

	/**
	 * @dataProvider notices
	 */
	public function test_a_shop_manager_sees_the_notice_and_can_dismiss_it( $callback, $param, $value, $nonce_field, $nonce_action, $option, $written ) {
		$printed = $this->run_notice_as( 'shop_manager', $callback );
		$this->assertStringContainsString( $nonce_field, $printed, 'The notice with its dismiss link is expected for a shop manager.' );

		$_REQUEST[ $param ]       = $value;
		$_REQUEST[ $nonce_field ] = wp_create_nonce( $nonce_action );
		ob_start();
		$callback();
		ob_end_clean();

		$this->assertSame( $written, get_option( $option ) );
	}

	/**
	 * @dataProvider notices
	 */
	public function test_a_dismissal_without_its_nonce_writes_nothing( $callback, $param, $value, $nonce_field, $nonce_action, $option ) {
		$this->run_notice_as( 'shop_manager', $callback, array( $param => $value ) );

		$this->assertFalse( get_option( $option ) );
	}

	/**
	 * Runs the welcome redirect and reports whether it redirected.
	 *
	 * @return bool
	 */
	private function welcome_splash_redirects() {
		try {
			redsys_welcome_splash();
		} catch ( Redsyslite_Test_Redirect $redirect ) {
			return true;
		}
		return false;
	}

	public function test_a_visitor_who_is_not_logged_in_does_not_trigger_the_welcome_redirect() {
		update_option( 'woocommerce-redsys-version', '0.0.1' );
		wp_set_current_user( 0 );

		$this->assertFalse( $this->welcome_splash_redirects(), 'An anonymous request to an admin entry point was answered with the welcome redirect.' );
		$this->assertSame( '0.0.1', get_option( 'woocommerce-redsys-version' ), 'The one-time welcome was used up by a visitor.' );
		$this->assertFalse( get_option( 'woocommerce-redsys-rate' ) );
	}

	public function test_a_user_who_cannot_open_the_about_page_is_not_redirected_to_it() {
		update_option( 'woocommerce-redsys-version', '0.0.1' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'shop_manager' ) ) );

		$this->assertFalse( $this->welcome_splash_redirects() );
		$this->assertSame( '0.0.1', get_option( 'woocommerce-redsys-version' ) );
	}

	public function test_an_ajax_request_is_never_answered_with_the_welcome_redirect() {
		update_option( 'woocommerce-redsys-version', '0.0.1' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		add_filter( 'wp_doing_ajax', '__return_true' );

		$this->assertFalse( $this->welcome_splash_redirects() );
		$this->assertSame( '0.0.1', get_option( 'woocommerce-redsys-version' ) );
	}

	/**
	 * A form the administrator submits right after an update must reach its
	 * handler: the welcome waits for the next screen they open.
	 */
	public function test_a_form_submission_is_not_swallowed_by_the_welcome_redirect() {
		update_option( 'woocommerce-redsys-version', '0.0.1' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		$_SERVER['REQUEST_METHOD'] = 'POST';

		$redirected = $this->welcome_splash_redirects();
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$this->assertFalse( $redirected );
		$this->assertSame( '0.0.1', get_option( 'woocommerce-redsys-version' ), 'The welcome is still owed.' );
		$this->assertTrue( $this->welcome_splash_redirects(), 'It is shown on the next screen.' );
	}

	public function test_an_administrator_is_welcomed_once() {
		update_option( 'woocommerce-redsys-version', '0.0.1' );
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->assertTrue( $this->welcome_splash_redirects() );
		$this->assertSame( REDSYS_WOOCOMMERCE_VERSION, get_option( 'woocommerce-redsys-version' ) );
		$this->assertFalse( $this->welcome_splash_redirects(), 'The welcome is shown once per version.' );
	}
}
