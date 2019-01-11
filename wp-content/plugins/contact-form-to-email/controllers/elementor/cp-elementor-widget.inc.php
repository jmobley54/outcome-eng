<?php

if (!class_exists('CP_ContactFormToEmail_Elementor'))
{    
    class CP_ContactFormToEmail_Elementor {
          
      private $prefix = 'cp_contactformtoemail';           
           
      function __construct() { 
              // Register widget for Elementor builder.
              add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widget'));
              // Register 10Web category for Elementor widget if 10Web builder doesn't installed.
              add_action('elementor/elements/categories_registered', array($this, 'register_widget_category'), 1, 1);
              //fires after elementor editor styles and scripts are enqueued.
              add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_editor_styles'), 11);
              if ((isset($_GET["action"]) && $_GET["action"] == 'elementor') || (isset($_GET["elementor-preview"]) ))
                  add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 11);
      }
         
      function enqueue_editor_styles() {
         // wp_enqueue_style('cfte-editor-styles', plugins_url('/elementor_icon.css', __FILE__), array(), '1.0.0');
      }      
         
      function enqueue_frontend_styles() {
          wp_enqueue_style('cfte-publicstyle', plugins_url('/../../css/stylepublic.css', __FILE__));
        
          wp_deregister_script('query-stringify');
          wp_register_script('query-stringify', plugins_url('/../../js/jQuery.stringify.js', __FILE__));
        
          wp_deregister_script($this->prefix.'_validate_script');
          wp_register_script($this->prefix.'_validate_script', plugins_url('/../../js/jquery.validate.js', __FILE__));
          wp_enqueue_script( $this->prefix.'_builder_script',
                 plugins_url('/../../js/fbuilderf.jquery.js', __FILE__),array("jquery","jquery-ui-core","jquery-ui-datepicker","jquery-ui-widget","jquery-ui-position","jquery-ui-tooltip","query-stringify",$this->prefix."_validate_script"), false, true );          
      }      
    
      /**
       * Register widget for Elementor builder.
       */
      function register_elementor_widget() {
          if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {
              require_once dirname( __FILE__ ) . '/elementorwidget_class.inc.php';
          }
      }
    
      /**
       * Register 10Web category for Elementor widget if 10Web builder doesn't installed.
       *
       * @param $elements_manager
       */
      function register_widget_category( $elements_manager ) {
          $elements_manager->add_category(
                                          'codepeople-widgets', array(
                                          'title' => __('CodePeople', 'codepeople-builder'),
                                          'icon' => 'fa fa-envelope',
                                          ));
      }
      
    }

}

$cp_cfte_elementor_widget = new CP_ContactFormToEmail_Elementor;

?>