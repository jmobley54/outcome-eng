<?php
/**
 * Template displays a write panel options
 * @var string $tab_id
 * @var array $settings
 * @var OptArt\WoocommerceOnePageShopping\Classes\Services\translator $translator
 */
?>

<div id="<?php print $tab_id; ?>" class="panel woocommerce_options_panel">

    <?php if ( sizeof( $settings ) === 0 ) : ?>
        <h2><?php print $translator->get_translation( 'settings.unavailable' ) ?></h2>
    <?php endif; ?>

    <?php if ( isset( $settings['enable_ops'] ) ) : ?>
        <?php woocommerce_wp_checkbox( $settings['enable_ops'] ); ?>
    <?php endif; ?>

    <?php if ( isset( $settings['display_cart'] ) ) : ?>
        <?php woocommerce_wp_checkbox( $settings['display_cart'] ); ?>
    <?php endif; ?>

    <?php if ( isset( $settings['display_checkout'] ) ) : ?>
        <?php woocommerce_wp_checkbox( $settings['display_checkout'] ); ?>
    <?php endif; ?>
</div>