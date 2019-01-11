<?php
namespace MaxButtons;

defined('ABSPATH') or die('No direct access permitted');
class maxButtonsProAdmin extends maxButtonsAdmin
{


	function __construct()
	{
		parent::__construct();

	}

	// override the parent function to get the child instance.
	public static function getInstance()
	{

		if (is_null(self::$instance))
			self::$instance = new maxButtonsProAdmin();


		return self::$instance;

	}

	public function proBasicFonts()
	{
		$fonts = array(
		//	'' => '',
			'Antic Slab ' => 'Antic Slab',
			'Arimo' => 'Arimo',
			'Arvo' => 'Arvo',
			'Droid Sans' => 'Droid Sans',
			'Droid Sans Mono' => 'Droid Sans Mono',
			'Droid Serif' => 'Droid Serif',
			'Josefin Slab' => 'Josefin Slab',
			'Lato' => 'Lato',
			'Lora' => 'Lora',
			'Merriweather' => 'Merriweather',
			'Montserrat' => 'Montserrat',
			'Noto Sans' => 'Noto Sans',
			'Open Sans' => 'Open Sans',
			'Open Sans Condensed' => 'Open Sans Condensed',
			'Oswald' => 'Oswald',
			'Pacifico' => 'Pacifico',
			'PT Sans' => 'PT Sans',
			'PT Sans Narrow' => 'PT Sans Narrow',
			'Quicksand' => 'Quicksand',
			'Raleway' => 'Raleway',
			'Roboto' => 'Roboto',
			'Rokkitt' => 'Rokkitt',
			'Shadows Into Light' => 'Shadows Into Light',
			'Source Sans Pro' => 'Source Sans Pro',
			'Ubuntu' => 'Ubuntu',
			'Ubuntu Condensed' => 'Ubuntu Condensed',

		);
		return $fonts;
	}

	public function loadFonts()
	{
		$used_fonts = get_option('maxbuttons_used_fonts');
		$additional_fonts = get_option('maxbuttons_additional_fonts');

		$basic_fonts = array_merge(parent::loadFonts(), $this->proBasicFonts());

		if (! is_array($used_fonts))
			$used_fonts = array();

		if (! is_array($additional_fonts))
			$additional_fonts = array();

		$fonts = array_merge($basic_fonts, $used_fonts, $additional_fonts);
		$fonts = array_unique(array_filter($fonts));
		$fonts = array_merge(array('' => ''), $fonts);
		$fonts = apply_filters("maxbuttons/fontfamilies", $fonts);

		ksort($fonts);
 		return $fonts;

	}

	public static function updateUsedFonts()
	{
		global $wpdb;
		$table = maxUtils::get_table_name();
		$sql = "SELECT text from $table ";
		$results = $wpdb->get_col($sql);
		$fonts = array();

		foreach($results as $result)
		{
			$data = json_decode($result, true);

			$font = isset($data["font"]) ? $data["font"] : '';
			$font2 = isset($data["font2"]) ? $data["font2"] : '';

			$fonts[$font] = $font;
			$fonts[$font2] = $font2;

		}

		$fonts = array_unique(array_filter($fonts));
		update_option('maxbuttons_used_fonts', $fonts);

	}

	// ajax communications for the font lib.
	public static function ajax_font_actions($post)
	{
		ob_start();

		$action = sanitize_text_field($_POST["font_action"]);
		//if ($action !== 'save')
		//	exit(); // none other

		switch($action)
		{
			case 'save':
					static::save_font_action($_POST);
			break;
			case 'load_webfonts':
				 static::load_webfonts();
			break;
		}

	}

	public static function save_font_action($post)
	{
		$fonts = array_filter($post["fonts"]);
		if (is_array($fonts))
			update_option('maxbuttons_additional_fonts', $fonts);

		$fonts = MB()->getClass('admin')->loadFonts();
		ob_end_clean();

		echo json_encode(array(
			'fonts' => $fonts,
			'usedfonts' =>  get_option('maxbuttons_additional_fonts'),
		));
		exit();
	}

	public static function ajax_load_icons ()
	{
			$blocks = maxBlocks::getBlockClasses(); // to init and all
			$iconBlock = new iconBlock();
			$categories = $iconBlock->getFaCategories();
			$icons = $iconBlock->getFAIcons();

			foreach ($icons as $name => $styles)
			{
				foreach($styles as $index => $item)
				{
					$icons[$name][$index]['svg'] = $iconBlock->getFASVG(array('icon' => $item['icon'] ) );
				}
			}

			$results = array(
					'categories' => $categories,
					'icons' => $icons);
			$output = json_encode($results);

			echo $output;

			exit();

	}



		/* Get multiple buttons  -- these overrides are not optimal :/

		Used for overview pages, retrieve buttons on basis of passed arguments.

		@return array Array of found buttons with argument
	*/

	function getButtons($args = array())
	{

		$defaults = array(
			"status" => "publish",
			"orderby" => "id",
			"order" => "DESC",
			"limit" => 20,
			"paged" => 1,
		);
		$args = wp_parse_args($args, $defaults);

		$limit = intval($args["limit"]);
		$page = intval($args["paged"]);
		$escape = array();
		$escape[] = $args["status"];

		// solve this different
		$search = (isset($_GET["s"])) ? sanitize_text_field($_GET["s"]) : false;

		// 'white-list' escaping
		switch ($args["orderby"])
		{
			case "id":
				$orderby = "id";
			break;
			case "name":
			default:
				$orderby = "name";
			break;

		}

		switch($args["order"])
		{
			case "DESC":
			case "desc":
				$order = "DESC";
			break;
			case "ASC":
			case "asc":
			default:
				$order = "ASC";
			break;
		}


		$sql = "SELECT id FROM " . maxUtils::get_buttons_table_name() . " WHERE status = '%s'";
		if ($search)
		{
			$sql .= " AND ( basic like %s OR text like %s )";
			$escape[] = "%" . $this->wpdb->esc_like ($search) . "%";
			$escape[] = "%" . $this->wpdb->esc_like ($search) . "%";  // twice yes
			//$escape .= " '%" . $this->wpdb->esc_like ($search) . "%'";
			//$escape .= " '%" . $this->wpdb->esc_like ($search) . "%'";  // twice yes
		}
		if ($args["orderby"] != '')
		{
			$sql .=  " ORDER BY $orderby $order";

		}

	 	if ($limit > 0)
	 	{

	 		if ($page == 1 )
	 			$offset = 0;
	 		else
	 			$offset = ($page-1) * $limit;

	 		$sql .= " LIMIT $offset, $limit ";
		}

		// fixes for WP 4.8.2, because this is better :/
 		array_unshift($escape, $sql);

		$sql = call_user_func_array(array($this->wpdb,'prepare'), $escape) ;

		$buttons = $this->wpdb->get_results($sql, ARRAY_A);
		return $buttons;

	}

	function getButtonCount($args = array())
	{
		$defaults = array(
			"status" => "publish",

		);
		$args = wp_parse_args($args, $defaults);


		$sql = "SELECT count(id) FROM " . maxUtils::get_buttons_table_name() . " WHERE status = '%s'";

		$escape = array($args["status"]);

		// solve this different
		$search = (isset($_GET["s"])) ? sanitize_text_field($_GET["s"]) : false;
		if ($search)
		{
			$sql .= " AND ( basic like %s OR text like %s )";
			$escape[] = "%" . $this->wpdb->esc_like ($search) . "%";
			$escape[] = "%" . $this->wpdb->esc_like ($search) . "%";  // twice yes
		}

		$sql = $this->wpdb->prepare($sql, $escape );

		if (! $sql ) return -1;
		$result = $this->wpdb->get_var($sql);
		return $result;

	}


}
