<?php
/**
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */

class Email_Before_Download_Shortcode {

    private $plugin_name;
    private $version;
    private $option_name = 'email_before_download';
    private $db;

    public function __construct( $plugin_name, $version ) {
        global $wpdb;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->db = $wpdb;

    }
public function init_shortcode($user_atts = array(), $content = null, $tag = 'email-download')
{
    if(isset($user_atts['title']))
        $tmpTitle = $user_atts['title'];

    $user_atts = array_change_key_case((array)$user_atts, CASE_LOWER);

    if(isset($tmpTitle))
        $user_atts['title'] = $tmpTitle;

    if(isset($user_atts['checked'])){
        if($user_atts['checked'] == 'no'){
            $user_atts['checked'] = '';
        }else{
            $user_atts['checked'] = 'checked';
        }
    }
    if(isset($user_atts['title'])){

    }
    $user_atts['download_id'] = str_replace(' ', '', $user_atts['download_id']);

    $default_titles = $this->generate_dl_title($user_atts['download_id']);
    $default_atts = array(
        'download_id'       => NULL,
        'contact_form_id'   => get_option($this->option_name.'_default_cf7'),
        'title'             => $default_titles,
        'file'              => NULL,
        'delivered_as'      => get_option($this->option_name.'_delivery_format'),
        'attachment'        => get_option($this->option_name.'_attachment'),
        'force_download'    => '0',
        'checked'           => get_option($this->option_name.'_multi_check','no'),
        'hide_form'         => get_option($this->option_name.'_hidden_form','yes'),
        'radio'             => get_option($this->option_name.'_radio','no'),
        'expire_time'       => get_option($this->option_name.'_expire'),
        'from_email'       => get_option($this->option_name.'_email_from'),
        'from_name'        => get_option($this->option_name.'_from_name'," "),
        'link_format'      => get_option($this->option_name.'_link_format')
    );
    $atts = shortcode_atts($default_atts, $user_atts, $tag);

    $atts['item_id'] = $this->add_item($atts);
    $form = new Email_Before_Download_Form($atts);
    $this->cf7_check();
    $content = $form->html();
    return $content;
}
private function add_item($atts)
{

    $db = new Email_Before_Download_DB();
    return $db->create_item($atts);
}

private function generate_dl_title($data){
        $title ="";
        if(strpos($data,',')){
            $ids = explode(',',$data);
        }else{
            $ids[] = $data;
        }
        foreach ($ids as $id){
            $title .= do_shortcode("[download_data id=\"$id\" data=\"title\"]"). ",";
        }
    $title = substr($title,0,-1);
        return $title;

}

private function cf7_check() {
    if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
        wpcf7_enqueue_scripts();
    }

    if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
        wpcf7_enqueue_styles();
    }
}



}
