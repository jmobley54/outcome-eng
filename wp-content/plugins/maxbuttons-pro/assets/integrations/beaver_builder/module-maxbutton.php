<?php
namespace MaxButtons;
defined('ABSPATH') or die('No direct access permitted');


class moduleMaxbutton extends \FLBuilderModule
{
	protected $module_url;
	protected $module_path;


	public function __construct()
    {
    	$this->module_url = MB()->get_plugin_url(true) . '/assets/integrations/beaver_builder/';
    	$this->module_path = MB()->get_plugin_path(true) . '/assets/integrations/beaver_builder/';


        parent::__construct(array(
            'name'            => __( 'MaxButtons Pro', 'maxbuttons-pro' ),
            'description'     => __( 'Add a MaxButton', 'fl-builder' ),
            'category'        => __( 'Advanced Modules', 'fl-builder' ),
            'dir'             => $this->module_path,
            'url'             => $this->module_url,
            'partial_refresh' => false,
        ));

        $version = MAXBUTTONS_VERSION_NUM;
				MB()->load_modal_script();
				MB()->load_media_script();
        $this->add_js('mbbeaver', $this->module_url . 'includes/beaver.js', array('jquery', 'maxbuttons-modal', 'mb-media-button'), $version , true);
        $this->add_css( 'mbbeaver-css', $this->module_url . 'includes/beaver.css' );
    }




}
