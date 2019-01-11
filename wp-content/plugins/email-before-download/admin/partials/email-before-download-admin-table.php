<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing log views of the plugin.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/admin/partials
 * @author     M & S Consulting
 */
$message = "";
if( isset($_SESSION['success']) ) {
    $message = "<div id=\"setting-error-settings_updated\" class=\"updated settings-error notice is-dismissible\">
        <p><strong>".$_SESSION['success']."</strong></p><button type=\"button\" class=\"notice-dismiss\"><span class=\"screen-reader-text\">Dismiss this notice.</span></button></div>";
    unset($_SESSION['success']);
}
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <?php echo $message ?>
    <h2><?php echo $title ?></h2>
    <div class="buttons">
        <a href="<?php echo admin_url( "admin-post.php?action=ebd.csv&table=".$wp_table->atts['table'] ); ?>" class="button button-primary">Export as CSV</a>
        <a href="<?php echo admin_url( "admin-post.php?action=ebd.purge&table=".$wp_table->atts['table'] ); ?>" class="button button-secondary" onclick="if(confirm('Are you sure you want to purge data?.')) return true; return false"><?php echo $wp_table->atts['purge_text']; ?></a>
        <a href="<?php echo admin_url( "options-general.php?page=email-before-download"); ?>" class="right button button-primary">Back to Settings</a>
    </div>
    <?php $wp_table->display();  ?>
</div>