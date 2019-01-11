<div class="wrap about-wrap" id="wc-ppp-help">
    <img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/icon.png'; ?>" class="alignleft" style="width:150px; margin-right:20px; margin-bottom:20px;"/>
    <h1><?php _e( 'WooCommerce Pay Per Post Help' ); ?></h1>
    <p class="about-text">If you have any questions or want to suggest a feature request please reach out to me at mattpram@gmail.com. If you really dig this plugin consider leaving me a review!</p>

    <div class="wc-ppp-settings-wrap">
        <h2 class="nav-tab-wrapper" id="wc-ppp-help-nav-tabs">
            <a class="nav-tab nav-tab-active" href="#wc-ppp-help-getting-started-tab">Getting Started</a>
            <a class="nav-tab" href="#wc-ppp-help-shortcode-tab">Shortcodes</a>
            <a class="nav-tab" href="#wc-ppp-help-shortcode-templates-tab">Shortcode Templates</a>
            <a class="nav-tab" href="#wc-ppp-help-template-tags-tab">Template Tags</a>
            <a class="nav-tab" href="#wc-ppp-help-filters-tab">Filters</a>
        </h2>
        <div id="poststuff">

            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
					<?php require_once plugin_dir_path( __FILE__ ) . 'help-getting-started.php'; ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'help-shortcodes.php'; ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'help-shortcodes-templates.php'; ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'help-template-tags.php'; ?>
					<?php require_once plugin_dir_path( __FILE__ ) . 'help-filters.php'; ?>
                </div>
				<?php require_once plugin_dir_path( __FILE__ ) . 'settings-sidebar.php'; ?>
            </div>

        </div>
    </div>
</div>