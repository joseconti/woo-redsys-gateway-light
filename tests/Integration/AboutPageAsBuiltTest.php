<?php
/**
 * Integration tests for AC-49 (D-045, slice S-060): the About Redsys page
 * under the WooCommerce menu, who may open it, and the six filters that
 * change its lists.
 *
 * As-built: the tests describe the code as it is and change none of it.
 * The page in a real browser session, and the refusal a user without
 * `manage_options` gets, are driven in tests/e2e/as-built-screens.spec.js.
 *
 * Runs only inside wp-env's tests-cli container — see
 * tests/bootstrap-integration.php.
 *
 * @package WooCommerce Redsys Gateway Light
 */

class AboutPageAsBuiltTest extends WP_UnitTestCase {

	/**
	 * Filters added by a test.
	 *
	 * @var string[]
	 */
	private $filters = array();

	public function set_up() {
		parent::set_up();
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$this->filters = array();
	}

	public function tear_down() {
		foreach ( $this->filters as $filter ) {
			remove_all_filters( $filter );
		}
		unset( $GLOBALS['submenu']['woocommerce'], $GLOBALS['_wp_submenu_nopriv']['woocommerce'] );
		parent::tear_down();
	}

	/**
	 * The page as the plugin prints it.
	 *
	 * @return string
	 */
	private function page() {
		ob_start();
		redsys_about_page();
		return (string) ob_get_clean();
	}

	/**
	 * The submenu entry of the page, if the menu has one.
	 *
	 * @return array|null
	 */
	private function menu_entry() {
		global $submenu;
		if ( empty( $submenu['woocommerce'] ) ) {
			return null;
		}
		foreach ( $submenu['woocommerce'] as $entry ) {
			if ( 'redsys-about-page' === $entry[2] ) {
				return $entry;
			}
		}
		return null;
	}

	public function test_the_page_is_in_the_woocommerce_menu_for_a_user_who_manages_options() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );

		$this->assertNotFalse( has_action( 'admin_menu', 'redsys_menu' ) );
		redsys_menu();

		$entry = $this->menu_entry();
		$this->assertNotNull( $entry, 'The page must hang from the WooCommerce menu.' );
		$this->assertSame( 'About Redsys', $entry[0] );
		$this->assertSame( 'manage_options', $entry[1] );
	}

	public function test_the_page_is_not_in_the_menu_of_a_shop_manager_and_wordpress_refuses_it() {
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'shop_manager' ) ) );
		$this->assertFalse( current_user_can( 'manage_options' ) );

		redsys_menu();

		$this->assertNull( $this->menu_entry() );
		$this->assertTrue(
			! empty( $GLOBALS['_wp_submenu_nopriv']['woocommerce']['redsys-about-page'] ),
			'WordPress must have the page recorded as one this user may not open.'
		);
	}

	public function test_the_page_prints_its_heading_and_the_six_lists() {
		$page = $this->page();

		$this->assertStringContainsString( 'Other plugins, Skills &amp; APPS', $page );
		foreach ( array(
			'packdesk-lockup.svg',
			'Enable Block Editor For WC Products',
			'Easy License Manager for WooCommerce',
			'https://nonprofits.joseconti.com/',
			'https://skills.joseconti.com/plugin/keel.html',
			'https://gist.github.com/joseconti',
		) as $from_a_default_list ) {
			$this->assertStringContainsString( $from_a_default_list, $page );
		}
	}

	/**
	 * Filter name and something only the default list prints.
	 *
	 * @return array[]
	 */
	public function data_lists() {
		return array(
			'free plugins'    => array( 'redsys_lite_apps_plugins_free', 'Enable Block Editor For WC Products' ),
			'premium plugins' => array( 'redsys_lite_apps_plugins_premium', 'Easy License Manager for WooCommerce' ),
			'websites'        => array( 'redsys_lite_apps_plugins_webs', 'https://nonprofits.joseconti.com/' ),
			'skills'          => array( 'redsys_lite_apps_plugins_skills', 'https://skills.joseconti.com/plugin/keel.html' ),
			'profiles'        => array( 'redsys_lite_apps_plugins_profiles', 'https://gist.github.com/joseconti' ),
		);
	}

	/**
	 * @dataProvider data_lists
	 * @param string $filter       Filter name.
	 * @param string $from_default Something only the default list prints.
	 */
	public function test_a_list_is_what_its_filter_returns( $filter, $from_default ) {
		$this->filters[] = $filter;
		add_filter(
			$filter,
			function ( $items ) use ( $filter ) {
				// The filter is handed the default list.
				$this->assertNotEmpty( $items );
				return array(
					array(
						'ini'   => 'ZZ',
						'bg'    => '#ffffff',
						'fg'    => '#000000',
						'name'  => 'Replaced through ' . $filter,
						'desc'  => 'A description from the filter.',
						'badge' => '',
						'url'   => 'https://from-the-filter.example.org/' . $filter,
					),
				);
			}
		);

		$page = $this->page();

		$this->assertStringContainsString( 'Replaced through ' . $filter, $page );
		$this->assertStringContainsString( 'https://from-the-filter.example.org/' . $filter, $page );
		$this->assertStringContainsString( 'from-the-filter.example.org</', $page, 'The host shown on the card is taken from the filtered URL.' );
		$this->assertStringNotContainsString( $from_default, $page );
	}

	public function test_the_featured_app_is_what_its_filter_returns() {
		$this->filters[] = 'redsys_lite_apps_plugins_mac_app';
		add_filter(
			'redsys_lite_apps_plugins_mac_app',
			function ( $app ) {
				$this->assertSame( 'PackDesk', $app['name'] );
				return array(
					'name'         => 'An app from the filter',
					'badge'        => 'Filtered badge',
					'logo'         => '',
					'desc'         => 'A description from the filter.',
					'version'      => '9.9.9-filtered',
					'requirements' => '',
				);
			}
		);

		$page = $this->page();

		$this->assertStringContainsString( 'An app from the filter', $page );
		$this->assertStringContainsString( 'Filtered badge', $page );
		$this->assertStringContainsString( '9.9.9-filtered', $page );
		$this->assertStringNotContainsString( 'packdesk-lockup.svg', $page );
	}
}
