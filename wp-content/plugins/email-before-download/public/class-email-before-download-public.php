<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks.
 *
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/public
 * @author     M & S Consulting
 */
class Email_Before_Download_Public
{

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/email-before-download-public.css', array(), $this->version, 'all');

    }

    public function enqueue_scripts()
    {
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/email-before-download-public.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name, 'ebd_inline', array('ajaxurl' => admin_url('admin-ajax.php'), 'ajax_nonce' => wp_create_nonce('ebd'),));
    }
    public function shortcode_cleanup($content)
    {
        $shortcodes = array(
            'email-download', 'emailreq'
        );
        foreach ($shortcodes as $shortcode){
            if (strpos($content, $shortcode))
            {
                $search = array(
                    '"','’','‘','“','”','"');

                $replace = "'";

                $content =  str_replace($search, $replace, $content);
            }
        }

        return $content;
    }

    public function ebd_ajax()
    {
        check_ajax_referer('ebd', 'security');
        $permalink = get_option('permalink_structure');
        if ($permalink != "") {
            $spacer = "?uid=";
        } else {
            $spacer = "&uid=";

        }
        $postData = sanitize_post($_POST);
        $data = array();
        $htmlBefore = get_option('email_before_download_html_before');
        $htmlAfter = get_option('email_before_download_html_after');
        $linkCSS = get_option('email_before_download_link_css');
        $db = new Email_Before_Download_DB();
        $links = array();
        $linksHTML = "";
        if(!is_array($postData['downloads'])){
            $downloads = array($postData['downloads']);
        }else {
            $downloads = $postData['downloads'];
        }
        if(!is_array($postData['settings'])){
            $postSettings = array($postData['settings']);
        }else {
            $postSettings = $postData['settings'];
        }
        foreach($downloads as $download){
            $tmp = explode('|', $download);
            $links[$tmp[0]]['title'] = $tmp[1];
        }
        foreach ($postSettings as $item){
            $tmp = explode('|',$item);
            $settings[$tmp[0]] = $tmp[1];
        }
        if(strtolower($settings['delivered_as']) == 'send email') {
            echo json_encode('email sent');
            exit();
        }
        $DBLinks = $db->get_ajax_links($postData['email']);
        if((count($DBLinks) == 1 ) && ( isset($settings['force_download']) ) ){
            $data['download'] = 'yes';
            $data['url'] = do_shortcode("[download_data id=\"".$DBLinks[0]->selected_id."\" data=\"download_link\"]").$spacer.$DBLinks[0]->uid;
            echo json_encode($data);
            exit();
        }

        foreach ($DBLinks as $link){

            $shortCode = do_shortcode("[download_data id=\"$link->selected_id\" data=\"download_link\"]").$spacer.$link->uid;
            $linksHTML .= "<a class=\"ebd_link $linkCSS\" href=\"".$shortCode."\" target=\"" .$settings['link_format']. "\" >".$links[$link->selected_id]['title']."</a></br>" ;
        }
        if(isset($links)){
            $data['html'] =  "<div class=\"ebd_results\">".$htmlBefore.$linksHTML.$htmlAfter."</div>";
        }else{
            $data['error'] =  'no data found';
        }
            echo json_encode($data);
        exit();
    }
public function record($allowed, $download){
        $permalink = get_option('permalink_structure');
        if($permalink != ""){
            $downloadID = $download->post->ID;
        }else {
            $downloadID = $_GET['download'];

        }
        $db = new Email_Before_Download_DB();
        
            if(isset($_GET['uid'])) {
                $link = $db->select_link('uid', $_GET['uid']);
                if (!$link)
                    wp_die(__('Invalid UID Please fill out a new form to generate a new link.', 'email-before-download'));

                if ($this->expired($link))
                    wp_die(__('This download has expired. Please fill out a new form to generate a new link.', 'email-before-download'));

                $db->mark_downloaded($link->id);
            }


    return $allowed;
}
    public function expired($link)
    {
        if ($link->expire_time)
            if ($link->expire_time < time()) return true;
        return false;
    }
}
