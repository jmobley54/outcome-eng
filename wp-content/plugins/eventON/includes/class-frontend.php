<?php
/**
 * evo_frontend class for front and backend.
 *
 * @class 		evo_frontend
 * @version		2.5.2
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class evo_frontend {

	private $content;
	public $evo_options;
	public $evo_lang_opt;

	public function __construct(){
		global $eventon;

		// eventon related wp options access on frontend
		$this->evo_options = get_option('evcal_options_evcal_1');
		$this->evo_lang_opt = get_option('evcal_options_evcal_2');

		// register styles and scripts
		add_action( 'init', array( $this, 'register_scripts' ), 10 );

		if(!evo_settings_val('evo_load_scripts_only_onevo', $this->evo_options)){
			add_action( 'wp_enqueue_scripts', array( $this, 'load_evo_scripts_styles' ), 10 );
			add_action( 'wp_head', array( $this, 'load_dynamic_evo_styles' ), 50 );
		}
		if(evo_settings_val('evo_load_scripts_only_onevo', $this->evo_options) && 
			evo_settings_val('evo_load_all_styles_onpages', $this->evo_options)
		){
			add_action( 'wp_enqueue_scripts', array( $this, 'load_default_evo_styles' ), 50 );
			add_action( 'wp_head', array( $this, 'load_dynamic_evo_styles' ), 50 );
		}
		
		add_filter('body_class', array($this, 'body_classes'), 10,1);

		$this->evopt1 = $eventon->evo_generator->evopt1;

		if(empty($this->evopt1['evcal_header_generator']) || (!empty($this->evopt1['evcal_header_generator']) && $this->evopt1['evcal_header_generator']!='yes')){
			add_action( 'wp_head', array( $this, 'generator' ) );
		}

		// SINGLE Events related
		add_action( 'wp_head', array( $this, 'fb_headers' ) );	
		add_filter('eventon_eventCard_evosocial', array($this, 'add_social_media_to_eventcard'), 10, 2);
		add_filter('eventon_eventcard_array', array($this, 'eventcard_array'), 10, 4);
		add_filter('evo_eventcard_adds', array($this, 'eventcard_adds'), 10, 1);
		$this->register_se_sidebar();

		// schedule deleting past events
			add_action('evo_trash_past_events', array($this, 'evo_perform_trash_past_events'));	

		add_action( 'wp_footer', array( $this, 'footer_code' ) ,15);
	}

	// styles and scripts
		public function register_scripts() {
			global $eventon;
			
			$evo_opt= $this->evo_options;			
			
			// Google gmap API script -- loadded from class-calendar_generator.php	
			//$apikey = !empty($evo_opt['evo_gmap_api_key'])? '?key='.$evo_opt['evo_gmap_api_key'] :'';
			//wp_enqueue_script('testgmap','https://maps.googleapis.com/maps/api/js'.$apikey);
			//wp_enqueue_style( 'select2',AJDE_EVCAL_URL.'/assets/css/select2.css');
			
			wp_register_script('evo_mobile',$eventon->assets_path.'js/jquery.mobile.min.js', array('jquery'), $eventon->version, true ); // 2.2.17
			wp_register_script('evcal_easing', $eventon->assets_path. 'js/jquery.easing.1.3.js', array('jquery'),'1.0',true );//2.2.24
			wp_register_script('evo_mouse', $eventon->assets_path. 'js/jquery.mousewheel.min.js', array('jquery'),$eventon->version,true );//2.2.24
			wp_register_script('evcal_functions', $eventon->assets_path. 'js/eventon_functions.js', array('jquery'), $eventon->version ,true );// 2.2.22
			wp_register_script('evcal_ajax_handle', $eventon->assets_path. 'js/eventon_script.js', array('jquery'), $eventon->version ,true );
			wp_localize_script( 
				'evcal_ajax_handle', 
				'the_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventon_nonce' )
				)
			);

			// google maps	
			wp_register_script('eventon_gmaps', $eventon->assets_path. 'js/maps/eventon_gen_maps.js', array('jquery'), $eventon->version ,true );	
			wp_register_script('eventon_gmaps_blank', $eventon->assets_path. 'js/maps/eventon_gen_maps_none.js', array('jquery'), $eventon->version ,true );	
			wp_register_script('eventon_init_gmaps', $eventon->assets_path. 'js/maps/eventon_init_gmap.js', array('jquery'),'1.0',true );
			wp_register_script( 'eventon_init_gmaps_blank', $eventon->assets_path. 'js/maps/eventon_init_gmap_blank.js', array('jquery'), $eventon->version ,true ); // load a blank initiate gmap javascript

			$apikey = !empty($evo_opt['evo_gmap_api_key'])? '?key='.$evo_opt['evo_gmap_api_key'] :'';
			wp_register_script( 'evcal_gmaps', apply_filters('eventon_google_map_url', 'https://maps.googleapis.com/maps/api/js'.$apikey), array('jquery'),'1.0',true);
			

			// STYLES
			wp_register_style('evo_font_icons',$eventon->assets_path.'fonts/font-awesome.css','',$eventon->version);		
			
			// Defaults styles and dynamic styles
			wp_register_style('evcal_cal_default',$eventon->assets_path.'css/eventon_styles.css', array(), $eventon->version);	
			// single event
			wp_register_style('evo_single_event',$eventon->assets_path.'css/evo_event_styles.css',array(),$eventon->version);	

			// addon styles
			if(evo_settings_check_yn($this->evo_options,'evcal_concat_styles'  )){
				$ver = get_option('eventon_addon_styles_version');
				wp_register_style('evo_addon_styles',$eventon->assets_path.'css/eventon_addon_styles.css',array(), (empty($ver)?1.0:$ver) );
			}


			global $is_IE;
			if ( $is_IE ) {
				wp_register_style( 'ieStyle', $eventon->assets_path.'css/ie.css', array(), '1.0' );
				wp_enqueue_style( 'ieStyle' );
			}

			// LOAD custom google fonts for skins	
			if( evo_settings_val( 'evo_googlefonts', $this->evo_options, true)){
				//$gfonts = (is_ssl())? 'https://fonts.googleapis.com/css?family=Oswald:400,300|Open+Sans:400,300': 'http://fonts.googleapis.com/css?family=Oswald:400,300|Open+Sans:400,300';	
				$gfonts="//fonts.googleapis.com/css?family=Oswald:400,300|Open+Sans:700,400,400i|Roboto:700,400";
				wp_register_style( 'evcal_google_fonts', $gfonts, '', '', 'screen' );
			}
			
			
			$this->register_evo_dynamic_styles();

			// Custom PHP codes to run via eventon settings
				if( evo_settings_check_yn($evo_opt, 'evo_php_coding') ){
					$php_codes = get_option('evcal_php');
					if(!empty($php_codes)){	

						$value = eval($php_codes."; return false;");
						if($value!== false) {eval($php_codes);}						
					}
				}

			// pluggable
				do_action('evo_register_other_styles_scripts');



		}
		public function register_evo_dynamic_styles(){
			global $eventon;
			$opt= $this->evo_options;
			if(  evo_settings_val('evcal_css_head',  $opt, true)){
				if(is_multisite()) {
					$uploads = wp_upload_dir();
					$dynamic_style_url = $uploads['baseurl'] . '/eventon_dynamic_styles.css';					
				} else {
					$dynamic_style_url = $eventon->assets_path. 'css/eventon_dynamic_styles.css';					
				}

				$dynamic_style_url = str_replace(array('http:','https:'), '',$dynamic_style_url);

				wp_register_style('eventon_dynamic_styles', $dynamic_style_url, 'style');
			}
		}
		
		public function load_dynamic_evo_styles(){
			$opt= $this->evo_options;
			if(evo_settings_val('evcal_css_head', $opt)){
				
				$dynamic_css = get_option('evo_dyn_css');
				if(!empty($dynamic_css)){
					echo '<style type ="text/css" rel="eventon_dynamic_styles">'.$dynamic_css.'</style>';
				}				
			}else{
				wp_enqueue_style( 'eventon_dynamic_styles');
			}
		}

		public function enqueue_evo_scripts(){
			$this->enqueue_evo_gmaps();
			$this->load_default_evo_scripts();
		}		

		public function enqueue_evo_gmaps(){
			wp_enqueue_script('evcal_gmaps');
			wp_enqueue_script('eventon_gmaps');
			wp_enqueue_script('eventon_init_gmaps');
		}
		public function load_default_evo_scripts(){
			//wp_enqueue_script('add_to_cal');
			wp_enqueue_script('evcal_functions');
			wp_enqueue_script('evo_mobile');
			wp_enqueue_script('evo_mouse');
			wp_enqueue_script('evcal_ajax_handle');			
			
			do_action('eventon_enqueue_scripts');

			// map enqueueing is done in calendar shell files
			
		}
		public function load_default_evo_styles(){
			$opt= $this->evo_options;
			if(empty($opt['evo_googlefonts']) || $opt['evo_googlefonts'] =='no'){
				wp_enqueue_style( 'evcal_google_fonts' );
			}

			wp_enqueue_style( 'evcal_cal_default');	
			wp_enqueue_style( 'evo_addon_styles');	
			
			if(empty($opt['evo_fontawesome']) || $opt['evo_fontawesome'] =='no')
				wp_enqueue_style( 'evo_font_icons' );

			// Addon styles will be loaded to page at this point
			do_action('eventon_enqueue_styles');

			$this->load_dynamic_evo_styles();
		}
		public function load_evo_scripts_styles(){
			$this->load_default_evo_scripts();
			$this->load_default_evo_styles();
		}
		public function evo_styles(){
			add_action('wp_head', array($this, 'load_default_evo_scripts'));
		}

	// check if members only
		function is_member_only($shortcode_args){
			 return ( 
			 	($shortcode_args['members_only']=='yes' && is_user_logged_in()) ||
			 	$shortcode_args['members_only']=='no' || empty($shortcode_args['members_only'])
			 )? true: false;
		}
		function nonMemberCalendar(){
			return __('You must login first to see calendar','eventon');
		}

	// language
		function lang($evo_options = '', 
			$field, 
			$default_val, 
			$lang = ''
		){
			global $eventon;
				
			$evo_options = (!empty($evo_options))? $evo_options: $this->evo_lang_opt;
			
			// check for language preference
			if(!empty($lang)){
				$_lang_variation = $lang;
			}else{
				$shortcode_arg = $eventon->evo_generator->shortcode_args;
				$_lang_variation = (!empty($shortcode_arg['lang']))? $shortcode_arg['lang']:'L1';
			}
			
			$new_lang_val = (!empty($evo_options[$_lang_variation][$field]) )?
				stripslashes($evo_options[$_lang_variation][$field]): $default_val;
				
			return $new_lang_val;
		}

	// Body class
		function body_classes($classes){
			if(!empty($this->evo_options['evo_rtl']) && $this->evo_options['evo_rtl']=='yes')
				$classes[] = 'evortl';
			return $classes;
		}

	// Event Type Taxonomies
		function get_localized_event_tax_names($lang='', $options='', $options2=''
		){
			$output ='';

			$options = (!empty($options))? $options: get_option('evcal_options_evcal_1');
			$options2 = (!empty($options2))? $options2: get_option('evcal_options_evcal_2');
			$_lang_variation = (!empty($lang))? $lang:'L1';

			
			// foreach event type upto activated event type categories
			for( $x=1; $x< (evo_get_ett_count($options)+1); $x++){
				$ab = ($x==1)? '':$x;

				$_tax_lang_field = 'evcal_lang_et'.$x;

				// check on eventon language values for saved name
				$lang_name = (!empty($options2[$_lang_variation][$_tax_lang_field]))? 
					stripslashes($options2[$_lang_variation][$_tax_lang_field]): null;

				// conditions
				if(!empty($lang_name)){
					$output[$x] = $lang_name;
				}else{
					$output[$x] = (!empty($options['evcal_eventt'.$ab]))? $options['evcal_eventt'.$ab]:'Event Type '.$ab;
				}			
			}
			return $output;
		}
		function get_localized_event_tax_names_by_slug($slug, $lang=''){
			$options = get_option('evcal_options_evcal_1');
			$options2 = get_option('evcal_options_evcal_2');
			$_lang_variation = (!empty($lang))? $lang:'L1';

			// initial values
			$x = ($slug=='event_type')?'1': (substr($slug,-1));
			$ab = ($x==1)? '':$x;
			$_tax_lang_field = 'evcal_lang_et'.$x;

			// check on eventon language values for saved name
			$lang_name = (!empty($options2[$_lang_variation][$_tax_lang_field]))? 
				stripslashes($options2[$_lang_variation][$_tax_lang_field]): null;

			// conditions
			if(!empty($lang_name)){
				return $lang_name;
			}else{
				return (!empty($options['evcal_eventt'.$ab]))? $options['evcal_eventt'.$ab]:'Event Type '.$ab;
			}	

		}

	// facebook header to single events pages
		function fb_headers(){
			global $post;
			//print_r($post);

			if($post && $post->post_type=='ajde_events'):
				//$thumbnail = get_the_post_thumbnail($post->ID, 'medium');
				$img_id =get_post_thumbnail_id($post->ID);
				$pmv = get_post_meta($post->ID);
				
				ob_start();
					$excerpt = eventon_get_normal_excerpt( $post->post_content, 20);
					//$excerpt = $post->post_content;
				?>
				<meta name="robots" content="all"/>
				<meta property="description" content="<?php echo $excerpt;?>" />
				<meta property="og:type" content="event" /> 
				<meta property="og:title" content="<?php echo $post->post_title;?>" />
				<meta property="og:url" content="<?php echo get_permalink($post->ID);?>" />
				<meta property="og:description" content="<?php echo $excerpt;?>" />
				<?php if($img_id!=''): 
					$img_src = wp_get_attachment_image_src($img_id,'medium');
				?>
					<meta property="og:image" content="<?php echo $img_src[0];?>" /> 
					<meta property="og:image:width" content="<?php echo $img_src[1];?>" /> 
					<meta property="og:image:height" content="<?php echo $img_src[2];?>" /> 
				<?php endif;?>
				<?php
				// organizer as author
					if(!empty($pmv['evcal_organizer']))
						echo '<meta property="article:author" content="'.$pmv['evcal_organizer'][0].'" />';

				echo apply_filters('evo_facebook_header_metadata', ob_get_clean());
			endif;
		}
		// add eventon single event card field to filter
			function eventcard_array($array, $pmv, $eventid, $__repeatInterval){
				$newarray = $array;
				$newarray['evosocial']= array(
					'event_id' => $eventid,
					'value'=>'tt',
					'__repeatInterval'=>(!empty($__repeatInterval)? $__repeatInterval:0)
				);
				return $newarray;
			}
			function eventcard_adds($array){
				$array[] = 'evosocial';
				return $array;
			}
		// ADD SOcial media to event card
			function add_social_media_to_eventcard($object, $helpers){
				global $eventon;

				$__calendar_type = $eventon->evo_generator->__calendar_type;
				$evo_opt = $helpers['evOPT'];

				$event_id = $object->event_id;
				$repeat_interval = $object->__repeatInterval;
				$event_post_meta = get_post_custom($event_id);

				// Event Times
					$dateTime = new evo_datetime();
					$unixes = $dateTime->get_correct_event_repeat_time($event_post_meta, $repeat_interval);
					$datetime_string = $dateTime->get_formatted_smart_time($unixes['start'], $unixes['end'],$event_post_meta);

				
				// check if social media to show or not
				if( (!empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='yes' && $__calendar_type=='single') 
					|| ( empty($evo_opt['evosm_som']) ) || ( !empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='no' ) ){
					
					$post_title = get_the_title($event_id);
					$event_permalink = get_permalink($event_id);

					// Link to event
						$permalink_connector = (strpos($event_permalink, '?')!== false)? '&':'?';
						$permalink = (!empty($object->__repeatInterval) && $object->__repeatInterval>0)? 
							$event_permalink.$permalink_connector.'ri='.$object->__repeatInterval: $event_permalink;
						$encodeURL = urlencode($permalink);

					// thumbnail
						$img_id = get_post_thumbnail_id($event_id);
						$img_src = ($img_id)? wp_get_attachment_image_src($img_id,'thumbnail'): false;

					// event details
						$summary = $eventon->frontend->filter_evo_content(get_post_field('post_content',$event_id));

					$title 		= str_replace('+','%20',urlencode($post_title));
					$titleCOUNT = $post_title;
					$summary = (!empty($summary)? urlencode(eventon_get_normal_excerpt($summary, 16)): '--');
					$imgurl = $img_src? urlencode($img_src[0]):'';
					
					// social media array

					$fb_js = "javascript:window.open(this.href, '', 'left=50,top=50,width=600,height=350,toolbar=0');return false;";
					$tw_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";
					$gp_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;";

					//http://www.facebook.com/sharer.php?s=100&p[url]=PERMALINK&p[title]=TITLE&display=popup" data-url="PERMALINK
					$social_sites = apply_filters('evo_se_social_media', array(
											
						'FacebookShare'    => array(
							'key'=>'eventonsm_fbs',
							'counter' =>1,
							'favicon' => 'likecounter.png',
							'url' => '<a class=" evo_ss" target="_blank" onclick="'.$fb_js.'"
								href="http://www.facebook.com/sharer.php?u=PERMALINK"><i class="fa fa-facebook"></i></a>',
						),
						'Twitter'    => array(
							'key'=>'eventonsm_tw',
							'counter' =>1,
							'favicon' => 'twitter.png',
							'url' => '<a class="tw evo_ss" onclick="'.$tw_js.'" href="http://twitter.com/share?text=TITLECOUNT&#32;-&#32;&url=PERMALINK" title="Share on Twitter" rel="nofollow" target="_blank" data-url="PERMALINK"><i class="fa fa-twitter"></i></a>',
						),
						'LinkedIn'=> array(
							'key'=>'eventonsm_ln',
							'counter'=>1,'favicon' => 'linkedin.png',
							'url' => '<a class="li evo_ss" href="http://www.linkedin.com/shareArticle?mini=true&url=PERMALINK&title=TITLE&summary=SUMMARY" target="_blank"><i class="fa fa-linkedin"></i></a>',
						),
						'Google' => Array (
							'key'=>'eventonsm_gp',
							'counter' =>1,'favicon' => 'google.png',
							'url' => '<a class="gp evo_ss" href="https://plus.google.com/share?url=PERMALINK" 
								onclick="'.$fb_js.'" target="_blank"><i class="fa fa-google-plus"></i></a>'
						),
						'Pinterest' => Array (
							'key'=>'eventonsm_pn',
							'counter' =>1,
							'favicon' => 'pinterest.png',
							'url' => '<a class="pn evo_ss" href="http://www.pinterest.com/pin/create/button/?url=PERMALINK&media=IMAGEURL&description=SUMMARY"
						        data-pin-do="buttonPin" data-pin-config="above" target="_blank"><i class="fa fa-pinterest"></i></a>'
						),'EmailShare' => Array (
							'key'=>'eventonsm_email',						
							'url' => '<a class="em evo_ss" href="HREF" target="_blank"><i class="fa fa-envelope"></i></a>'
						)						
					));
					
					$sm_count = 0;
					$output_sm='';
					
					// foreach sharing option
					foreach($social_sites as $sm_site=>$sm_site_val){
						if(!empty($evo_opt[$sm_site_val['key']]) && $evo_opt[$sm_site_val['key']]=='yes'){
							// for emailing
							if($sm_site=='EmailShare'){
								$url = $sm_site_val['url'];

								$mailtocontent = '';
								foreach( apply_filters('evo_emailshare_data', array(
									'event_name'=> array('label'=> evo_lang('Event Name'), 'value'=>$title),
									'event_date'=> array('label'=> evo_lang('Event Date'), 'value'=>$datetime_string),
									'link'=> array('label'=> evo_lang('Link'), 'value'=>$encodeURL),

								)) as $key=>$data){
									$mailtocontent .= $data['label'] .': '. $data['value'] . '%0A';
								}
								
								// removed urlencode on title
								$title 		= str_replace('+','%20',($post_title));

								$href_ = 'mailto:?subject='.$title.'&body='.$mailtocontent;
								$url = str_replace('HREF', $href_, $url);

								$link= "<div class='evo_sm ".$sm_site."'>".$url."</div>";
							}else{

								// check interest
								if( $sm_site=='Pinterest' && empty($imgurl)) continue;

								$site = $sm_site;
								$url = $sm_site_val['url'];
								
								$url = str_replace('TITLECOUNT', $titleCOUNT, $url);
								$url = str_replace('TITLE', $title, $url);			
								$url = str_replace('PERMALINK', $encodeURL, $url);
								$url = str_replace('SUMMARY', $summary, $url);
								$url = str_replace('IMAGEURL', $imgurl, $url);
								
								$linkitem = '';
								
								$style='';
								$target='';
								$href = $url;
								
								$link= "<div class='evo_sm ".$sm_site."'>".$href."</div>";
							}

							// Output
							$link = apply_filters('evo_single_process_sharable',$link);
							$output_sm.=$link;
							$sm_count++;
						}
					}
					
					if($sm_count>0){
						return "<div class='bordb evo_metarow_socialmedia evcal_evdata_row'>".$output_sm."<div class='clear'></div></div>";
					}
				}
			
				$eventon->evo_generator->__calendar_type ='default';		
			}

	// Side bars
		// create a single event sidebar
		function register_se_sidebar(){			
			$opt = $this->evo_options;

			if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
				register_sidebar(array(
				  'name' => __( 'Single Event Sidebar' ),
				  'id' => 'evose_sidebar',
				  'description' => __( 'Widgets in this area will be shown on the right-hand side of single events page.' ),
				  'before_title' => '<h3 class="widget-title">',
				  'after_title' => '</h3>'
				));
			}
		}

	// Schedule 
	// initiated in install
		function evo_perform_trash_past_events(){

			if(empty($this->evopt1['evcal_move_trash']) || $this->evopt1['evcal_move_trash']!= 'yes') return;
			eventon_trash_past_events();
		}

	// EMAILING	
		// get email parts
			public function get_email_part($part){
				global $eventon;

				$file_name = 'email_'.$part.'.php';

				$paths = array(
					0=> TEMPLATEPATH.'/'.$eventon->template_url.'templates/email/',
					1=> AJDE_EVCAL_PATH.'/templates/email/',
				);

				foreach($paths as $path){				
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}//echo($path.$file_name.'<br/>');
				}

				ob_start();

				include($template);

				return ob_get_clean();
			}

		// Get email body parts
		// to pull full email templates
			public function get_email_body($part, $def_location, $args='', $paths=''){
				global $eventon;

				ob_start();

				$file_location = EVO()->template_locator(
					$part.'.php', 
					$def_location,
					'templates/email/'
				);
				include($file_location);

				return ob_get_clean();
				
				/*
				$file_name = $part.'.php';
				global $eventon;

				if(empty($paths) && !is_array($paths)){
					$paths = array(
						0=> TEMPLATEPATH.'/'.$eventon->template_url.'templates/email/',
						1=> $def_location,
					);
				}

				foreach($paths as $path){	
					// /echo $path.$file_name.'<br/>';			
					if(file_exists($path.$file_name) ){	
						$template = $path.$file_name;	
						break;
					}
				}

				ob_start();

				if($template)
					include($template);

				return ob_get_clean();
				*/
			}
	// front-end website
		/** Output generator to aid debugging. */
			public function generator() {
				global $eventon;
				echo "\n\n" . '<!-- EventON Version -->' . "\n" . '<meta name="generator" content="EventON ' . esc_attr( $eventon->version ) . '" />' . "\n\n";
			}

	// CONTENT FILTERING
		function filter_evo_content($str){
			global $wp_embed;

			if(empty($this->evo_options['evo_content_filter']) || $this->evo_options['evo_content_filter']=='evo'){
				$str = $wp_embed->autoembed($str);
				$str = wptexturize($str);
				$str = convert_smilies($str);
				$str = convert_chars($str);
				$str = wpautop($str);
				$str = shortcode_unautop($str);
				$str = prepend_attachment($str);
				$str = do_shortcode($str);
				return $str;
			}elseif($this->evo_options['evo_content_filter']=='def'){
				return apply_filters('the_content', $str);
				
			}else{// no filter at all
				return $str;
			}
			
		}

	// footer for page with lightbox
		function footer_code(){
			$lightboxWindows = apply_filters('evo_frontend_lightbox', array(
				'eventcard'=> array(
					'id'=>'',
					'classes'=>'eventon_events_list',
					'CLin'=>'eventon_list_event evo_pop_body evcal_eventcard',
					'CLclosebtn'
				)
			));

			if(count($lightboxWindows)>0){

				$display = (evo_settings_val('evo_load_scripts_only_onevo', $this->evo_options) && !evo_settings_val('evo_load_all_styles_onpages', $this->evo_options))? 'none':'block';

				echo "<div class='evo_lightboxes' style='display:{$display}'>";
				foreach($lightboxWindows as $key=>$lb){
					?>
					<div class='evo_lightbox <?php echo $key;?> <?php echo !empty($lb['classes'])? $lb['classes']:'';?>' id='<?php echo !empty($lb['id'])? $lb['id']:'';?>' >
						<div class="evo_content_in">													
							<div class="evo_content_inin">
								<div class="evo_lightbox_content">
									<a class='evolbclose <?php echo !empty($lb['CLclosebtn'])? $lb['CLclosebtn']:'';?>'>X</a>
									<div class='evo_lightbox_body <?php echo !empty($lb['CLin'])? $lb['CLin']:'';?>'></div>
								</div>
							</div>							
						</div>
					</div>
					<?php
				} // endforeach
				echo "</div>";
			}

		}
}