<div id="postbox-container-1" class="postbox-container">

	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial()  ) : ?>
        <a href="<?php echo wcppp_freemius()->get_upgrade_url(); ?>"><img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/upgrade.png'; ?>" class="image-50" style="width:80%" /></a>
        <div class="postbox">
            <h2 class="hndle" style="background-color:#087891; color:white;"><?php esc_attr_e( 'Upgrade to Premium Version', 'wc_pay_per_post' ); ?></h2>

            <div class="inside">
                <p><?php esc_attr_e( 'The premium version has a ton of great features including page view expiration and time based expiration!', 'wc_pay_per_post' ); ?></p>

                <a href="<?php echo wcppp_freemius()->get_upgrade_url(); ?>" class="button">Upgrade Today</a>

            </div>

        </div>
	<?php endif; ?>

    <div class="postbox">
        <h2 class="hndle"><?php esc_attr_e( 'Have Questions? Request a Feature?', 'wc_pay_per_post' ); ?></h2>

        <div class="inside">
            <p><?php esc_attr_e( 'If you have any questions or want to suggest a feature request please reach out to me at mattpram@gmail.com.  If you really dig this plugin consider leaving me a review!', 'wc_pay_per_post' ); ?></p>

            <h3><?php esc_attr_e( 'Additional Help', 'wc_pay_per_post' ); ?></h3>
            <ul>
                <li><a href="/wp-admin/admin.php?page=wc_pay_per_post-help#wc-ppp-help-getting-started-tab"><?php esc_attr_e( 'Getting Started', 'wc_pay_per_post' ); ?></a></li>
                <li><a href="/wp-admin/admin.php?page=wc_pay_per_post-help#wc-ppp-help-shortcode-tab"><?php esc_attr_e( 'Shortcodes', 'wc_pay_per_post' ); ?></a></li>
                <li><a href="/wp-admin/admin.php?page=wc_pay_per_post-help#wc-ppp-help-shortcode-templates-tab"><?php esc_attr_e( 'Shortcode Templates', 'wc_pay_per_post' ); ?></a></li>
                <li><a href="/wp-admin/admin.php?page=wc_pay_per_post-help#wc-ppp-help-template-tags-tab"><?php esc_attr_e( 'Template Tags', 'wc_pay_per_post' ); ?></a></li>
                <li><a href="/wp-admin/admin.php?page=wc_pay_per_post-help#wc-ppp-help-filters-tab"><?php esc_attr_e( 'Filters', 'wc_pay_per_post' ); ?></a></li>
            </ul>

        </div>

    </div>


</div>