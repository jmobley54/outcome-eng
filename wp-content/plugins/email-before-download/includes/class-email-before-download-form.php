<?php
/**
 * @since      5.0.0
 * @package    Email_Before_Download
 * @subpackage Email_Before_Download/includes
 * @author     M & S Consulting
 */

class Email_Before_Download_Form
{
    public $form_id;
    public $download_id;
    public $titles;
    public $atts;

    public function __construct($atts)
    {
        $this->atts = $atts;
        $this->form_id = $atts['contact_form_id'];
        $this->download_id = $this->att_to_array($atts['download_id']);
        $this->titles = $this->att_to_array($atts['title']);
        unset($this->atts['contact_form_id']);
        unset($this->atts['download_id']);
    }

    public function html()
    {

        $raw = do_shortcode("[contact-form-7 id=\"$this->form_id\" ]");
        $remove = array('<ebd />','<ebd/>','<ebd_left />','<ebd_left/>');
        $raw = str_replace($remove, "", $raw);
        $form = new DOMDocument;
        libxml_use_internal_errors(true);
        $form->formatOutput = true;
        $form->loadHTML(mb_convert_encoding($raw, 'HTML-ENTITIES', 'UTF-8'));
        $form->removeChild($form->doctype);
        $form->replaceChild($form->firstChild->firstChild->firstChild, $form->firstChild);
        $parent = $form->getElementsByTagName('form')->item(0);
        $formID = $parent->getAttribute('action');
        $tmp = explode('#', $formID);
        $this->atts['form_id'] = $tmp[1];
        $paragraph = $parent->getElementsByTagName('div')->item(0);

        foreach ($this->download_id as $key => $id) {
            if(count($this->download_id) == 1) {
                $item = $this->hide_download_dom_obj($id, $key);
            }else{
                $item = $this->download_dom_obj($id, $key);
            }
            $item = $form->importNode($item, true);
            $paragraph->parentNode->insertBefore($item, $paragraph);
        }

        foreach ($this->atts as $name => $value) {
            if($value){
                $item = $this->hidden_dom_obj($name,$value);
                $item = $form->importNode($item, true);
                $paragraph->parentNode->insertBefore($item, $paragraph);
            }
        }
        if($this->atts['hide_form'] == 'yes'){
            $script = $this->jquery_dom_obj();
            $item = $form->importNode($script, true);
            $parent->appendChild($item);
        }
        libxml_clear_errors();
        return $form->saveHTML();
    }

    private function download_dom_obj($id, $key){

        $formObject = new DOMDocument();
        $formObject->formatOutput = true;
            $title = $this->titles[$key];
        $isChecked = get_option('email_before_download_multi_check');
        $isRadio = $this->atts['radio'];
        if ($isRadio != "yes" ) {
            $input = 'checkbox';
        } else {
            $input = 'radio';
        }
        $formObject->loadHTML("<label class='ebd_input'><input type=\"$input\" $isChecked name=\"ebd_downloads[]\" value=\"$id|$title\"> $title</label>");
        $node = $formObject->getElementsByTagName("label");
        return $node->item(0);
    }
    private function hide_download_dom_obj($id, $key){

        $formObject = new DOMDocument();
        $formObject->formatOutput = true;
        $title = $this->titles[$key];
        $formObject->loadHTML("<label class='ebd_input'><input type=\"hidden\"  name=\"ebd_downloads[]\" value=\"$id|$title\"> </label>");
        $node = $formObject->getElementsByTagName("label");
        return $node->item(0);
    }
    private function hidden_dom_obj($name, $value){
        $formObject = new DOMDocument();
        $formObject->formatOutput = true;
        $formObject->loadHTML("<input type=\"hidden\" class=\"ebd_setting\" name =\"ebd_settings[]\" value=\"$name|$value\">");
        $node = $formObject->getElementsByTagName("input");
        return $node->item(0);
    }
    private function jquery_dom_obj(){
            $hide = "";
        $formObject = new DOMDocument();
        $formID = $this->atts['form_id'];
        $formSelector = str_replace('-','',$formID);
        $formObject->formatOutput = true;
        if(($this->atts['hide_form'] == 'yes') && ($this->atts['checked'] == '') && (count($this->download_id) > 1) )
            $hide = "jQuery(\"div#$formID form \").children().not('.ebd_input').not('.wpcf7-response-output').hide();";

        $script = "<script> var ". $formSelector. "selectors = \"div#$formID > form > .ebd_input > input\";
        $hide
        jQuery( ". $formSelector. "selectors ).on( \"click\", function () {
            var ". $formSelector. "n = jQuery( ". $formSelector. "selectors+\":checked\" ).length;
            
            if(". $formSelector. "n > 0) jQuery(\"div#$formID form \").children().not('.ebd_input').not('.wpcf7-response-output').show();
            else jQuery(\"div#$formID form \").children().not('.ebd_input').not('.wpcf7-response-output').hide();
        } );    jQuery('.wpcf7-response-output').hide();
</script>";
        $formObject->loadHTML($script);
        $node = $formObject->getElementsByTagName("script");
        return $node->item(0);

    }
    private function att_to_array($data){
        $formatted = array();
        if (stripos($data, ',')) {
            $formatted = explode(',', $data);
        } else {
            $formatted[] = $data;
        }
        return $formatted;
    }

}