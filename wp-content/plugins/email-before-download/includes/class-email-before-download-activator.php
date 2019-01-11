<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */
class Email_Before_Download_Activator
{

    public static function activate()
    {
        $plugins = array();
        if (!is_plugin_active('download-monitor/download-monitor.php'))
            $plugins[] = "Download Monitor";
        if (!is_plugin_active('contact-form-7/wp-contact-form-7.php'))
            $plugins[] = "Contact Form 7";

        if (!empty($plugins)) {
            echo("Missing the following plugin(s):<br>");
            foreach ($plugins as $plugin) {
                echo("<strong>$plugin</strong><br>");
            }
            @trigger_error('missing dependencies', E_USER_ERROR);
        }
        global $wpdb;

            $tables = array(
                "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "ebd_item (
			  `id` int(11) NOT NULL auto_increment,
			  `download_id` varchar(128) NULL,
			  `file` varchar(255) NULL,
			  `title` varchar(255) NULL,
			  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
			  PRIMARY KEY  (`id`)
			);",
                "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "ebd_link (
             `id` mediumint(9) NOT NULL AUTO_INCREMENT,
             `item_id` int(11) NOT NULL,
             `is_downloaded` smallint(3) NOT NULL DEFAULT '0',
             `email` varchar(128) NOT NULL,
             `expire_time` bigint(11) DEFAULT NULL,
             `time_requested` bigint(11) DEFAULT NULL,
             `uid` varchar(255) NOT NULL,
             `selected_id` bigint(20) NOT NULL,
             `delivered_as` varchar(255) DEFAULT NULL,
             `is_masked` varchar(4) DEFAULT NULL,
             `is_force_download` varchar(4) DEFAULT NULL,
             UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8
			;",
                "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "ebd_posted_data (
			time_requested bigint(20),
			email VARCHAR(128) NULL,
			user_name VARCHAR(128)CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
            posted_data text(2000) NOT NULL,
			UNIQUE KEY id (time_requested)
			);",

            );
            $charset_collate = $wpdb->get_charset_collate();
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            foreach ($tables as $table) {
                dbDelta($table . $charset_collate);
            }
    }

}
