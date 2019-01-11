<?php
/**
 * Elementor oEmbed Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Elementor_CPCFTE_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'Contact Form to Email';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return 'Contact Form to Email';
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fa fa-envelope';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'codepeople-widgets' ];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

        global $wpdb, $cp_cfte_plugin;
        
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Insert Contact Form to Email', 'contact-form-to-email' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

        
        $forms = array();
        $rows = $wpdb->get_results("SELECT id,form_name FROM ".$wpdb->prefix.$cp_cfte_plugin->table_items." ORDER BY form_name");
        foreach ($rows as $item)
           $forms[$item->id] = $item->form_name;
                
		$this->add_control(
			'formid',
			[
				'label' => __( 'Select Form', 'contact-form-to-email' ),
				'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '1',
				'options' => $forms,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
        global $cp_cfte_plugin;

        
        $settings = $this->get_settings_for_display();
        $id = $settings["formid"];
        
        $cp_cfte_plugin->setId($id);
        
        if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) 
        {
            echo $cp_cfte_plugin->filter_content( array("id" => $id) );
            return;
        } 
        else
        {
            $cp_cfte_plugin->print_counter = mt_rand(99999,999999);
            $counter = $cp_cfte_plugin->print_counter;                  
            echo '<fieldset class="ahbgutenberg_editor" disabled>';
            echo $cp_cfte_plugin->filter_content( array("id" => $id) );
            echo '</fieldset>';
            echo '<script>'.
                    'var id = "'.$counter.'";'.
                    'var cp_appbooking_fbuilder_myconfig = {"obj":"{\"pub\":true,\"identifier\":\"_"+id+"\",\"messages\": {}}"};'.
                    'var f = jQuery("#fbuilder_"+id).fbuilder(jQuery.parseJSON(cp_appbooking_fbuilder_myconfig.obj));'.
                    'f.fBuild.loadData("form_structure_"+id);'.
                    '</script>';                          
        }

	}

}

\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elementor_CPCFTE_Widget());