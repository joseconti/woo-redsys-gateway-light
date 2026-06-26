<?php
/**
 * "Other plugins, Skills & APPS" section for the About page.
 *
 * Renders an informational landing block inside the plugin About page. It
 * showcases the native macOS management app and the rest of the plugins,
 * websites, skills and profiles published by José Conti, grouped by type.
 *
 * This is the light-plugin port of the premium Redsys_Apps_Plugins_Admin_Settings
 * section, so the content stays in sync between both plugins.
 *
 * @package WooCommerce Redsys Gateway Light
 * @link https://plugins.joseconti.com
 * @since 7.0.3
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Redsys_Lite_Apps_Plugins' ) ) :

	/**
	 * Class Redsys_Lite_Apps_Plugins
	 */
	final class Redsys_Lite_Apps_Plugins {

		/**
		 * Render the "Other plugins, Skills & APPS" section.
		 *
		 * @return void
		 */
		public static function render() {
			$mac      = self::get_mac_app();
			$free     = self::get_free_plugins();
			$premium  = self::get_premium_plugins();
			$webs     = self::get_webs();
			$skills   = self::get_skills();
			$profiles = self::get_profiles();
			?>
			<style>
				.redsys-apps .plug-card { transition: box-shadow .15s ease, transform .15s ease, border-color .15s ease; }
				.redsys-apps .plug-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,.10); transform: translateY(-2px); border-color: #c3c4c7; }
				.redsys-apps .plug-link { transition: gap .15s ease; }
				.redsys-apps .plug-card:hover .plug-link { gap: 9px; }
				.redsys-apps .wp-btn-primary:hover { background: #135e96 !important; border-color: #135e96 !important; }
				.redsys-apps .wp-btn-secondary:hover { background: #f6f7f7 !important; border-color: #0a4b78 !important; color: #0a4b78 !important; }
			</style>
			<div class="redsys-apps" style="max-width:1180px;">

				<h2 style="font-size:20px; font-weight:600; color:#1d2327; margin:18px 0 4px;"><?php esc_html_e( 'Other plugins, Skills & APPS', 'woo-redsys-gateway-light' ); ?></h2>
				<p style="font-size:14px; line-height:1.6; color:#50575e; margin:0 0 22px; max-width:760px;">
					<?php esc_html_e( 'Desktop apps and plugins developed by José Conti. Download the management app and discover the rest of the extensions for WooCommerce and WordPress, grouped by type.', 'woo-redsys-gateway-light' ); ?>
				</p>

				<?php self::render_mac_app( $mac ); ?>

				<?php
				self::render_section_heading(
					__( 'Free plugins', 'woo-redsys-gateway-light' ),
					/* translators: %d: number of available items. */
					sprintf( esc_html__( '%d available', 'woo-redsys-gateway-light' ), count( $free ) ),
					__( 'Download them for free from WordPress.org or GitHub.', 'woo-redsys-gateway-light' )
				);
				?>
				<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; margin-bottom:38px;">
					<?php
					foreach ( $free as $plugin ) {
						self::render_plugin_card(
							$plugin,
							array(
								'label'     => __( 'Free', 'woo-redsys-gateway-light' ),
								'fg'        => '#2a7d4f',
								'bg'        => '#edf7f0',
								'border'    => '#cce8d6',
								'uppercase' => true,
							),
							__( 'View plugin', 'woo-redsys-gateway-light' )
						);
					}
					?>
				</div>

				<?php
				self::render_section_heading(
					__( 'Premium plugins', 'woo-redsys-gateway-light' ),
					/* translators: %d: number of available items. */
					sprintf( esc_html__( '%d available', 'woo-redsys-gateway-light' ), count( $premium ) ),
					__( 'Advanced solutions with support and updates.', 'woo-redsys-gateway-light' )
				);
				?>
				<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; margin-bottom:38px;">
					<?php
					foreach ( $premium as $plugin ) {
						self::render_plugin_card(
							$plugin,
							array(
								'label'     => __( 'Premium', 'woo-redsys-gateway-light' ),
								'fg'        => '#8a5a00',
								'bg'        => '#fcf6e8',
								'border'    => '#f0e0bb',
								'uppercase' => true,
							),
							__( 'View plugin', 'woo-redsys-gateway-light' )
						);
					}
					?>
				</div>

				<?php
				self::render_section_heading(
					__( 'Websites and portals', 'woo-redsys-gateway-light' ),
					'',
					__( 'Official sites and related programs.', 'woo-redsys-gateway-light' )
				);
				?>
				<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; margin-bottom:38px;">
					<?php
					foreach ( $webs as $web ) {
						self::render_web_card( $web );
					}
					?>
				</div>

				<?php
				self::render_section_heading(
					__( 'Skills', 'woo-redsys-gateway-light' ),
					__( 'Marketplace for Claude', 'woo-redsys-gateway-light' ),
					__( 'Abilities for AI assistants.', 'woo-redsys-gateway-light' )
				);
				?>
				<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:16px; margin-bottom:38px;">
					<?php
					foreach ( $skills as $skill ) {
						$badge = array();
						if ( ! empty( $skill['badge'] ) ) {
							$badge = array(
								'label'     => $skill['badge'],
								'fg'        => '#5e3eaa',
								'bg'        => '#efe9fa',
								'border'    => '#ddd0f5',
								'uppercase' => false,
							);
						}
						self::render_plugin_card(
							$skill,
							$badge,
							__( 'View skill', 'woo-redsys-gateway-light' ),
							true
						);
					}
					?>
				</div>

				<?php
				self::render_section_heading(
					__( 'Profiles', 'woo-redsys-gateway-light' ),
					'',
					__( 'Follow me and check out the code.', 'woo-redsys-gateway-light' )
				);
				?>
				<div style="display:flex; gap:12px; flex-wrap:wrap;">
					<?php
					foreach ( $profiles as $profile ) {
						self::render_profile_card( $profile );
					}
					?>
				</div>

			</div>
			<?php
		}

		/**
		 * Render the featured macOS app card.
		 *
		 * @param array $mac Mac app data.
		 * @return void
		 */
		private static function render_mac_app( $mac ) {
			?>
			<section style="background:#fff; border:1px solid #dcdcde; border-radius:8px; box-shadow:0 1px 1px rgba(0,0,0,.04); padding:26px 28px; margin-bottom:34px;">
				<div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
					<?php if ( ! empty( $mac['logo'] ) ) : ?>
						<img src="<?php echo esc_url( $mac['logo'] ); ?>" alt="<?php echo esc_attr( $mac['name'] ); ?>" style="height:54px; width:auto; display:block;" />
					<?php else : ?>
						<h3 style="font-size:19px; font-weight:600; color:#1d2327; margin:0;"><?php echo esc_html( $mac['name'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $mac['badge'] ) ) : ?>
						<span style="font-size:12px; font-weight:600; color:#8a5a00; background:#fcf6e8; border:1px solid #f0e0bb; padding:3px 11px; border-radius:11px; text-transform:uppercase; letter-spacing:.4px;"><?php echo esc_html( $mac['badge'] ); ?></span>
					<?php endif; ?>
				</div>
				<p style="font-size:14px; line-height:1.6; color:#50575e; margin:0 0 14px; max-width:620px;"><?php echo esc_html( $mac['desc'] ); ?></p>
				<div style="display:flex; gap:18px; flex-wrap:wrap; font-size:12.5px; color:#646970;">
					<?php if ( ! empty( $mac['version'] ) ) : ?>
						<span><strong style="color:#1d2327; font-weight:600;"><?php esc_html_e( 'Version', 'woo-redsys-gateway-light' ); ?></strong> <?php echo esc_html( $mac['version'] ); ?></span>
					<?php endif; ?>
					<?php if ( ! empty( $mac['version'] ) && ! empty( $mac['requirements'] ) ) : ?>
						<span style="color:#c3c4c7;">&middot;</span>
					<?php endif; ?>
					<?php if ( ! empty( $mac['requirements'] ) ) : ?>
						<span><strong style="color:#1d2327; font-weight:600;"><?php esc_html_e( 'Requirements', 'woo-redsys-gateway-light' ); ?></strong> <?php echo esc_html( $mac['requirements'] ); ?></span>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}

		/**
		 * Render a section heading (title + optional meta + description).
		 *
		 * @param string $title Heading title.
		 * @param string $meta  Optional inline meta shown next to the title.
		 * @param string $desc  Description line under the title.
		 * @return void
		 */
		private static function render_section_heading( $title, $meta, $desc ) {
			?>
			<div style="display:flex; align-items:baseline; gap:10px; margin:0 0 4px;">
				<h2 style="font-size:17px; font-weight:600; color:#1d2327; margin:0;"><?php echo esc_html( $title ); ?></h2>
				<?php if ( '' !== $meta ) : ?>
					<span style="font-size:13px; color:#646970;"><?php echo esc_html( $meta ); ?></span>
				<?php endif; ?>
			</div>
			<p style="font-size:13px; color:#646970; margin:0 0 16px;"><?php echo esc_html( $desc ); ?></p>
			<?php
		}

		/**
		 * Render a plugin / skill card.
		 *
		 * @param array  $item       Card data (ini, bg, fg, name, host, desc, url).
		 * @param array  $badge      Optional badge data (label, fg, bg, border, uppercase).
		 * @param string $link_label Link text.
		 * @param bool   $mono       Whether the title uses a monospace font (skills).
		 * @return void
		 */
		private static function render_plugin_card( $item, $badge, $link_label, $mono = false ) {
			$name_style = 'font-size:' . ( $mono ? '14px' : '14.5px' ) . '; font-weight:600; color:#1d2327; margin:1px 0; line-height:1.3;';
			if ( $mono ) {
				$name_style .= ' font-family:ui-monospace,SFMono-Regular,Menlo,monospace;';
			}
			$icon_style = 'width:40px; height:40px; flex:none; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700; letter-spacing:-.5px; background:' . $item['bg'] . '; color:' . $item['fg'] . ';';
			?>
			<div class="plug-card" style="background:#fff; border:1px solid #e0e0e2; border-radius:8px; padding:18px; display:flex; flex-direction:column;">
				<div style="display:flex; gap:13px; align-items:flex-start;">
					<div style="<?php echo esc_attr( $icon_style ); ?>"><?php echo esc_html( $item['ini'] ); ?></div>
					<div style="flex:1; min-width:0;">
						<div style="display:flex; align-items:center; gap:7px; flex-wrap:wrap;">
							<h4 style="<?php echo esc_attr( $name_style ); ?>"><?php echo esc_html( $item['name'] ); ?></h4>
							<?php self::render_badge( $badge ); ?>
						</div>
						<span style="font-size:11.5px; color:#8c8f94;"><?php echo esc_html( $item['host'] ); ?></span>
					</div>
				</div>
				<p style="font-size:13px; line-height:1.55; color:#50575e; margin:12px 0 16px; flex:1;"><?php echo esc_html( $item['desc'] ); ?></p>
				<a class="plug-link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener" style="display:inline-flex; align-items:center; gap:5px; align-self:flex-start; font-size:13px; font-weight:500; color:#2271b1; text-decoration:none;"><?php echo esc_html( $link_label ); ?> <span style="font-size:15px; line-height:1;">&rarr;</span></a>
			</div>
			<?php
		}

		/**
		 * Render a badge pill next to a card title.
		 *
		 * @param array $badge Badge data (label, fg, bg, border, uppercase).
		 * @return void
		 */
		private static function render_badge( $badge ) {
			if ( empty( $badge['label'] ) ) {
				return;
			}
			$style = 'font-size:10.5px; font-weight:600; color:' . $badge['fg'] . '; background:' . $badge['bg'] . '; border:1px solid ' . $badge['border'] . '; padding:1px 7px; border-radius:9px;';
			if ( ! empty( $badge['uppercase'] ) ) {
				$style .= ' text-transform:uppercase; letter-spacing:.3px;';
			}
			printf(
				'<span style="%s">%s</span>',
				esc_attr( $style ),
				esc_html( $badge['label'] )
			);
		}

		/**
		 * Render a "website / portal" card (whole card is a link).
		 *
		 * @param array $web Card data (ini, bg, fg, name, host, desc, url).
		 * @return void
		 */
		private static function render_web_card( $web ) {
			$icon_style = 'width:40px; height:40px; flex:none; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; letter-spacing:-.3px; background:' . $web['bg'] . '; color:' . $web['fg'] . ';';
			?>
			<a class="plug-card" href="<?php echo esc_url( $web['url'] ); ?>" target="_blank" rel="noopener" style="text-decoration:none; background:#fff; border:1px solid #e0e0e2; border-radius:8px; padding:18px; display:flex; gap:13px; align-items:flex-start;">
				<div style="<?php echo esc_attr( $icon_style ); ?>"><?php echo esc_html( $web['ini'] ); ?></div>
				<div style="flex:1; min-width:0;">
					<h4 style="font-size:14.5px; font-weight:600; color:#1d2327; margin:1px 0; line-height:1.3;"><?php echo esc_html( $web['name'] ); ?></h4>
					<span style="font-size:11.5px; color:#8c8f94;"><?php echo esc_html( $web['host'] ); ?></span>
					<p style="font-size:13px; line-height:1.55; color:#50575e; margin:9px 0 0;"><?php echo esc_html( $web['desc'] ); ?></p>
				</div>
			</a>
			<?php
		}

		/**
		 * Render a profile pill card.
		 *
		 * @param array $profile Card data (ini, bg, fg, name, host, url).
		 * @return void
		 */
		private static function render_profile_card( $profile ) {
			$icon_style = 'width:32px; height:32px; flex:none; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; background:' . $profile['bg'] . '; color:' . $profile['fg'] . ';';
			?>
			<a class="plug-card" href="<?php echo esc_url( $profile['url'] ); ?>" target="_blank" rel="noopener" style="text-decoration:none; display:inline-flex; align-items:center; gap:11px; background:#fff; border:1px solid #e0e0e2; border-radius:8px; padding:11px 16px 11px 12px;">
				<div style="<?php echo esc_attr( $icon_style ); ?>"><?php echo esc_html( $profile['ini'] ); ?></div>
				<div>
					<div style="font-size:14px; font-weight:600; color:#1d2327; line-height:1.2;"><?php echo esc_html( $profile['name'] ); ?></div>
					<div style="font-size:11.5px; color:#8c8f94;"><?php echo esc_html( $profile['host'] ); ?></div>
				</div>
			</a>
			<?php
		}

		/**
		 * Extract a clean host (without leading "www.") from a URL.
		 *
		 * @param string $url URL.
		 * @return string
		 */
		private static function host( $url ) {
			$host = wp_parse_url( $url, PHP_URL_HOST );
			if ( empty( $host ) ) {
				return '';
			}
			return preg_replace( '/^www\./', '', $host );
		}

		/**
		 * Add the computed host to every item in a list.
		 *
		 * @param array $items List of cards.
		 * @return array
		 */
		private static function with_host( $items ) {
			foreach ( $items as $key => $item ) {
				$items[ $key ]['host'] = isset( $item['url'] ) ? self::host( $item['url'] ) : '';
			}
			return $items;
		}

		/**
		 * Featured macOS app data.
		 *
		 * @return array
		 */
		private static function get_mac_app() {
			$defaults = array(
				'name'         => 'PackDesk',
				'badge'        => __( 'Coming soon', 'woo-redsys-gateway-light' ),
				'logo'         => defined( 'REDSYS_PLUGIN_URL' ) ? REDSYS_PLUGIN_URL . 'assets/images/packdesk-lockup.svg' : '',
				'desc'         => __( 'Manage your store orders, refunds and notifications from a native macOS app. Connect it to this store by enabling the API in "Behaviors towards the APP".', 'woo-redsys-gateway-light' ),
				'version'      => '0.12.0-beta',
				'requirements' => __( 'macOS 15 Sequoia or later', 'woo-redsys-gateway-light' ),
			);

			/**
			 * Filter the featured macOS app data shown in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $defaults Mac app data.
			 */
			return (array) apply_filters( 'redsys_lite_apps_plugins_mac_app', $defaults );
		}

		/**
		 * Free plugins data.
		 *
		 * @return array
		 */
		private static function get_free_plugins() {
			$items = array(
				array(
					'ini'  => 'BE',
					'bg'   => '#e7f0fa',
					'fg'   => '#1f5c97',
					'name' => 'Enable Block Editor For WC Products',
					'desc' => __( 'Enables the block editor (Gutenberg) to edit WooCommerce product pages.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://github.com/joseconti/gutenberg-for-wc-products',
				),
				array(
					'ini'  => 'RL',
					'bg'   => '#e3f4f1',
					'fg'   => '#1f7a6b',
					'name' => 'WooCommerce Redsys Gateway (Light)',
					'desc' => __( 'Free version of the Redsys payment gateway to accept card payments in WooCommerce.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://wordpress.org/plugins/woo-redsys-gateway-light/',
				),
				array(
					'ini'  => 'ML',
					'bg'   => '#efe9fa',
					'fg'   => '#5e3eaa',
					'name' => 'MCP Content Manager Lite',
					'desc' => __( 'Connects WordPress with AI assistants via Model Context Protocol to create and edit content.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://wordpress.org/plugins/mcp-content-manager-lite/',
				),
				array(
					'ini'  => 'AU',
					'bg'   => '#e8f3ea',
					'fg'   => '#2a7d4f',
					'name' => 'WooCommerce Autónomos',
					'desc' => __( 'Adapts WooCommerce to freelancer (autónomos) invoicing in Spain: IRPF withholding and tax fields.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://wordpress.org/plugins/autonomos/',
				),
			);

			/**
			 * Filter the free plugins listed in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $items Free plugins.
			 */
			return self::with_host( (array) apply_filters( 'redsys_lite_apps_plugins_free', $items ) );
		}

		/**
		 * Premium plugins data.
		 *
		 * @return array
		 */
		private static function get_premium_plugins() {
			$items = array(
				array(
					'ini'  => 'LM',
					'bg'   => '#e7eafa',
					'fg'   => '#3a4aa8',
					'name' => 'Easy License Manager for WooCommerce',
					'desc' => __( 'Generate and control software licenses with activations and updates for your products.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://easylicensemanager.joseconti.com/',
				),
				array(
					'ini'  => 'AS',
					'bg'   => '#fbeee2',
					'fg'   => '#a85d1f',
					'name' => 'Advanced Subscriptions for WooCommerce',
					'desc' => __( 'Advanced subscriptions and recurring payments, with flexible renewal and billing rules.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://advancedsubscriptionswc.com/',
				),
				array(
					'ini'  => 'MP',
					'bg'   => '#efe9fa',
					'fg'   => '#5e3eaa',
					'name' => 'MCP Content Manager Premium',
					'desc' => __( 'AI content management through MCP: professional version with all the tools.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://mcmwp.com/',
				),
				array(
					'ini'  => 'RG',
					'bg'   => '#fbe6e8',
					'fg'   => '#b02333',
					'name' => 'WooCommerce Redsys Gateway',
					'desc' => __( 'Complete Redsys gateway: Bizum, tokenization, subscriptions, Apple Pay, Google Pay and more.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/plugin-woocommerce-redsys-gateway/',
				),
				array(
					'ini'  => 'AP',
					'bg'   => '#e8f3ea',
					'fg'   => '#2a7d4f',
					'name' => 'WooCommerce Autónomos Premium',
					'desc' => __( 'Complete tax invoicing for freelancers and SMEs: invoice series, IRPF, equivalence surcharge.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/woocommerce-autonomos-premium/',
				),
				array(
					'ini'  => 'FS',
					'bg'   => '#e3f1f6',
					'fg'   => '#1f6f93',
					'name' => 'FacturaScripts Sync para WooCommerce',
					'desc' => __( 'Automatically sync orders, products and stock between WooCommerce and FacturaScripts.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/facturascripts-sync-para-woocommerce/',
				),
				array(
					'ini'  => 'PP',
					'bg'   => '#ecedef',
					'fg'   => '#4a5260',
					'name' => 'Private Products para WooCommerce',
					'desc' => __( 'Create private products and catalogs, visible only to authorized customers or roles.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/private-products-para-woocommerce/',
				),
				array(
					'ini'  => 'AI',
					'bg'   => '#efe7fb',
					'fg'   => '#6a3eb8',
					'name' => 'Smart AI Translate for WP',
					'desc' => __( 'Automatically translate your WordPress into several languages with artificial intelligence.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/smart-ai-translate-para-wp-traduce-wordpress-con-inteligencia-artificial/',
				),
				array(
					'ini'  => 'DW',
					'bg'   => '#fbf2e0',
					'fg'   => '#9a7311',
					'name' => 'DemoWP',
					'desc' => __( 'Create temporary WordPress demos and sandboxes so your customers can try your products.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/demowp-crea-demos-sandbox-temporales-en-wordpress-para-tus-productos/',
				),
				array(
					'ini'  => 'PS',
					'bg'   => '#fbe6ec',
					'fg'   => '#b02564',
					'name' => 'Redsys for PrestaShop Premium',
					'desc' => __( 'Advanced Redsys payment gateway for PrestaShop stores, with all the POS options.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/product/redsys-for-prestashop-premium/',
				),
			);

			/**
			 * Filter the premium plugins listed in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $items Premium plugins.
			 */
			return self::with_host( (array) apply_filters( 'redsys_lite_apps_plugins_premium', $items ) );
		}

		/**
		 * Websites and portals data.
		 *
		 * @return array
		 */
		private static function get_webs() {
			$items = array(
				array(
					'ini'  => 'WEB',
					'bg'   => '#e7f0fa',
					'fg'   => '#1f5c97',
					'name' => __( 'Plugins (main site)', 'woo-redsys-gateway-light' ),
					'desc' => __( 'Main site with the full plugin catalog and documentation.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/',
				),
				array(
					'ini'  => 'NGO',
					'bg'   => '#e8f3ea',
					'fg'   => '#2a7d4f',
					'name' => __( 'Nonprofit program', 'woo-redsys-gateway-light' ),
					'desc' => __( 'Premium plugins free for registered nonprofits.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://nonprofits.joseconti.com/',
				),
				array(
					'ini'  => 'SK',
					'bg'   => '#efe9fa',
					'fg'   => '#5e3eaa',
					'name' => __( 'Skills marketplace', 'woo-redsys-gateway-light' ),
					'desc' => __( 'Marketplace of skills to use with Claude.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://skills.joseconti.com/',
				),
				array(
					'ini'  => 'CAT',
					'bg'   => '#fbeee2',
					'fg'   => '#a85d1f',
					'name' => __( 'Full premium plugin catalog', 'woo-redsys-gateway-light' ),
					'desc' => __( 'Online store with all available premium plugins.', 'woo-redsys-gateway-light' ),
					'url'  => 'https://plugins.joseconti.com/tienda/',
				),
			);

			/**
			 * Filter the websites and portals listed in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $items Websites.
			 */
			return self::with_host( (array) apply_filters( 'redsys_lite_apps_plugins_webs', $items ) );
		}

		/**
		 * Skills data.
		 *
		 * @return array
		 */
		private static function get_skills() {
			$items = array(
				array(
					'ini'   => 'RF',
					'bg'    => '#e7eafa',
					'fg'    => '#3a4aa8',
					'name'  => 'refactor',
					'desc'  => __( 'Skill to refactor and reorganize code with Claude.', 'woo-redsys-gateway-light' ),
					'badge' => '',
					'url'   => 'https://skills.joseconti.com/plugin/refactor.html',
				),
				array(
					'ini'   => 'KE',
					'bg'    => '#e3f4f1',
					'fg'    => '#1f7a6b',
					'name'  => 'keel',
					'desc'  => __( 'keel skill for Claude-assisted workflows.', 'woo-redsys-gateway-light' ),
					'badge' => '',
					'url'   => 'https://skills.joseconti.com/plugin/keel.html',
				),
				array(
					'ini'   => 'GB',
					'bg'    => '#efe9fa',
					'fg'    => '#5e3eaa',
					'name'  => 'wp-woo-gutenberg-blocks',
					'desc'  => __( 'Skills bundle to build Gutenberg blocks for WordPress and WooCommerce.', 'woo-redsys-gateway-light' ),
					'badge' => __( '18 skills', 'woo-redsys-gateway-light' ),
					'url'   => 'https://skills.joseconti.com/plugin/wp-woo-gutenberg-blocks.html',
				),
				array(
					'ini'   => 'FS',
					'bg'    => '#e3f1f6',
					'fg'    => '#1f6f93',
					'name'  => 'facturascripts',
					'desc'  => __( 'Integration and automation skill for FacturaScripts.', 'woo-redsys-gateway-light' ),
					'badge' => '',
					'url'   => 'https://skills.joseconti.com/plugin/facturascripts.html',
				),
				array(
					'ini'   => 'RE',
					'bg'    => '#fbe6e8',
					'fg'    => '#b02333',
					'name'  => 'Declaración de la Renta España',
					'desc'  => __( 'Assistant to prepare the income tax return in Spain.', 'woo-redsys-gateway-light' ),
					'badge' => '',
					'url'   => 'https://skillrenta.com/',
				),
			);

			/**
			 * Filter the skills listed in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $items Skills.
			 */
			return self::with_host( (array) apply_filters( 'redsys_lite_apps_plugins_skills', $items ) );
		}

		/**
		 * Profiles data.
		 *
		 * @return array
		 */
		private static function get_profiles() {
			$items = array(
				array(
					'ini'  => 'GH',
					'bg'   => '#ecedef',
					'fg'   => '#24292f',
					'name' => 'GitHub',
					'url'  => 'https://github.com/joseconti',
				),
				array(
					'ini'  => 'GI',
					'bg'   => '#ecedef',
					'fg'   => '#4a5260',
					'name' => 'Gists',
					'url'  => 'https://gist.github.com/joseconti',
				),
				array(
					'ini'  => 'X',
					'bg'   => '#e6edf5',
					'fg'   => '#1d2327',
					'name' => 'X / Twitter',
					'url'  => 'https://twitter.com/josecontic',
				),
			);

			/**
			 * Filter the profiles listed in the section.
			 *
			 * @since 7.0.3
			 *
			 * @param array $items Profiles.
			 */
			return self::with_host( (array) apply_filters( 'redsys_lite_apps_plugins_profiles', $items ) );
		}
	}

endif;
