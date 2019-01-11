<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/admin/partials
 * @author     M & S Consulting
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <div class="buttons">
        <a href="<?php echo admin_url( "options-general.php?page=email-before-download-links" ); ?>" class="button button-primary">
            <?php _e('View Download Links','email-before-download'); ?></a>
        <a href="<?php echo admin_url( "options-general.php?page=email-before-download-logs" ); ?>" class="button button-primary">
            <?php _e('View Submission Logs','email-before-download'); ?></a>
    </div>
    <div style="float:right">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
        <input type="hidden" name="cmd" value="_s-xclick" />
        <input type="hidden" name="hosted_button_id" value="47FLSBA363KAU" />
        <input type="image" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" alt="PayPal - The safer, easier way to pay online!" />
        <img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" />
    </form>
    </div>
    <form action="options.php" method="post">
        <?php
        settings_fields( $this->plugin_name );
        do_settings_sections( $this->plugin_name );
        submit_button();
        ?>
    </form>
</div>