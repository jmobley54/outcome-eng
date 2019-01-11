<?php
/**
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */

class Email_Before_download_Downloader
{
    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }
    public function serve_file($uid)
    {

        $db = new Email_Before_Download_DB();
        $link = $db->select_link('uid', $uid);
        if ($this->expired($link)) {
            wp_die(__('This download has expired. Please fill out a new form to generate a new link.', 'email-before-download'));
        }
        if(isset($link->file)){
            $file = $link->file;
        }else{
            $file = do_shortcode("[download_data id=\"$link->selected_id\" data=\"download_link\"]");

        }
        $db->mark_downloaded($link->id);
         wp_redirect($file);
         exit();
    }

    public function expired($link)
    {
        if ($link->expire_time)
            if ($link->expire_time < time()) return true;
        return false;
    }
}