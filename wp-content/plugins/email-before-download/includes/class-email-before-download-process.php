<?php
/**
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */

class Email_Before_Download_Process
{

    private $plugin_name;
    private $version;
    private $db;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->db = new Email_Before_Download_DB();
    }

    public function process_cf7($form_obj)
    {
        if (!isset($_POST['ebd_settings'])) {
            return $form_obj;  // make sure this only fires when there is EBD info
        }
        $post_data = $_POST;
        $user_input = array();
        $settings = $this->parse_post_array($post_data['ebd_settings']);
        $downloads = $this->parse_post_array($post_data['ebd_downloads']);
        foreach ($post_data as $key => $value) {
            if (!is_array($value))
                $user_input[$key] = $value;
        }
        $this->log($post_data, $user_input);
        $links = $this->generate_links($downloads, $settings, $user_input);

        if (($settings['delivered_as'] == 'send email') || ($settings['delivered_as'] == 'both')) {
            $this->send_email($user_input, $links, $settings);
        }
        $mail = $form_obj->prop('mail');
        $newMail = $this->reply($mail,$links);
        $form_obj->set_properties(array('mail' => $newMail));
        return $form_obj;
    }

    private function reply($mail,$links){
        $message = "The downloaded file name(s): ";
        foreach ($links as $link){
            $message .= " ".$link->title.",";
        }
        $message = rtrim($message, ',');
        if(stripos($mail['body'],"[your-message]")){
            $mail['body'] = str_replace("[your-message]", $message, $mail['body']);
            }else{
            $mail['body'] .= $message;
        }
        return $mail;
    }
    private function parse_post_array($post_array)
    {
        $new_array = array();
        $delimiter = "|";
        foreach ($post_array as $item) {
            if (strpos($item, '|')) {
                $delimiter = "|";
            }
            $tmp = explode($delimiter, $item);
            $new_array[$tmp[0]] = strtolower($tmp[1]);
        }
        return $new_array;
    }
    private function generate_email($body, $data, $links, $settings = null)
    {
        //build email to send to user
        $files = $this->email_links($links);
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $tagToReplace = "[$key]";
                $body = str_replace($tagToReplace, $value, $body);
            }
        }
        $urls = "";
        foreach ($files as $file) {
            if (count($files) == 1) {
                if(isset($settings['file'])){
                    $file['url'] = $settings['file'];
                }
                $body = str_replace('[file_url]', $file['url'], $body);
                $body = str_replace('[file_name]', $file['title'], $body);
            } else {
                $urls .= $file['url'] . "\r\n";
            }
        }
        $body = str_replace('[file_urls]', $urls, $body);


        return $body;
    }


    private function send_email($user_input, $links, $settings)
    {
        $headers = "Content-Type: text/html; charset=UTF-8\r\n";
        if(isset($settings['from_name']))  $name = $settings['from_name'] ;
        if(isset($settings['from_email']))  $from = $settings['from_email'] ;
        if( (isset($from) ) && ( is_email($from) ) ){
            $from = sanitize_email($from);
            if( (isset($name) ) && ( $name != "" ) ){
                $headers .= "From: $name <$from>\r\n";
            }else {
                $headers .= "From: $from\r\n";
            }
        }
        $headers = rtrim($headers);
        $attachments = array();
        if (count($links) > 1) {
            $template = get_option("email_before_download_multi_email");
            if (!$template) $template = "[file_urls]";
        } else {
            $template = get_option("email_before_download_single_email");
            if (!$template) $template = __("Here is the download for <a href=\"[file_url]\">[file_name]</a> that you requested.",'email-before-download');

        }
        $subject = get_option("email_before_download_subject");
        if (!$subject) $subject = __("Your Requested File(s)", 'email-before-download');
            $subject = $this->generate_email($subject, $user_input, $links);
            $body = $this->generate_email($template, $user_input, $links,$settings);
        if ( (isset($settings['attachment']) ) && ($settings['attachment'] != 'no') ) {
            $attachments = $this->email_attachments($links);
        }
        wp_mail($user_input['your-email'], $subject, $body, $headers, $attachments);
        return true;
    }


    private function generate_links($downloads, $settings, $user_input)
    {

        $links = array();
        $default_link_data = array(
            "expire_time" => "",
            "delivered_as" => "",
            "force_download" => ""
        );
        $settings = array_merge($default_link_data, $settings);

        foreach ($downloads as $id => $title) {
            $linkdata = array(
                "item_id" => $settings['item_id'],
                "selected_id" => $id,
                "is_force_download" => $settings['force_download'],
                "email" => sanitize_email($user_input['your-email']),
                "delivered_as" => $settings['delivered_as'],
                'uid' => substr(md5(uniqid(mt_rand(), true)), 0, 10),
                'time_requested' => time(),

            );

            $linkdata = array_filter($linkdata);
            $linkdata["expire_time"] = get_option('email_before_download_expire',"");
            $link = $this->db->create_link($linkdata);
            $link->title = $title;
            $links[$link->id] = $link;
        }
        return $links;
    }

    private function inline_links($links, $form_obj, $settings)
    {

        $htmlBefore = get_option('email_before_download_html_before');
        $htmlAfter  = get_option('email_before_download_html_after');
        $htmlBefore = isset($htmlBefore) ? $htmlBefore : "";
        $htmlAfter = isset($htmlAfter) ? $htmlAfter : "";
        $linkHTML = "<div>" .$htmlBefore ;
        $form_id = $settings['form_id'];
        foreach ($links as $link) {
            if(isset($settings['file'])){
                $downloadURL = $settings['file'];
            }else{
                $downloadURL = $this->link_url($link);
            }
            $linkCSS = get_option('email_before_download_link_css');
            $linkCSS = isset($linkCSS) ? $linkCSS : "";
            $target = $settings['link_format'];
            $html = "<a href=\"$downloadURL\" class=\"ebd_link $linkCSS\" target=\"$target\">";
                if(isset($link->title)){
                    $html .= $link->title;
                }else {
                    $html .= do_shortcode("[download_data id=\"$link->selected_id\" data=\"title\"]");

                }
            $html .= "</a></br>";
            $linkHTML .= $html;
        }
        $linkHTML .= $htmlAfter. "</div>";
            $additional_settings = $form_obj->prop('additional_settings');
            $hideOnSubmit = "jQuery('.ebd_input').hide();jQuery('.cf7_input').hide(); ";
            $additional_settings .= "\n" . "on_sent_ok: \" jQuery('#$form_id').append('$linkHTML'); \"";
            $form_obj->set_properties(array('additional_settings' => $additional_settings));

        return $form_obj;
    }
    private function email_links($links)
    {
        $files = array();
        foreach ($links as $link) {
            if(isset($link->title)){
                $title = $link->title;
            }else{
                $title = do_shortcode("[download_data id=\"$link->selected_id\" data=\"title\"]");

            }

                $files[] = array('title' => $title,'url' => $this->link_url($link));


        }
        return $files;
    }
    private function email_attachments($links)
    {

        $filePaths = array();
        foreach ($links as $link) {
            $fileName = do_shortcode("[download_data id=\"$link->selected_id\" data=\"filename\"]");

            $uploadsetting = get_option('uploads_use_yearmonth_folders');
            $uploadsetting = isset($uploadsetting) ? $uploadsetting : 0;
            $directory = wp_upload_dir();
            if($uploadsetting == 1){
                $fileDate = do_shortcode("[download_data id=\"$link->selected_id\" data=\"file_date\"]");
                $fileDate = date('Y/m', strtotime($fileDate));
                $filePaths[] =  $directory['basedir']. "/dlm_uploads/$fileDate/$fileName";
            }else{
                $filePaths[] = $directory['basedir'] . "/dlm_uploads/$fileName";
            }

        }

        return $filePaths;
    }

    public function check_blacklist($result, $tag)
    {
        if (!isset($_POST['ebd_settings'])) {
            return $result;  // make sure this is an EBD form
        }
        $email = strtolower(sanitize_email($_POST['your-email']));
        if ($this->blacklist($email)) {
            $result->invalidate('your-email', __('addresses from this domain are not allowed.', 'email-before-download'));
        }
        return $result;
    }

    public function blacklist($email)
    {
        $blacklist = strtolower(get_option('email_before_download_forbidden',""));
        if ($blacklist == "") return false;
        $blacklist = str_replace(array("\r", "\n",'@',' '), '', $blacklist);
        $email = explode('@', $email);
        $domain = $email[1];
        if (strpos($blacklist, ',')) {
            $blacklist = explode(',', $blacklist);
            file_put_contents('blacklinst.txt', var_export($blacklist, true),true);
            if (in_array($domain, $blacklist))
                return true;
        } else {
            if ($domain == $blacklist) return true;
        }

        return false;
    }

    public function link_url($link)
    {
        $permalink = get_option('permalink_structure');
            if($permalink != ""){
                $spacer = "?uid=";
            }else{
                $spacer = "&uid=";
            }
           return do_shortcode("[download_data id=\"$link->selected_id\" data=\"download_link\"]").$spacer.$link->uid;
    }

    public function log($post_data, $user_data)
    {
        $xml = $this->xml($post_data);
        $data = array(
            'email' => sanitize_email($user_data['your-email']),
            'user_name' => sanitize_text_field($user_data['your-name']),
            'posted_data' => $xml,
            'time_requested' => time()
        );

        return $this->db->create_log($data);
    }

    public function xml($data)
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><posted_data>";
        foreach ($data as $key => $value) {
            $xml .= "<$key>";
            if (is_array($value)) {
                foreach ($value as $key2 => $item) {
                    $xml .= "<item-$key2>";
                    $xml .= $item;
                    $xml .= "</item-$key2>";
                }
            } else {
                $xml .= $value;
            }
            $xml .= "</$key>";
        }

        $xml .= "</posted_data>";
        return $xml;
    }

}
