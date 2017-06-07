<?php
	if ( !class_exists( 'WC_Payment_Gateway' ) ) return;

	function redsys_about_page() {
		$link_shop    = '<a href="https://woocommerce.com/products/redsys-gateway/" target="_blank">WooCommerce.com</a>';
		$link_support = '<a href="https://docs.woocommerce.com/document/redsys-servired-sermepa-gateway/" target="_blank">' . __('Here', 'woo-redsys-gateway-light') . '</a>';
	?>
		<div class="wrap about-wrap-redsys">
		<h1><?php printf( __( 'Welcome to WooCommerce Redsys', 'woo-redsys-gateway-light' ), REDSYS_WOOCOMMERCE_VERSION ); ?></h1>

		<p class="about-text"><?php printf( __( 'Thank you for install the latest version! WooCommerce Redsys Gateway light, democratizing ecommerce.', 'woo-redsys-gateway-light' ), REDSYS_WOOCOMMERCE_VERSION ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s', 'woo-redsys-gateway-light' ), REDSYS_WOOCOMMERCE_VERSION ); ?></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New', 'woo-redsys-gateway-light' ); ?></a>
		</h2>

		<div class="feature-section one-col">
			<div class="col">
				<h2><?php _e( 'The official WooCommerce extension', 'woo-redsys-gateway-light' ); ?></h2>
				<p class="lead-description"><?php _e( 'This gateway is the light version of official WooCommerce Redsys plugin at WooCommerce.com', 'woo-redsys-gateway-light' ); ?></p>
				<p><?php printf( __( 'This WooCommerce extension has all you need for start selling through Redsys. It is full compatible with WPML. If you need more power, you can buy the premium extension at %s.', 'woo-redsys-gateway-light' ), $link_shop ); ?></p>
				<p><?php printf( __( 'With the premium version you get many important features like two terminals, error actions, Sequential Invoice Number, invoice export, etc. See all features %s', 'woo-redsys-gateway-light' ), $link_support ); ?></p>
			</div>
		</div>
		<div class="woocommerce-message inline">
			<p>
				<center><a href="https://woocommerce.com/products/redsys-gateway/" target="_blank" rel="noopener"><img class="aligncenter wp-image-211 size-full" title="<?php _e('Get the pro version at WooCommerce.com', 'woo-redsys-gateway-light' ) ?>" src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/banner.png' ?>" alt="<?php _e('Get the pro version at WooCommerce.com', 'woo-redsys-gateway-light' ) ?>" width="800" height="150" /></a></center>
			</p>
		</div>
		<p>&nbsp;</p>
		<hr />

		<h2><?php _e( 'Features of light version', 'woo-redsys-gateway-light' ); ?></h2>

		<!-- <div class="headline-feature one-col">
			<div class="col">
				<picture>

					<source media="(min-width: 1050px)" srcset="https://cldup.com/-951havc3C.png" />

					<source media="(min-width: 601px)" srcset="https://cldup.com/60ktdYzv0l.png" />

					<img src="https://cldup.com/mwvU0Zi5wW.png" alt="" />
				</picture>
			</div>
		</div> -->
		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Select a Language', 'woo-redsys-gateway-light' ); ?></h3>
				<p><?php _e( 'Select the Redsys Gateway Language from settings. You can select the Redsys Gateway language from settings, so if you have several shops, you can select a different language for each one.', 'woo-redsys-gateway-light' ); ?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'WMPL Ready', 'woo-redsys-gateway-light' ); ?></h3>
				<p><?php _e( 'This extension is WPML ready. If you use WPML, Redsys Gateway will be shown in the customer language, don&#8217;t lose orders because the customer don&#8217;t understand the predefined language gateway. If you use WPML, the language selection in settings is not needed', 'woo-redsys-gateway-light' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'SNI Compatibility', 'woo-redsys-gateway-light' ); ?></h3>
				<p><?php _e( 'This extension is SNI Ready, so will work with Let&#8217;s Encrypt certificate. With SNI Compatibility, you orders will not market as paid after customer pais at Redsys because Redsys will not be able to communicate with your website.', 'woo-redsys-gateway-light' );?></p>
			</div>
			<div class="col">
				<h3><?php _e( 'Always compatible', 'woo-redsys-gateway-light' ); ?></h3>
				<p><?php _e( 'Because this is the official WooCommerce Extension, always will be compatible with the latest WooCommerce version. All official extension are constantly audited by WooCommerce team ensuring compatibility with the latest WooCommerce version', 'woo-redsys-gateway-light' ); ?></p>
			</div>
		</div>

		<hr />

		<h2><?php _e( 'Features of Premium Version', 'woo-redsys-gateway-light' ); ?></h2>

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Video demostration' ); ?></h3>
				<p><?php _e( 'You can watch premium features in action in this video. If you watch this video, you can evaluate the features of the premium version and in this way, assess if you are interested. After viewing it, you can evaluate if you are interested in buying it.', 'woo-redsys-gateway-light' ); ?></p>
			</div>
			<div class="col">
				<iframe src="https://player.vimeo.com/video/129766213" width="490" height="306" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Second terminal', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'With the Second terminal, you can add a security check for expensive orders, and a direct pay (without security check) for cheapest orders.', 'woo-redsys-gateway-light' ); ?></p> 				<p><?php _e( 'With two terminals you can have the right balance between security and risk. You will not lose orders on cheap purchases, and you will protect yourself from expensive order frauds', 'woo-redsys-gateway-light' ); ?></p>
				<p>&nbsp;</p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/second-terminal.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Sequential Invoice Number', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'Did you know that WordPress has a thriving offline community with groups meeting regularly in more than 400 cities around the world? WordPress now draws your attention to the events that help you continue improving your WordPress skills, meet friends, and, of course, publish!' ); ?></p>

				<p><?php _e( 'This is quickly becoming one of our favorite features. While you are in the dashboard (because you&#8217;re running updates and writing posts, right?) all upcoming WordCamps and WordPress Meetups &mdash; local to you &mdash; will be displayed.' ); ?>

				<p><?php _e( 'Being part of the community can help you improve your WordPress skills and network with people you wouldn&#8217;t otherwise meet. Now you can easily find your local events just by logging in to your dashboard and looking at the new Events and News dashboard widget.' ); ?>
			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/sequential.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Select error action', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'Did you know that WordPress has a thriving offline community with groups meeting regularly in more than 400 cities around the world? WordPress now draws your attention to the events that help you continue improving your WordPress skills, meet friends, and, of course, publish!' ); ?></p>

				<p><?php _e( 'This is quickly becoming one of our favorite features. While you are in the dashboard (because you&#8217;re running updates and writing posts, right?) all upcoming WordCamps and WordPress Meetups &mdash; local to you &mdash; will be displayed.' ); ?>

				<p><?php _e( 'Being part of the community can help you improve your WordPress skills and network with people you wouldn&#8217;t otherwise meet. Now you can easily find your local events just by logging in to your dashboard and looking at the new Events and News dashboard widget.' ); ?>
			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/select-error.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Expor Orders to CSV', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'Did you know that WordPress has a thriving offline community with groups meeting regularly in more than 400 cities around the world? WordPress now draws your attention to the events that help you continue improving your WordPress skills, meet friends, and, of course, publish!' ); ?></p>

				<p><?php _e( 'This is quickly becoming one of our favorite features. While you are in the dashboard (because you&#8217;re running updates and writing posts, right?) all upcoming WordCamps and WordPress Meetups &mdash; local to you &mdash; will be displayed.' ); ?>

				<p><?php _e( 'Being part of the community can help you improve your WordPress skills and network with people you wouldn&#8217;t otherwise meet. Now you can easily find your local events just by logging in to your dashboard and looking at the new Events and News dashboard widget.' ); ?>
			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/order-export.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="changelog">
			<h2><?php
				printf(
					/* translators: %s: smiling face with smiling eyes emoji */
					__( 'Even More Developer Happiness %s' ),
					'&#x1F60A'
				);
			?></h2>

			<div class="under-the-hood three-col">
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/17/cleaner-headings-in-the-admin-screens/"><?php _e( 'More Accessible Admin Panel Headings' ); ?></a></h3>
					<p><?php _e( 'New CSS rules mean extraneous content (like &ldquo;Add New&rdquo; links) no longer need to be included in admin-area headings. These panel headings improve the experience for people using assistive technologies.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/removal-of-core-embedding-support-for-wmv-and-wma-file-formats/"><?php _e( 'Removal of Core Support for WMV and WMA Files' ); ?></a></h3>
					<p><?php _e( 'As fewer and fewer browsers support Silverlight, file formats which require the presence of the Silverlight plugin are being removed from core support. Files will still display as a download link, but will no longer be embedded automatically.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/22/multisite-focused-changes-in-4-8/"><?php _e( 'Multisite Updates' ); ?></a></h3>
					<p><?php _e( 'New capabilities have been introduced to 4.8 with an eye towards removing calls to <code>is_super_admin()</code>. Additionally, new hooks and tweaks to more granularly control site and user counts per network have been added.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/23/addition-of-tinymce-to-the-text-widget/"><?php _e( 'Text-Editor JavaScript API' ); ?></a></h3>
					<p><?php _e( 'With the addition of TinyMCE to the text widget in 4.8 comes a new JavaScript API for instantiating the editor after page load. This can be used to add an editor instance to any text area, and customize it with buttons and functions. Great for plugin authors!' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/26/media-widgets-for-images-video-and-audio/"><?php _e( 'Media Widgets API' ); ?></a></h3>
					<p><?php _e( 'The introduction of a new base media widget REST API schema to 4.8 opens up possibilities for even more media widgets (like galleries or playlists) in the future. The three new media widgets are powered by a shared base class that covers most of the interactions with the media modal. That class also makes it easier to create new media widgets and paves the way for more to come.' ); ?></p>
				</div>
				<div class="col">
					<h3><a href="https://make.wordpress.org/core/2017/05/16/customizer-sidebar-width-is-now-variable/"><?php _e( 'Customizer Width Variable' ); ?></a></h3>
					<p><?php _e( 'Rejoice! New responsive breakpoints have been added to the customizer sidebar to make it wider on high-resolution screens. Customizer controls should use percentage-based widths instead of pixels.' ); ?></p>
				</div>
			</div>
		</div>

		<hr />

		<div class="return-to-dashboard">
			<?php if ( current_user_can( 'update_core' ) && isset( $_GET['updated'] ) ) : ?>
				<a href="<?php echo esc_url( self_admin_url( 'update-core.php' ) ); ?>">
					<?php is_multisite() ? _e( 'Return to Updates' ) : _e( 'Return to Dashboard &rarr; Updates' ); ?>
				</a> |
			<?php endif; ?>
			<a href="<?php echo esc_url( self_admin_url() ); ?>"><?php is_blog_admin() ? _e( 'Go to Dashboard &rarr; Home' ) : _e( 'Go to Dashboard' ); ?></a>
		</div>

	</div>

	<?php	}