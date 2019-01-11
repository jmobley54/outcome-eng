<?php
namespace OptArt\WoocommerceOnePageShopping\Classes\WpPages;
use OptArt\WoocommerceOnePageShopping\Classes\Services\setting_provider;

/**
 * Functionalities on admin setting page
 * Class Bsb_Admin_Settings
 */
class admin_settings extends common
{
    /**
     * @var setting_provider
     */
    private $setting_provider;

	private $section_tabs = array('product','shop','category','advanced');
	private $sections = array('product'=>'product.settings','shop'=>'shop.page.settings','category'=>'cat.page.settings','advanced'=>'advanced.settings');
	
    /**
     * Run the hooks!
     */
    public function _run()
    {
        add_action( 'admin_menu', array( $this, 'admin_menu_item' ) );
        add_action( 'admin_init', array( $this, 'register_plugin_options' ) );

        $this->setting_provider = new setting_provider( $this->get_translator(), self::get_plugin_identifier() );
		
		//TipTip CSS
		wp_enqueue_style( self::get_plugin_identifier() . '_tiptip_styles', plugins_url( 'assets/css/tipTip.css', self::get_plugin_file() ) );

        if(!is_admin()){return false;}
		//TipTip JS
		$this->enqueue_script( self::get_plugin_identifier() . '_tiptip_scripts', 'jquery.tipTip.minified.js', self::get_plugin_file(), false, array(
            'jquery'
        ) );
		$this->enqueue_script( self::get_plugin_identifier() . '_admin_scripts', 'admin-tiptip-min.js', self::get_plugin_file(), false, array(
            'jquery'
        ) );
    }

    /**
     * Returns the path to the templates in admin setting page
     * @return string
     */
    public function get_template_path()
    {
        return 'templates/admin-settings';
    }

    /**
     * Adding the submenu to WooCommerce menu
     */
    public function admin_menu_item()
    {
        add_submenu_page(
            'woocommerce',
            $this->get_translation( 'one.page.shopping' ),
            $this->get_translation( 'one.page.shopping' ),
            'manage_options',
            self::get_plugin_identifier(),
            array( $this, 'display_admin_settings' )
        );
    }

    /**
     * Render the template with settings
     */
    public function display_admin_settings()
    {
        $this->render_template( 'admin-settings.php', array(
            'plugin_identifier' => self::get_plugin_identifier(),
            'translator' => $this->get_translator(),
			'tabs' => $this->render_settings_tabs($this->get_active_tab()),
			'sections' => $this->get_sections()
        ) );
    }

    /**
     * Adds the new section on the setting page
     * @param string $id
     * @param string $label
     * @return $this
     */
    private function add_section( $id, $label )
    {
        add_settings_section(
            $id,
            $label,
            function() {},
            self::get_plugin_identifier()
        );
        return $this;
    }

    /**
     * Registering the settings, sections and field for plugin settings page
     */
    public function register_plugin_options()
    {
        register_setting(
            self::get_plugin_identifier(),
            self::get_plugin_identifier(),
            array( $this, 'validate_settings' )
        );

        $product_section_id = self::get_plugin_identifier() . '_product';
        $shop_section_id = self::get_plugin_identifier() . '_shop';
        $cat_section_id = self::get_plugin_identifier() . '_cat';
        $adv_section_id = self::get_plugin_identifier() . '_adv';
		
        $this
            ->add_section( $product_section_id, $this->get_translation( 'product.settings' ) )
            ->add_section( $shop_section_id, $this->get_translation( 'shop.page.settings' ) )
            ->add_section( $cat_section_id, $this->get_translation( 'cat.page.settings' ) )
            ->add_section( $adv_section_id, $this->get_translation( 'advanced.settings' ) );

        $this
            ->add_settings_radio( 'plugin-scope', $product_section_id )
            ->add_settings_radio( 'display-cart', $product_section_id )
            ->add_settings_radio( 'display-checkout', $product_section_id )
            ->add_settings_radio( 'automatically-add-to-cart', $product_section_id )
            ->add_settings_radio( 'shop-page', $shop_section_id )
            ->add_settings_radio( 'shop-display-cart', $shop_section_id )
            ->add_settings_radio( 'shop-display-checkout', $shop_section_id )
            ->add_settings_radio( 'cat-plugin-scope', $cat_section_id )
            ->add_settings_radio( 'cat-display-cart', $cat_section_id )
            ->add_settings_radio( 'cat-display-checkout', $cat_section_id );
			
		$this->settings_line( 'update-sidebar', $adv_section_id , array( 'enabled' => 0, 'tag' => 'li', 'attribute' => 'class', 'attribute-value' => 'cart') );
		$this->settings_line( 'update-cart-total', $adv_section_id, array( 'enabled' => 0, 'tag' => 'span', 'attribute' => 'class', 'attribute-value' => 'amount') );
		$this->settings_line( 'update-cart-contents', $adv_section_id, array( 'enabled' => 0, 'tag' => 'span', 'attribute' => 'class', 'attribute-value' => 'contents') );
		
        $section_id = $adv_section_id;
        $options = get_option(self::get_plugin_identifier());
        $setting_id = 'update-cart-contents';
        $defaults = array('add-text' => 0, 'singular-form' => 'item', 'plural-form' => 'items', 'force-refresh' => false);
        add_settings_field(
            $setting_id.'-add-text',
            $this->get_translation('label.'.$setting_id.'-add-text'),
            array( $this, 'render_checkbox' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-add-text',
                'value' => $this->get_option($options,$setting_id.'-add-text', $defaults['add-text']),
                'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-add-text')
            )
        );
        add_settings_field(
            $setting_id.'-singular-form',
            $this->get_translation('label.'.$setting_id.'-singular-form'),
            array( $this, 'render_text' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-singular-form',
                'value' => $this->get_option($options,$setting_id.'-singular-form', $defaults['singular-form']),
                'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-singular-form')
            )
        );
        add_settings_field(
            $setting_id.'-plural-form',
            $this->get_translation('label.'.$setting_id.'-plural-form'),
            array( $this, 'render_text' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-plural-form',
                'value' => $this->get_option($options,$setting_id.'-plural-form', $defaults['plural-form']),
                'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-plural-form')
            )
        );
        add_settings_field(
            $setting_id.'-force-refresh',
            $this->get_translation('label.'.$setting_id.'-force-refresh'),
            array( $this, 'render_checkbox' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-force-refresh',
                'value' => $this->get_option($options,$setting_id.'-force-refresh', $defaults['force-refresh']),
                'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-force-refresh')
            )
        );
    }
	
	private function settings_line( $setting_id, $section_id, $defaults ){
		$options = get_option(self::get_plugin_identifier());
		
		add_settings_field(
            $setting_id.'-enable',
            $this->get_translation('label.'.$setting_id.'-enable'),
            array( $this, 'render_checkbox' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-enable',
                'value' => $this->get_option($options,$setting_id.'-enable', $defaults['enabled']),
				'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-enable')
            )
        );
		add_settings_field(
            $setting_id.'-tag',
			$this->get_translation('label.'.$setting_id.'-tag'),
            array( $this, 'render_text' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-tag',
                'value' => $this->get_option($options,$setting_id.'-tag', $defaults['tag']),
				'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-tag')
            )
        );
		
		$select=array('class' => '.','id' => '#');
		add_settings_field(
            $setting_id.'-attribute',
			$this->get_translation('label.'.$setting_id.'-attribute'),
            array( $this, 'render_select' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-attribute',
                'value' => $this->get_option($options,$setting_id.'-attribute', $defaults['attribute']),
				'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-attribute'),
				'select' => $select
            )
        );
		add_settings_field(
            $setting_id.'-attribute-value',
			$this->get_translation('label.'.$setting_id.'-attribute-value'),
            array( $this, 'render_text' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'id' => $setting_id.'-attribute-value',
                'value' => $this->get_option($options,$setting_id.'-attribute-value', $defaults['attribute-value']),
				'tiptip' => $this->get_translation('tiptip.'.$setting_id.'-attribute-value')
            )
        );
	}
	
	public function render_checkbox( $params ){
		$checked = ($params['value']== TRUE) ? 'checked' : '';
		echo '<span class="dashicons dashicons-editor-help help_tip checkbox_tip" title="'.$params['tiptip'].'"></span>';
		echo '<input type="checkbox" name="'.self::get_plugin_identifier().'['.$params['id'].']" value="TRUE" '.$checked.'/>';
	}
	
	public function render_text( $params ){
		echo '<span class="dashicons dashicons-editor-help help_tip text_tip" title="'.$params['tiptip'].'"></span>';
		echo '<input type="text" name="'.self::get_plugin_identifier().'['.$params['id'].']" value="'.$params['value'].'" />';
	}
	
	public function render_select( $params ){
		echo '<span class="dashicons dashicons-editor-help help_tip select_tip" title="'.$params['tiptip'].'"></span>';
		echo '<select name="'.self::get_plugin_identifier().'['.$params['id'].']">';
		foreach($params['select'] as $option => $value){
			echo '<option value="'.$value.'" '.selected($params['value'],$value).'>'.$option.'</option>';
		}
		echo '</select>';
	}
	
	public function get_option($options, $id, $default=''){
		if(is_array($options) ? array_key_exists($id, $options) : FALSE){
			return $options[$id];
		}
		return $default;
		
	}

    /**
     * Add radio input into the settings page
     * @param string $setting_id
     * @param string $section_id
     * @return $this
     * @throws \Exception
     */
    private function add_settings_radio( $setting_id, $section_id )
    {
        $setting = $this->setting_provider->get( $setting_id );
		$tiptips = array();
        $options = array();
        foreach ( $setting->get_all_values() as $value ) {

            $options[$value->get_identifier()] = $value->get_description();
			$tiptips[$value->get_identifier()] = $this->get_translation('tiptip.'.$setting->get_identifier().'.'.$value->get_identifier());
        }

        add_settings_field(
            $setting->get_identifier(),
            $setting->get_label(),
            array( $this, 'render_settings_radio' ),
            self::get_plugin_identifier(),
            $section_id,
            array(
                'field_id' => $setting->get_identifier(),
                'options' => $options,
                'stored_value' => $setting->get_stored_value(),
				'tiptips' => $tiptips
            )
        );

        return $this;
    }

    /**
     * Validate (and return store value) the user (editor) input
     * @param array $input
     * @return array
     */
    public function validate_settings( $input )
    {
        add_settings_error(
            self::get_plugin_identifier(),
            self::get_plugin_identifier(),
            $this->get_translation( 'settings.saved' ),
            'updated'
        );

        return $input;
    }

    /**
     * Rendering the radio input
     * @param array $params
     */
    public function render_settings_radio( array $params )
    {
        $this->render_template( 'radio.php', array(
            'name' => $params['field_id'],
            'plugin_identifier' => self::get_plugin_identifier(),
            'options' => $params['options'],
            'checked' => $params['stored_value'],
            'tiptips' => $params['tiptips']
        ) );
    }
	
	//Function for getting Tabs HTML
	public function render_settings_tabs($active='product'){
		$markup='';
		
		foreach($this->section_tabs as $section){
			$a_tab = $active == $section ? 'nav-tab-active' : '';
			$markup.='<a href="?page='.self::get_plugin_identifier().'&tab='.$section.'" class="nav-tab '.$a_tab.'">'.$this->get_translation('ops.tab.'.$section).'</a>';
		};
		return $markup;
	}

	//Function for getting active tab from GET request.
	public function get_active_tab(){
		if( isset( $_GET['tab'] ) ){
			$active_tab = $_GET['tab'];
			return stripslashes_deep($active_tab);
		}
		else{
			return $this->section_tabs[0];
		}
	}
	
	//Function for getting selected section visible only.
	public function get_sections(){
		
		ob_start();
			do_settings_sections(self::get_plugin_identifier());
			$html = ob_get_contents();
		ob_end_clean();
		
		$tab=$this->get_active_tab();
		$sections=$this->sections;
		unset($sections[$tab]);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $tsections = array();
        foreach($sections as $section){
            $tsections[] = $this->get_translation($section);
        }

        $tables = $dom->getElementsByTagName('table');
        $i = 0;
        $headers = ($dom->getElementsByTagName('h2')->length > $dom->getElementsByTagName('h3')->length) ?  $dom->getElementsByTagName('h2') : $dom->getElementsByTagName('h3');
        foreach($headers as $element){
            if(array_search($element->nodeValue, $tsections) !== false){
                $element->setAttribute('style', 'display:none');
                $tables->item($i)->setAttribute('style', 'display:none');
            }
            $i++;
        }
        return $dom->saveHTML();
		
	}
}
