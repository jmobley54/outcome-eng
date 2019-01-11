<?php
/**
 * Displaying the admin settings
 */
?>

<div class="wrap">
    <div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
    <h2><?php print $translator->get_translation( 'ops.settings' ); ?></h2>
    <?php settings_errors(); ?>
	<h2 class="nav-tab-wrapper">
		<?php print $tabs; ?>
	</h2>
    <form action="options.php" method="post">
        <?php settings_fields( $plugin_identifier ); ?>
        <?php print $sections; ?>
        <p class="submit">
            <input type="submit" value="<?php print $translator->get_translation( 'save.changes' ) ?>" class="button-primary" name="Submit">
        </p>
    </form>
</div>