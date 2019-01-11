<?php wp_nonce_field($this->plugin_name . '_nonce', $this->plugin_name . '_nonce'); ?>
<!-- Start tabs -->
<ul class="wcppp-tab-bar">
    <li class="wcppp-tab-active"><a href="#wc-ppp-product-info"><?php esc_attr_e( 'Product Information', 'wc_pay_per_post' ); ?></a></li>
    <li><?php if($delay_restriction_enable): ?> <span class="dashicons dashicons-yes" title="Enabled" style="color:green;"></span> <?php endif; ?><a href="#wc-ppp-delay-restriction"><?php esc_attr_e( 'Delay Restriction', 'wc_pay_per_post' ); ?></a></li>
    <li><?php if($page_view_restriction_enable): ?> <span class="dashicons dashicons-yes" title="Enabled" style="color:green;"></span> <?php endif; ?><a href="#wc-ppp-page-view-restriction"><?php esc_attr_e( 'Page View Restriction', 'wc_pay_per_post' ); ?></a></li>
    <li><?php if($expire_restriction_enable): ?> <span class="dashicons dashicons-yes" title="Enabled" style="color:green;"></span> <?php endif; ?><a href="#wc-ppp-expiry-restriction"><?php esc_attr_e( 'Expiry Restriction', 'wc_pay_per_post' ); ?></a></li>
    <li><a href="#wc-ppp-options"><?php esc_attr_e( 'Options', 'wc_pay_per_post' ); ?></a></li>
</ul>
<div class="wcppp-tab-panel" id="wc-ppp-product-info">
    <?php require 'meta-box-product-info.php'; ?>
</div>

<div class="wcppp-tab-panel" id="wc-ppp-delay-restriction" style="display: none;">
	<?php require 'meta-box-delay-restriction.php'; ?>
</div>
<div class="wcppp-tab-panel" id="wc-ppp-page-view-restriction" style="display: none;">
	<?php require 'meta-box-page-view-restriction.php'; ?>

</div>
<div class="wcppp-tab-panel" id="wc-ppp-expiry-restriction" style="display: none;">
	<?php require 'meta-box-expiry-restriction.php'; ?>
</div>
<div class="wcppp-tab-panel" id="wc-ppp-options" style="display: none;">
	<?php require 'meta-box-options.php'; ?>
</div>