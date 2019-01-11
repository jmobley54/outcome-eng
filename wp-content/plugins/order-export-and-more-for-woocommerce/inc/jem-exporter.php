<?php

class JEM_export_lite
{


    private $objects = array();
    private $my_errors = "";
    private $settings;
    private $message = "";


    public function __construct()
    {

        //error_log('jem_exporter');

        //Globals
        global $jem_export_globals;

        add_action('admin_menu', array($this, 'add_to_menu'), 99);


        //handles the post form - WP automagically calls these!
        add_action('admin_post_update_labels', array(&$this, 'update_labels'));
        add_action('admin_enqueue_scripts', array(&$this, 'load_scripts'));
        add_action('admin_post_export_data', array(&$this, 'export_data'));
        add_action('admin_post_update_meta', array(&$this, 'update_meta'));
        //Simon 2.0.6 - moving save to form based
        add_action('admin_post_save_defaults', array(&$this, 'save_defaults'));

        //handles the form post for the SETTINGS
        add_action('admin_post_save_settings', array(&$this, 'save_settings'));


        $entities = $jem_export_globals['entities'];

        foreach ($entities as $entity) {
            //create the object
            $ent = new $entity;
            //stick it in an array
            $this->objects[$ent->id] = $ent;
        }

        //get the settings
        $this->get_settings();

        //create the error object
        $this->my_errors = new WP_Error();

        //Create the saved order of fields if needed
        foreach ($this->objects as $obj) {

        }
    }

    //Gets called from Render settings and gets us ready!
    public function init()
    {
        //error_log('init');
        //Globals
        global $jem_export_globals;


    }

    /**
     * Load up the stuff we need!
     */
    public function load_scripts()
    {
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-datepicker');

    }

    /**
     * Gets the settings, sets defaults etc
     */
    public function get_settings()
    {
        $this->settings = get_option(JEM_EXP_DOMAIN);


        //ok lets check they are set, if not lets set them to defaults
        //we use this array, makes it very easy to add new ones
        $defaults = array(
            "filename" => "woo-export.csv",
            "date_format" => "Y/m/d",
            "encoding" => "UTF-8",
            "delimiter" => ","

        );

        foreach ($defaults as $key => $value) {
            if (empty($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
    }

    /**
     * This puts us on the woo menu
     */
    public function add_to_menu()
    {


        $this->page = add_submenu_page(
            'woocommerce',
            __('WooCommerce Order Export and More', JEM_EXP_DOMAIN),
            __('Order Export +', JEM_EXP_DOMAIN),
            'manage_woocommerce',
            'JEM_EXPORT_MENU',
            array($this, 'render_settings')
        );
    }


    /**
     * This renders the main page for the plugin - all the front-end fun happens here!!
     */
    public function render_settings()
    {


        $this->init();

        //get the main tab
        if (isset($_REQUEST['tab'])) {

            //so get the tab from the url...
            $tab = $_REQUEST['tab'];
        } else {
            //no tab default to export
            $tab = "export";
        }

        //get the sub-tab
        if (isset($_REQUEST['sub-tab'])) {

            //so get the tab from the url...
            $subTab = $_REQUEST['sub-tab'];
        } else {
            //no sub-tab default to fields
            $subTab = "fields";
        }

        //are we editing an entity? if not default to Product
        if (isset($_REQUEST['entity'])) {

            $entity = $_REQUEST['entity'];
        } else {
            //default
            $entity = "Product";
        }

        //set the active tabs to blank
        $export_active = "";
        $settings_active = "";
        $meta_active = "";


        //get the tab data for this tab

        $content = "";
        switch ($tab) {
            case 'settings':
                $content = $this->generate_settings_tab($subTab);
                $settings_active = "nav-tab-active";
                break;

            case 'meta':
                $content = $this->generate_meta_tab($subTab);
                $meta_active = "nav-tab-active";
                break;

            //default to export
            default:
                $content = $this->generate_export_tab($subTab);
                $export_active = 'nav-tab-active';
                break;


        }


        //The basic html for our page


        //check if we have a message


        $html = '<div class="wrap">
					<h2>' . __('WooCommerce Order Export and More', JEM_EXP_DOMAIN) . '</h2>' . $this->print_admin_messages() . '

							
				<!--  begin email -->

<!-- Begin MailChimp Signup Form -->
<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
<style type="text/css">
	#mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
	/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
	   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
	   #optin {
	background: #dde2ec;
	border: 2px solid #1c3b7e;
	/* padding: 20px 15px; */
	text-align: center;
	width: 800px;
}
	#optin input {
		background: #fff;
		border: 1px solid #ccc;
		font-size: 15px;
		margin-bottom: 10px;
		padding: 8px 10px;
		border-radius: 3px;
		-moz-border-radius: 3px;
		-webkit-border-radius: 3px;
		box-shadow: 0 2px 2px #ddd;
		-moz-box-shadow: 0 2px 2px #ddd;
		-webkit-box-shadow: 0 2px 2px #ddd
	}
		#optin input.name { background: #fff url("' . JEM_EXP_URL . '/images/name.png") no-repeat 10px center; padding-left: 35px }
		#optin input.myemail { background: #fff url("' . JEM_EXP_URL . 'images/email.png") no-repeat 10px center; padding-left: 35px }
		#optin input[type="submit"] {
			background: #217b30 url("' . JEM_EXP_URL . '/images/green.png") repeat-x top;
			border: 1px solid #137725;
			color: #fff;
			cursor: pointer;
			font-size: 14px;
			font-weight: bold;
			padding: 2px 0;
			text-shadow: -1px -1px #1c5d28;
			width: 120px;
			height: 38px;
		}
			#optin input[type="submit"]:hover { color: #c6ffd1 }
		.optin-header{
			font-size: 24px;
			color: #ffffff;
			background-color: #1c3b7e;
			padding: 20px 15px;
		}
		#jem-submit-results{
			padding: 10px 0px;
			font-size: 24px;
		}
</style>
<div id="optin">
<form action="//jem-products.us12.list-manage.com/subscribe/post?u=6d531bf4acbb9df72cd2e718d&amp;id=e70736aa58" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
    <div id="mc_embed_signup_scroll">
	<div class="optin-header">Upgrade to Pro - get a 20% Discount Coupon</div>
<div class="mc-field-group" style="padding: 20px 15px;; text-align: left;">
	<input type="text" value="Enter your email" size="30" name="EMAIL" class="myemail" id="mce-EMAIL" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;"
	>
	<input type="text" value="Enter your name" size="30" name="FNAME" class="name" id="mce-FNAME" onfocus="if(this.value==this.defaultValue)this.value=\'\';" onblur="if(this.value==\'\')this.value=this.defaultValue;"
	>

<input type="submit" value="Get Discount" name="subscribe" id="" class="button">			
	</div>
	<div id="mce-responses" class="clear">
		<div class="response" id="mce-error-response" style="display:none"></div>
		<div class="response" id="mce-success-response" style="display:none"></div>
	</div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_6d531bf4acbb9df72cd2e718d_de987ac678" tabindex="-1" value=""></div>
    <div class="clear"><img src="' . JEM_EXP_URL . '/images/lock.png">We respect your privacy and will never sell or rent your details</div>
    </div>
</form>
</div>
				
				<!--  end email -->

							
							
							
							
				
					<div id="jem-content">
					<h2 class="nav-tab-wrapper">
						<a data-tab-id="export" class="nav-tab ' . $export_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=export-data"">' . __('Export Data', JEM_EXP_DOMAIN) . '</a>
						<a data-tab-id="setting" class="nav-tab ' . $settings_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=settings">' . __('Settings', JEM_EXP_DOMAIN) . '</a>
						<a data-tab-id="meta" class="nav-tab ' . $meta_active . '" href="admin.php?page=JEM_EXPORT_MENU&amp;tab=meta">' . __('Meta', JEM_EXP_DOMAIN) . '</a>
					</h2>
					</div>
			
				</div>';


        //add on the content for this specific tab and voila we are off to the races...
        $html = $html . $content;
        echo $html;


        //now add in the jscript to select the approriate entity, tab & sub-tab & entity
        $html = '
			<div class="hidden" style="display: none;" id="current-tab">' . $tab . '</div>
			<div class="hidden" style="display: none;" id="current-sub-tab">' . $subTab . '</div>
			<div class="hidden" style="display: none;" id="current-entity">' . $entity . '</div>
					<script>
				tab = "' . $tab . '";
				subTab = "' . $subTab . '";
			</script>
		';

        echo $html;
    }

    /**
     * This generates the screen for the export tab
     */
    function generate_export_tab($subTab)
    {

        //the wrapper
        $ret = '<div id="jem-export-post" class="wrap">';

        //First create the list of entities you can export....

        $ret .= $this->generate_entity_list();


        //create the vertical tabs
        $ret .= '
<div id="export-tabs" class="postbox">

	<div class="jem-vert-header">
		<h3 class="hndle" id="entity-type-title">Title goes Here</h3>
	</div>

  <div class="vert-tabs-container">
				

	<div class="jemex-panel-wrap">			
		<ul class="jemex-vert-tabs">
		  <li><a href="#field-tab" id="vert-field-tab" class="dashicons-before dashicons-media-spreadsheet">' . __('Fields', JEM_EXP_DOMAIN) . ' & <span id="vert-label-tab" class="dashicons-before dashicons-tag">' . __('Labels', JEM_EXP_DOMAIN) . '</a></a></li>
		  <li><a href="#filters-tab" id="vert-filter-tab"  class="dashicons-before dashicons-filter">' . __('Filters', JEM_EXP_DOMAIN) . '</a></li>
		  <li><a href="#scheduled-tab" id="vert-scheduled-tab" class="dashicons-before dashicons-clock">' . __('Scheduled', JEM_EXP_DOMAIN) . '</a></li>
								</ul>
		<div id="field-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
		' . $this->generate_fields_tab() . '
		</div>
		</div>
		<div id="filters-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
		' . $this->generate_filters_tab() . '
		</div>
		</div>
				<div id="labels-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
                ' . $this->generate_labels_tab() . '
		</div>
		</div>
		<div id="scheduled-tab" class="jemex-panel">
		<div class="jemex-inner-panel">
				' . $this->generate_scheduled_tab() . '
		</div>
		</div>						
	</div>
			
  </div>				

			</div>
</div> <!-- end wrap -->
				';

        return $ret;
    }


    /**
     * This generates the screen for the settings - HORIZONTAL tabs
     */
    function generate_settings_tab($subTab)
    {


        //Trying out output buffering
        ob_start();

        include_once('templates/tab-settings.php');

        $html = ob_get_clean();


        return $html;


    }

    /**
     * Creates an html output for meta data
     * @param $meta_data - the meta data, assumes you have already got it
     * @return string - html data
     */
    function jemxp_explode_meta_to_html($meta_data)
    {

        $html = "";

        foreach ($meta_data as $meta_name => $val) {

            if (count(maybe_unserialize($val)) == 1) {
                $val = $val[0];

            }

            $val = maybe_unserialize($val);

            //is the val an array?
            if (is_array($val)) {
                $html .= "<TR><TD style='width: 20%;'>{$meta_name}</TD><TD></TD></TR>";

                foreach ($val as $child_name => $child_val) {
                    $html .= "<TR><TD>{$child_name}</TD><TD></TD></TR>";
                    //get it in a nice format
                    if (is_array(maybe_unserialize($child_val)) && count(maybe_unserialize($child_val)) == 1) {
                        $child_val = $child_val[0];
                    }

                    maybe_unserialize($child_val);

                    //possible for children to be arrays as well!!!
                    if (is_array($child_val)) {
                        foreach ($child_val as $grandchild_name => $grandchild_val) {
                            $html .= "<TR><TD>---{$grandchild_name}</TD><TD>{$grandchild_val}</TD></TR>";
                        }
                    } else {
                        $html .= "<TR><TD>---{$child_name}</TD><TD>{$child_val}</TD></TR>";

                    }

                }
            } else {
                $html .= "<TR><TD style='width: 20%;'>{$meta_name}</TD><TD>{$val}</TD></TR>";

            }

        }


        return $html;

    }

    /**
     * This generates the screen for the META - HORIZONTAL tabs
     */
    function generate_meta_tab($subTab)
    {
        //ok so lets get the meta data for this id
        $meta_id = isset($_REQUEST['meta-id']) ? $_REQUEST['meta-id'] : "";
        $meta_type = isset($_REQUEST['meta-type']) ? $_REQUEST['meta-type'] : "";


        //we only try and get the meta data if a vaule is passed in!
        if($meta_id != ""){
            $meta_data = get_post_meta($meta_id);

            //if it's empty then set a message
            if (count($meta_data) == 0)  {
                $this->message = __("No meta data found for this item", JEM_EXP_DOMAIN);
            }

        }

        $html = "";
        $line_item_html = "";
        //*******************
        //Is it a product?
        //*******************
        if ($meta_type == "product") {
            //loop thru and display
            $html .= "<h2>Product Meta</h2>";

            foreach ($meta_data as $meta_name => $val) {

                if (count(maybe_unserialize($val)) == 1) {
                    $val = $val[0];

                }

                $val = maybe_unserialize($val);

                //is the val an array?
                if (is_array($val)) {
                    $html .= "<TR><TD style='width: 20%;'>{$meta_name}</TD><TD></TD></TR>";

                    foreach ($val as $child_name => $child_val) {
                        $html .= "<TR><TD>{$child_name}</TD><TD></TD></TR>";
                        //get it in a nice format
                        if (is_array(maybe_unserialize($child_val)) && count(maybe_unserialize($child_val)) == 1) {
                            $child_val = $child_val[0];
                        }

                        maybe_unserialize($child_val);

                        //possible for children to be arrays as well!!!
                        if (is_array($child_val)) {
                            foreach ($child_val as $grandchild_name => $grandchild_val) {
                                $html .= "<TR><TD>---{$grandchild_name}</TD><TD>{$grandchild_val}</TD></TR>";
                            }
                        } else {
                            $html .= "<TR><TD>---{$child_name}</TD><TD>{$child_val}</TD></TR>";

                        }

                    }
                } else {
                    $html .= "<TR><TD style='width: 20%;'>{$meta_name}</TD><TD>{$val}</TD></TR>";

                }

            }
        }

        //Is it an order?
        if ($meta_type == "order") {
            //we need to get the meta and item meta

            //first Order Meta
            $html .= "<h2>Order Meta</h2>";

            $html .= $this->jemxp_explode_meta_to_html($meta_data);

            //now we need to iterate thru the line items
            global $wpdb;

            $line_item_html = "<h2>Order Line Item Meta</h2>";
            $order_items_sql = $wpdb->prepare("SELECT `order_item_id` as id, `order_item_name` as name, `order_item_type` as type FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id` = %d", $meta_id);
            if ($order_items = $wpdb->get_results($order_items_sql)) {
                foreach ($order_items as $key => $order_item) {
                    $order_itemmeta_sql = $wpdb->prepare("SELECT `meta_key`, `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` AS order_itemmeta WHERE `order_item_id` = %d ORDER BY `order_itemmeta`.`meta_key` ASC", $order_item->id);
                    $order_items[$key]->meta = $wpdb->get_results($order_itemmeta_sql);
                }


                //ok we should now have a nice set of items/meta, meta
                foreach ($order_items as $item) {
                    $line_item_html .= "<TR><TD style='width: 20%;'>{$item->name}</TD><TD>{$item->type}</TD></TR>";

                    //if we have meta for item
                    if ($item->meta) {
                        foreach ($item->meta as $meta_val) {
                            $line_item_html .= "<TR><TD>---{$meta_val->meta_key}</TD><TD>{$meta_val->meta_value}</TD></TR>";

                        }
                    }
                }
            }


        }

        //Trying out output buffering
        ob_start();

        include_once('templates/meta.php');

        $html = ob_get_clean();


        return $html;


    }

    //Simon 2.0.6 - custom sorting
    /**
     * Custom usort function to sort the fieldlist by sort order
     * @param $a
     * @param $b
     * @return int
     */
    function sortBySortOrder($a, $b)
    {

        //set defaults if they are not set
        if (!isset($a['sortOrder'])) {
            $a['sortOrder'] = 999;
        }

        if (!isset($b['sortOrder'])) {
            $b['sortOrder'] = 999;
        }


        return $a['sortOrder'] - $b['sortOrder'];
    }

    /**
     * This generates the fields screen - VERTICAL tab
     */
    function generate_fields_tab()
    {
        //Globals
        global $jem_export_globals;

        $html = "<p class='instructions'>" . __('Select the fields you would like to export and drag rows to reorder exported fields.', JEM_EXP_DOMAIN) . "</p>";
        $html .= '<a href="javascript:void(0);" id="export-select-all">' . __('Select all', JEM_EXP_DOMAIN) . '</a>  |';
        $html .= '<a href="javascript:void(0);" id="export-select-none">' . __('Select none', JEM_EXP_DOMAIN) . '</a>';
        $html .= '<form method="post" id="postform" name="postform"  action="' . admin_url("admin-post.php") . '?tab=export&sub-tab=fields">';

        foreach ($this->objects as $object) {

            $html .= '<div class="export-fields" id="' . $object->id . '-div" style="display: none;">';

            $html .= '<table class="fields-table ' . $object->id . ' " id="fieldsTable"><tbody id="fieldsTableBody" class="sortable_table">';

            //now loop thru the entities fields

            $checkbox_name = 'name="' . $object->id . '_fields[';

            //define variable for labelbox name
            $labelbox_name = 'name="' . $object->id . '_labels[';

            //Simon - 2.0.6 - changes for saving sort order/refactored this bit

            //get label array
            $labels = get_option(JEM_EXP_DOMAIN . '_' . $object->id . '_labels');

            //Build the name for the saved field order
            $sortOptionName = "jemx_" . $object->id . "_sort_order";

            //And get it/decode
            $get_sort_labels = get_option($sortOptionName);
            $sort_order_decode = json_decode($get_sort_labels, true);

            //Name for the selected fields
            $selectedOptionName = "jemx_" . $object->id . "_selected_fields";
            $selected_fields= get_option($selectedOptionName);



            //check if we got a sort order...
            if (null === $sort_order_decode) {
                $sort_order_decode = $object->generate_default_sort_order();
            }

            //First we set any saved sort order values
            foreach ($object->fields as $key => $field) {

                //The field name
                $name = $field['name'];

                //Is it in the sort order saved list? If so set the value
                if (isset($sort_order_decode[$name])) {
                    $object->fields[$name]['sortOrder'] = $sort_order_decode[$name];
                }

            }

            //Now we sort the array
            uasort($object->fields, array($this, 'sortBySortOrder'));


            //Simon 2.0.6 - fixing sorting - basically lets create an array sorted in the right order
            foreach ($object->fields as $field) {

                if (isset($field['disabled'])) {
                    $disabled = " disabled='disabled' ";
                    $msg = "<td><a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>" . __('Available in the PRO version', JEM_EXP_DOMAIN) . "</a></td>";
                } else {
                    $disabled = '';
                    $msg = "";
                }

                // check if label is set or not
                $val = (isset($labels[$field['name']])) ? $labels[$field['name']] : '';


                //Is this field checked???
                if (isset($selected_fields[$field['name']]) && ($selected_fields[$field['name']])) {
                    $checked_status = 'checked';
                } else {
                    $checked_status = '';
                }


                $html .= '<tr class="test" data-key="' . $field['name'] . '" ><td class="drag-handler"></td><td class="recipe-table__cell"><div class="custom-field-class mapping-col-1">';

                $html .= '<input ' . $checked_status . ' type="checkbox" ' . $checkbox_name . $field['name'] . ']"' . $disabled . '>';

                $html .= '</div><div class="mapping-class-2">' . $field['placeholder'] . '</div><div class="custom-labels-class mapping-col-3"><input type="text" size="50"' . $labelbox_name . $field['name'] . ']" placeholder="' . $field['placeholder'] . '" value="' . $val . '"></div></td>';
                $html .= $msg;
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';

        }

        $html .= '<p class="submit">
                <input type="hidden" name="entity-being-edited" id="entity-being-edited" value="">
                <input type="hidden" name="action" value="update_labels"> <input type="hidden" name="data" value="update_labels">
				<input type="hidden" name="action" value="export_data">
				<input type="hidden" name="_wp_http_referer" value="' . urlencode($_SERVER['REQUEST_URI']) . '">
				<input type="hidden" id="entity-to-export" name="entity-to-export" value="export_data">
				<input type="submit" class="button-primary"  id="submit-export" value="Export ' . $object->id . '">
				<input type="submit" class="button-primary jemx_save_defaults"  id="submit-' . $object->id . '-defaults" value="Save as Default">

			</p>';

        $html .= '<p class="display_success_msg" style="display: none;"> Settings Saved successfully. </p>';
        return $html;

    }


    /**
     * This generates the LABELS screen - VERTICAL tab
     */
    function generate_labels_tab()
    {
        //we create a set of divs for each entity

        $html = '<form method="post" id="postform" action="' . admin_url("admin-post.php") . '">';

        foreach ($this->objects as $object) {
            $html .= '<div class="export-labels" id="' . $object->id . '-labels-div" style="display: block;">';
            $html .= '<table><tbody>';


            //lets get the options for these labels
            $labels = get_option(JEM_EXP_DOMAIN . '_' . $object->id . '_labels');

            //now loop thru the entities fields

            $labelbox_name = 'name="' . $object->id . '_labels[';

            foreach ($object->fields as $field) {

                //do we have a custum label for this one?
                $val = (isset($labels[$field['name']])) ? $labels[$field['name']] : '';


                $html .= '<tr><th><label>' . $field['name'] . '</label><td><input type="text" size="50"' . $labelbox_name . $field['name'] . ']" placeholder="' . $field['placeholder'] . '" value="' . $val . '"></td></tr>';
            }

            $html .= '</tbody></table></div>';

        }

        $html .= '<input type="hidden" name="entity-being-edited" id="entity-being-edited" value="">';
        $html .= ' <input type="hidden" name="_wp_http_referer" value="' . urlencode($_SERVER['REQUEST_URI']) . '">';
        $html .= '  <input type="hidden" name="action" value="update_labels"> <input type="hidden" name="data" value="update_labels">';

        $html .= '<p class="submit"><input type="submit" value="' . __('Save changes', JEM_EXP_DOMAIN) . '" class="button-primary"></p>';
//		
        return $html;


    }


    /**
     * This generates the FILTERS for the VERTICAL tab
     */
    function generate_filters_tab()
    {
        $html = "";

        //we generate a div for each entity

        foreach ($this->objects as $object) {
            $html .= '<div class="export-filters" id="' . $object->id . '-filters-div" style="display: block;">';


// 			$html .= '
// 				<div class="filter-dates">
// 					<label>
// 				 	' . __('From Date', JEM_EXP_DOMAIN) . '
// 				 	</label>
// 				 	<input id="order-filter-start-date"  class="jemexp-datepicker">
// 				</div>
// 			';


            $html .= $object->generate_filters();

            $html .= '</div>';

        }


        //now add the submit button
        $html .= '
			<p class="submit">
				<input type="submit" class="button-primary"  id="submit2-export" value="Export ' . $object->id . '">
			</p>';

        //we close the form from that is obened in labels
        $html .= '</form>';

        return $html;
    }

    /**
     * This generates the SCHEDULED screen - VERTICAL tab
     */
    function generate_scheduled_tab()
    {

        //we create a set of divs for each entity

        $html = '';

        foreach ($this->objects as $object) {

            $html .= '<div class="jemex-scheduled export-scheduled" id="' . $object->id . '-scheduled-div" style="display: block;">';

            $html .= "<h2>Scheduled Exports</h2>";
            $html .= "<p><a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>" . __('Available in the PRO version', JEM_EXP_DOMAIN) . "</a></p>";
            $html .= '</div>';

        }


        return $html;

    }


    /**
     * This generates the box with the entity list
     */
    function generate_entity_list()
    {

        global $jem_export_globals;

        //list of active entities
        $active = $jem_export_globals['active'];

        //first lets build the table of entities

        //loop thru the entities & build the table rows
        $html = "";
        foreach ($this->objects as $object) {
            $id = $object->id;

            if (isset($active[$id])) {
                $msg = "";

            } else {
                $msg = "<a href='http://jem-products.com/woocommerce-export-orders-pro-plugin/?utm_source=wordpress&utm_medium=plugin&utm_campaign=wordpress' target='_blank'>" . __('This data is available in the PRO version', JEM_EXP_DOMAIN) . "</a>";
            }

            $html .= '<tr><td width="150px"><input type="radio" class="checkbox-class" id="' . $id . '" value="' . $id . '" name="datatype">';
            $html .= '<label for="' . $id . '">' . __($id, JEM_EXP_DOMAIN) . '</label></td>';
            $html .= '<td>' . $msg . '</td>';
            $html .= '</tr>';
        }


        $table = '<table><tbody>' . $html . '</tbody></table>';

        $html = '
<div id="export-type" class="postbox">
	<h3 class="hndle">' . __('Export Type', JEM_EXP_DOMAIN) . '</h3>
	<div class="inside">
		<p class="instructions">' . __('Select the data type you would like to export.', JEM_EXP_DOMAIN) . '</p>' . $table . '
	</div>
				
</div>				
		';


        return $html;

    }


    /**
     * This handles the updates to the labels
     * It is called automagically from the form post
     */
    function update_labels()
    {

        //lets update any of the labels!
        //first get the entity we are edting
        $ent = (isset($_POST['entity-being-edited'])) ? $_POST['entity-being-edited'] : '';

        if ($ent === '') {
            //no entity being edited
            wp_redirect(urldecode($_POST['_wp_http_referer']));
        }

        //the name of the labels
        $nm = $ent . "_labels";

        $labels = (isset($_POST[$nm])) ? array_filter($_POST[$nm]) : array();

        //And update we go
        update_option(JEM_EXP_DOMAIN . '_' . $ent . '_labels', $labels);

        //save the location on the page into the url

        $url = add_query_arg(array('tab' => 'export', 'sub-tab' => 'labels', 'entity' => $ent), urldecode($_POST['_wp_http_referer']));


        wp_redirect($url);
    }

    /**
     * This handles the form post from the settings tab
     * Called automagically from the admin_post action
     */

    /**
     * This handles the form post from from the META VIEWER tab
     * Called automagically from the admin_post action
     */
    function update_meta()
    {


        //if no meta id then just go straight back
        if (!isset($_POST['meta_id']) || $_POST['meta_id'] == "") {
            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        $id = $_POST['meta_id'];
        $type = $_POST['meta_type'];

        $url = add_query_arg(array('tab' => 'meta', 'meta-id' => $id, 'meta-type' => $type), urldecode($_POST['_wp_http_referer']));
        wp_redirect(urldecode($url));
        return;


    }

    /**
     * This handles the form post from the settings tab
     * Called automagically from the admin_post action
     */
    function save_settings()
    {
        //ok lets take each field, sanitize it and off we go!

        $settings = array();
        $settings['filename'] = sanitize_text_field($_POST['jemex_export_filename']);

        $settings['encoding'] = sanitize_text_field($_POST['jemex_encoding']);

        $settings['date_format'] = sanitize_text_field($_POST['jemex_date_format']);

        $settings['delimiter'] = sanitize_text_field($_POST['jemex_field_delimiter']);

        //save them
        update_option(JEM_EXP_DOMAIN, $settings);

        //set the transient
        $this->save_admin_messages(__('Settings Saved.', JEM_EXP_DOMAIN), 'updated');

        //now just goback to settings!
        wp_redirect(urldecode($_POST['_wp_http_referer']));
        return;


    }


    //Simon 2.0.6 moving save defaults from AJAX to form based
    /**
     * This handles the form post from the labels tabe for SAVE DEFAULTS
     * Called automagically from the admin_post action
     */
    function save_defaults()
    {

        //First lets find what entity we are saving for.
        $ent = (isset($_POST['entity-to-export'])) ? $_POST['entity-to-export'] : '';

        if ($ent === '') {
            //no entity - shouldn't get here, but if we do, go back to where we came from!
            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        //lets get the fields that have been selected
        $temp = $ent . "_fields";

        $defaultFields = array();

        if (isset($_POST[$temp])) {
            $defaultFields = $_POST[$temp];
        } else {
            //No fields to export so display an error message and return

            $this->save_admin_messages(__('You have not selected any fields to save as default', JEM_XPRO_DOMAIN), 'error');

            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        //OK lets save them
        $name = "jemx_" . $ent . "_selected_fields";

        update_option($name, $defaultFields);

        //OK lets save it in our options
        //$this->settings['field_defaults'][$ent] = $defaultFields;

//        //add any meta if we have them
//        if (isset($_POST[$ent . '_meta'])) {
//            $this->settings['meta'][$ent] = $_POST[$ent . '_meta'];
//        }
//
//        //add any product if we have them
//        if (isset($_POST[$ent . '_product'])) {
//            $this->settings['product'][$ent] = $_POST[$ent . '_product'];
//        }
//
//        //add any item meta if we have them
//        if (isset($_POST[$ent . '_item_meta'])) {
//            $this->settings['item_meta'][$ent] = $_POST[$ent . '_item_meta'];
//        }


        //And any custom options
//        if (isset($_POST[$ent . '_custom'])) {
//            $this->settings['custom'][$ent] = $_POST[$ent . '_custom'];
//        }

        //save them
        update_option(JEM_XPRO_DOMAIN, $this->settings);

        //Now we want to save the SORT ORDER of the fields
        //comes in ENT_labels where ENT is he entity being edited
        $labelsName = $ent . '_labels';


        if(isset($_POST[$labelsName])){

            $sortOrder = array();
            $i=1;

            foreach($_POST[$labelsName] as $key => $val){
                $sortOrder[$key] = $i;

                $i = $i +1;

            }

            //Now save it
            $encoded = json_encode($sortOrder);
            $optionName = 'jemx_' . $ent . '_sort_order';
            update_option($optionName, $encoded);

        }

        //set the transient
        $this->save_admin_messages(__('Defaults Updated.', JEM_XPRO_DOMAIN), 'updated');

        //now just goback to where we came from - with the addition of the tab/sub-tab
        $url = add_query_arg(array('tab' => 'export', 'sub-tab' => 'fields', 'entity' => $ent), urldecode($_POST['_wp_http_referer']));

        wp_redirect(urldecode($url));
        return;


    }

    /**
     * This handles the export of the data
     * * gets called automagically by the submit of the form
     */
    function export_data()
    {

        //code for save labels
        //lets update any of the labels!
        //first get the entity we are edting
        $ent = (isset($_POST['entity-being-edited'])) ? $_POST['entity-being-edited'] : '';

        if ($ent === '') {
            //no entity being edited
            wp_redirect(urldecode($_POST['_wp_http_referer']));
        }

        //the name of the labels
        $nm = $ent . "_labels";
        $labels = (isset($_POST[$nm])) ? array_filter($_POST[$nm]) : array();

        //And update we go
        update_option(JEM_EXP_DOMAIN . '_' . $ent . '_labels', $labels);

        //load settings
        $this->get_settings();

        $output_fileName = $this->settings['filename'];

        //first get the entity we are exporting
        $ent = (isset($_POST['entity-to-export'])) ? $_POST['entity-to-export'] : '';

        if ($ent === '') {
            //no entity being edited
            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        //if no object redirects
        if (!isset($this->objects[$ent])) {

            //hmmmmm no entity exists - something screwey happened!
            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        //get the entity
        $obj = $this->objects[$ent];

        //lets get the field list to display and put it in the entity object
        $temp = $ent . "_fields";
        if (isset($_POST[$temp])) {
            $fieldsToExport = $_POST[$temp];
        } else {
            //No fields to export so display an error message and return

            $this->save_admin_messages(__('You have not selected any fields to export', JEM_EXP_DOMAIN), 'error');

            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;
        }

        $obj->fieldsToExport = $fieldsToExport;

        //load the user settings into the object
        $obj->settings = $this->settings;

        //lets get the appropriate filters for this entity
        $ret = $obj->extract_filters($_POST);

        //did we get an error?
        if ($ret != '') {
            $this->save_admin_messages($ret, 'error');

            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;

        }

        //create the file name - this is the name stored on our server
        $dir = wp_upload_dir();
        $fileName = $dir['basedir'] . '/JEM_csv_export.csv';
        $file = fopen($fileName, 'w+');


        //ok we have an object - lets execute the darn query!
        $ret = $obj->run_query($file);

        if ($ret === false) {
            $this->save_admin_messages(__('No records were found - please modify the filters and try again', JEM_EXP_DOMAIN), 'error');

            wp_redirect(urldecode($_POST['_wp_http_referer']));
            return;


        }
        fclose($file);

        //now download the CSV file...

        if (file_exists($fileName)) {

            $file = fopen($fileName, 'r');
            $contents = fread($file, filesize($fileName));
            fclose($file);

            //delete the file
            unlink($fileName);

            //funky headers!
            //TODO - put this in a function - need to work out how to handle non-western characters etc
            //http://www.andrew-kirkpatrick.com/2013/08/output-csv-straight-to-browser-using-php/ with some mods
            header("Expires: 0");
            header("Pragma: no-cache");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=$output_fileName.csv");

            //now write it out
            $file = @fopen('php://output', 'w');
            fwrite($file, $contents);
            fclose($file);
        }

    }


    /**
     * Saves admion messages of a specific type, currently 'updated' or 'error'
     * @param unknown $message
     * @param unknown $type
     */
    function save_admin_messages($message, $type = 'updated')
    {
        //add it to the trasnient queue

        $html = '
			<div id="message" class="' . $type . '">
			<p>' . $message . '</p>
			</div>
		';

        set_transient(JEM_EXP_DOMAIN . '_messages', $html, MINUTE_IN_SECONDS);
    }


    /**
     * Prints any admin messages
     */
    function print_admin_messages()
    {
        $html = get_transient(JEM_EXP_DOMAIN . '_messages');
        if ($html != false) {
            delete_transient(JEM_EXP_DOMAIN . '_messages');
            echo $html;
        }
    }
}

?>