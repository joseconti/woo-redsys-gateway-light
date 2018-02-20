<?php
	if( !defined('ABSPATH') ){ exit; }

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
				<p><?php printf( __( 'With the premium version you get many important features like tokenization, refund, two terminals, error actions, Sequential Invoice Number, invoice export, etc. See all features %s', 'woo-redsys-gateway-light' ), $link_support ); ?></p>
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
				<p><?php _e( 'This extension is SNI Ready, so will work with Let&#8217;s Encrypt certificate. Without SNI Compatibility, you orders will not market as paid after customer pais at Redsys because Redsys will not be able to communicate with your website.', 'woo-redsys-gateway-light' );?></p>
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
				<h3><?php _e( 'Refund from Order', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'With Refund, you can refund an Order from admin, you dont need to go to Redsys.', 'woo-redsys-gateway-light' ); ?></p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/refund.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Tokenization', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'With Tokenization, your customers can Pay with one click, after first order', 'woo-redsys-gateway-light' ); ?></p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/tokenization.png' ?>" alt="" />
			</div>
		</div>

		<hr />

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

				<p><?php _e( 'With the Second terminal, you can add a security check for expensive orders, and a direct pay (without security check) for cheapest orders.', 'woo-redsys-gateway-light' ); ?></p>

				<p><?php _e( 'With two terminals you can have the right balance between security and risk. You will not lose orders on cheap purchases, and you will protect yourself from expensive order frauds', 'woo-redsys-gateway-light' ); ?></p>

				<p>&nbsp;</p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/second-terminal.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Sequential Invoice Number', 'woo-redsys-gateway-light', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'Get Sequential Invoice Number with the Pro version. Sequential Invoice Number is mandatory for Spain.', 'woo-redsys-gateway-light' ); ?></p>

				<p><?php _e( 'With the Pro version, you will get Sequential Invoice Number so all your payed orders will have a sequential number, complying with the Spanish public finances.', 'woo-redsys-gateway-light' ); ?></p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/sequential.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Select error action', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'With the select error action, you can set what happen when a customer make an error typing his credit card.', 'woo-redsys-gateway-light' ); ?></p>

				<p><?php _e( 'By default, the order is cancelled, but with this option you can redirect the customer to the checkout page without cancelling the order, so You get a new opportunity for get a conversion!.', 'woo-redsys-gateway-light' ); ?></p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/select-error.png' ?>" alt="" />
			</div>
		</div>

		<hr />

		<div class="feature-section two-col">
			<div class="col">
				<h3><?php _e( 'Export Orders to CSV', 'woo-redsys-gateway-light', 'woo-redsys-gateway-light' ); ?></h3>

				<p><?php _e( 'Export Orders to CSV for has all needed data. With this feature, you can easily get all the data you need.', 'woo-redsys-gateway-light' ); ?></p>

				<p><?php _e( 'With Export Orders to CSV, you will download a CSV with all your orders between two dates, so you will be able to import that CSV to your contatibility software or in an Excel or similar to Excel.', 'woo-redsys-gateway-light' ); ?></p>

			</div>
			<div class="col">
				<img src="<?php echo REDSYS_PLUGIN_URL . 'assets/images/order-export.png' ?>" alt="" />
			</div>
		</div>

		<hr />

	</div>

	<?php	}