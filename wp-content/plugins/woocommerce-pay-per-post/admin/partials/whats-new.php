<div class="wrap about-wrap">
	<img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>" class="alignleft"
		 style="width:150px; margin-right:20px; margin-bottom:20px;"/>
	<h1><?php esc_html_e( 'WooCommerce Pay Per Post', 'wc_pay_per_post' ); ?> (<a
				href="#changelog"><?php echo WC_PPP_PLUGIN_VERSION; ?></a>)</h1>

	<div class="about-text">
		<?php esc_html_e( 'Version 2.1 is FINALLY here and has been completely rewritten from the ground up!  Over 400 hours have been poured into this new version.', 'wc_pay_per_post' ); ?>
	</div>

	<hr style="clear:both;">

	<?php if ( 'true' === $needs_upgrade ) : ?>
		<div class="wc-ppp-upgrade">
			<h3><?php esc_html_e( 'Your database needs to be upgraded as you have ', 'wc_pay_per_post' ); ?><?php echo $old_products->post_count; ?><?php esc_html_e( ' products that use an outdated version of this plugin!', 'wc_pay_per_post' ); ?></h3>
			<p><?php esc_html_e( 'It is', 'wc_pay_per_post' ); ?>
				<strong><?php esc_html_e( 'STRONGLY', 'wc_pay_per_post' ); ?></strong> <?php esc_html_e( 'recommended that you backup your database before doing any upgrades.', 'wc_pay_per_post' ); ?>
			</p>
			<form id="wc-ppp-upgrade" action="" method="post">
				<?php wp_nonce_field( 'wc_ppp_upgrade', 'wc_ppp_upgrade_nonce' ); ?>

				<ul>
					<?php
					while ( $old_products->have_posts() ) :
						$old_products->the_post();
						?>
						<li>[<?php the_ID(); ?>] - <a
									href="<?php echo admin_url(); ?>post.php?post=<?php echo get_the_ID(); ?>&action=edit"><?php the_title(); ?></a>
						</li>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				</ul>
				<input type="submit" name="wc-ppp-upgrade-btn" class="wc-ppp-upgrade-btn button action"
					   value="Upgrade Now">
			</form>

		</div>
	<?php endif; ?>

	<?php if ( isset( $_GET['upgrade_complete'] ) && $_GET['upgrade_complete'] === 'true' ) : ?>
		<div class="wc-ppp-upgrade success">
			<h3><?php esc_html_e( 'Awesome, the database upgrade was a success', 'wc_pay_per_post' ); ?></h3>
		</div>
	<?php endif; ?>
	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial() ) : ?>
		<a href="<?php echo wcppp_freemius()->get_upgrade_url(); ?>"><img
					src="<?php echo plugin_dir_url( __DIR__ ) . 'img/upgrade.png'; ?>"
					style="position:fixed; right:0; bottom:0; margin-right:20px; margin-top:20px;"/></a>
	<?php else : ?>
	<div class="wc-ppp-whats-new-wrap">
		<?php endif; ?>
		<h2><?php esc_html_e( 'Introducing awesome Premium features!', 'wc_pay_per_post' ); ?></h2>

		<h3><?php esc_html_e( 'Multiple protection types', 'wc_pay_per_post' ); ?></h3>
		<img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/videos/protection-types.gif'; ?>" width="600">

		<h3><?php esc_html_e( 'Customizable per page purchase messages', 'wc_pay_per_post' ); ?></h3>
		<img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/videos/override-oops.gif'; ?>" width="600">

		<h3><?php esc_html_e( 'More powerful / feature rich shortcodes', 'wc_pay_per_post' ); ?></h3>
		<img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/videos/shortcodes.gif'; ?>" width="600">


		<hr>
		<div class="wc-ppp-changelog">
			<h2><?php esc_html_e( 'Changelog', 'wc_pay_per_post' ); ?></h2>

			<div class="feature-section images-stagger-right" id="changelog">
                <h4><?php esc_html_e( 'Version 2.1.13', 'wc_pay_per_post' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'NEW FEATURE - Added in two new filters wc_pay_per_post_all_product_args and wc_pay_per_post_virtual_product_args', 'wc_pay_per_post' ); ?></li>
                    <li><?php esc_html_e( 'UPDATE - Updated the virtual product filter to adhere to new WooCommerce meta values', 'wc_pay_per_post' ); ?></li>
                    <li><?php esc_html_e( 'UPDATE - Addressed more pages to make them translatable', 'wc_pay_per_post' ); ?></li>
                    <li><?php esc_html_e( 'BUG FIX - Corrected PHP warning message for invalid argument when using implode()', 'wc_pay_per_post' ); ?></li>
                </ul>
                <h4><?php esc_html_e( 'Version 2.1.12', 'wc_pay_per_post' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'BUG FIX - Corrected issue with debug code displaying on protected products', 'wc_pay_per_post' ); ?></li>
                </ul>
                <h4><?php esc_html_e( 'Version 2.1.11', 'wc_pay_per_post' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'BUG FIX - Fixed issue with comments being displayed through entire site instead of just on protected posts', 'wc_pay_per_post' ); ?></li>
                </ul>
                <h4><?php esc_html_e( 'Version 2.1.10', 'wc_pay_per_post' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'BUG FIX - Fixed issue where if multiple products were associated with post it would only look for the first product', 'wc_pay_per_post' ); ?></li>
                </ul>
                <h4><?php esc_html_e( 'Version 2.1.9', 'wc_pay_per_post' ); ?></h4>
                <ul>
                    <li><?php esc_html_e( 'Updated composer to default to php 5.6 instead of php 7', 'wc_pay_per_post' ); ?></li>
                </ul>

                <h4><?php esc_html_e( 'Version 2.1.8', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'Multiple updates to conform to WordPress Coding Standards', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.7', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'NEW PREMIUM FEATURE Ability to utilize product variations', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'NEW Shortcode replacement for {{parent_id}} which is to be used with Variations to get the main product ID', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'FIXED issue which help page tabs would not work', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.6', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'REFACTOR refactored the way the Select2 javascript library was enqueued to minimize conflicts with other plugins using Select2', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.5', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'FIXED minor bug with upgrade script that accounts for blank records on post_meta', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.4', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'FIXED bug which if upgraded would still show as FREE license sometimes.', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'UPDATED Freemius WordPress SDK to latest version', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'UPDATED POT File for translations', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'UPDATED Spanish translation', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'UPDATED French translation', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.3', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'FIXED allow for multiple product ids to be show in shortcode', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'FIXED issue with trial subscriptions which still showed upgrade to premium even though you were in premium trial', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.2', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'FIXED PHP Notice on Upgrade complete page', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'FIXED bug which did not account for custom post types in upgrade process', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'FIXED bug in shortcode that did not account for custom post types', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.1', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'FIXED bug which if toggle for allow admins to view protected content it would allow users to view protected content', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'FIXED bug which was double encoding the restricted message before saving in database.', 'wc_pay_per_post' ); ?></li>
				</ul>
				<h4><?php esc_html_e( 'Version 2.1.0', 'wc_pay_per_post' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'Initial Freemium version released.', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'Added in Delay Restriction', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'Added in Page Expiration', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'Added in Pageview Restriction', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'Added in ability to sort, order and filter shortcode', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'Added in ability to remove users pageviews', 'wc_pay_per_post' ); ?></li>
					<li><?php esc_html_e( 'And MUCH MORE!', 'wc_pay_per_post' ); ?></li>
				</ul>
			</div>
		</div>
		<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial() ) : ?>
	</div>
<?php endif; ?>
</div>
