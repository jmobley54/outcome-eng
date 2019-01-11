<?php
/**
 * Search Capabilities of events through out eventon
 * @version 2.5.3
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class evo_search{

	public function __construct(){
		add_shortcode('add_eventon_search', array($this, 'search_content'), 10, 1);
		add_filter('eventon_shortcode_popup',array($this,'add_shortcode_options'), 10, 1);

		// frontend
		$this->options = get_option('evcal_options_evcal_1');

		add_filter('evo_cal_above_header_btn', array($this, 'header_search_button'), 10, 2);
		add_filter('evo_cal_above_header_content', array($this, 'header_search_bar'), 10, 2);
		add_action('evo_cal_footer', array($this, 'remove_search_bar'), 10);

		//shortcodes
		add_filter('eventon_shortcode_defaults', array($this,'add_shortcode_defaults'), 10, 1);	

		// include events in search
		add_action( 'pre_get_posts', array($this,'include_events_search'),10,1 );
		add_filter( 'evo_cpt_search_visibility', array($this, 'enable_events_search'), 10,1 );	

		add_filter( 'posts_search', array($this, 'advanced_custom_search'), 500, 2 );	
	}

// shortcode content
		function search_content($atts){
			global $eventon_sr, $eventon;
			ob_start(); 

			// enqueue required eventon scripts
			$eventon->frontend->load_default_evo_scripts();
			wp_enqueue_script('eventon_gmaps');
			wp_enqueue_script('eventon_init_gmaps');
			wp_enqueue_script('evcal_gmaps');

			$defaults = array(
				'event_type'=>'',
				'event_type_2'=>'',
				'number_of_months'=>12,
				'search_all'=>'no',
				'lang'=>'L1',
			);

			$data = '';
			foreach($defaults as $def=>$val){
				$val = !empty($atts[$def])? $atts[$def]: $val;
				if(empty($val)) continue;
				$data .= 'data-'.$def .'="' . ($val) .'"';
			}
		
			?>
			<div id='evo_search' class='EVOSR_section '>
				<div class="evo_search_entry">
					<p class='evosr_search_box' >
						<input type="text" placeholder='<?php echo evo_lang_get('evoSR_001a','Search Calendar Events');?>' data-role="none">
						<a class='evo_do_search'><i class="fa fa-search"></i></a>
						<span class="evosr_blur"></span>
						<span class="evosr_blur_process"></span>
						<span class="evosr_blur_text"><?php echo evo_lang_get('evoSR_002','Searching');?></span>
						<span style="display:none" class='data' <?php echo $data;?>></span>
					</p>
					<p class='evosr_msg' style='display:none'><?php echo evo_lang_get('evoSR_003','What do you want to search for?');?></p>
				</div>
				<p class="evo_search_results_count" style='display:none'><span>10</span> <?php echo evo_lang_get('evoSR_004','Event(s) found');?></p>
				<div class="evo_search_results"></div>
			</div>
			<?php
			return ob_get_clean();
		}

		function add_shortcode_options($shortcode_array){
			global $evo_shortcode_box;
				
				$new_shortcode_array = array(
					array(
						'id'=>'s_SR',
						'name'=>'Search Box',
						'code'=>'add_eventon_search',
						'variables'=>array(
							array(
								'name'=>'<i>'.__('NOTE: This will allow you to drop an interactive event search field that allow users to search through all current events. You can further filter search results with below options.','eventon') .'</i>',
								'type'=>'note',							
							),
							$evo_shortcode_box->shortcode_default_field('event_type'),
							$evo_shortcode_box->shortcode_default_field('event_type_2'),
							$evo_shortcode_box->shortcode_default_field('lang'),
							$evo_shortcode_box->shortcode_default_field('number_of_months'),
							array(
								'name'=>'Search all events (past and current)',
								'type'=>'YN',
								'guide'=>'Setting this will disregard number of months value and will search in all the events.',
								'default'=>'no',
								'var'=>'search_all',
							)
						)
					)
				);

				return array_merge($shortcode_array, $new_shortcode_array);
		}

// frotnend
	function list_searcheable_acf(){
	  	$list_searcheable_acf = array("title", "evcal_subtitle", "excerpt_short", "excerpt_long");
	  	return $list_searcheable_acf;
	}
	function advanced_custom_search( $where, $wp_query ) {
	    global $wpdb, $eventon;

	    if(!evo_settings_check_yn( $this->options, 'EVOSR_advance_search')) return $where;
	   
	    if ( empty( $where ))  return $where;

	    //if( is_admin()) return $where;

	    // restrict this only to event post search
	    if( $wp_query->query['post_type']!= 'ajde_events') return $where;
	 
	    // get search expression
	    $terms = $wp_query->query_vars[ 's' ];

	    // explode search expression to get search terms
	    $exploded = explode( ' ', $terms );
	    if( $exploded === FALSE || count( $exploded ) == 0 )  $exploded = array( 0 => $terms );
	         
	    // reset search in order to rebuilt it as we whish
	    $where = '';
	    $tableprefix = $wpdb->prefix;

	    // get searcheable_acf, a list of advanced custom fields you want to search content in
	    $list_searcheable_acf = $this->list_searcheable_acf();
	    foreach( $exploded as $tag ) :
	        $where .= " 
	          AND (
	            (".$tableprefix."posts.post_title LIKE '%$tag%')
	            OR (".$tableprefix."posts.post_content LIKE '%$tag%')
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."postmeta
		              WHERE post_id = ".$tableprefix."posts.ID
		                AND (";
	        foreach ($list_searcheable_acf as $searcheable_acf) :
	          if ($searcheable_acf == $list_searcheable_acf[0]):
	            $where .= " (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
	          else :
	            $where .= " OR (meta_key LIKE '%" . $searcheable_acf . "%' AND meta_value LIKE '%$tag%') ";
	          endif;
	        endforeach;
		        $where .= ")
	            )
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."comments
	              WHERE comment_post_ID = ".$tableprefix."posts.ID
	                AND comment_content LIKE '%$tag%'
	            )
	            OR EXISTS (
	              SELECT * FROM ".$tableprefix."terms
	              INNER JOIN ".$tableprefix."term_taxonomy
	                ON ".$tableprefix."term_taxonomy.term_id = ".$tableprefix."terms.term_id
	              INNER JOIN wp_term_relationships
	                ON ".$tableprefix."term_relationships.term_taxonomy_id = ".$tableprefix."term_taxonomy.term_taxonomy_id
	              WHERE (
	          		taxonomy = 'event_location'
	            		OR taxonomy = 'event_organizer'          		
	            		OR taxonomy = 'event_speaker'          		
	            		OR taxonomy = 'event_type'
	            		OR taxonomy = 'event_type_2'
	            		OR taxonomy = 'event_type_3'
	          		)
	              	AND object_id = ".$tableprefix."posts.ID
	              	AND ".$tableprefix."terms.name LIKE '%$tag%'
	            )
	        )";
	    endforeach;

	    return $where;
	}

	// include events in default wordpress search 
		function enable_events_search($value){
			if(!evo_settings_val('EVOSR_default_search',$this->options)) return $value;
			return false;
		}
		function include_events_search($query){

			if(!evo_settings_val('EVOSR_default_search',$this->options)) return $query;

			// Check to verify it's search page
			if( $query->is_search ) {
				// Get post types
				$post_types = get_post_types(array('public' => true, 'exclude_from_search' => false), 'objects');
				$searchable_types = array();
				
				// Add available post types
				if( $post_types ) {
					foreach( $post_types as $type) {
						$searchable_types[] = $type->name;
					}
				}

				if(!in_array('ajde_events', $searchable_types)) 
					$searchable_types[] = 'ajde_events';

				$query->set( 'post_type', $searchable_types );
			}
			return $query;
		}

		// evosr_disable_search
	// include search in header section
		function header_search_button($array, $args){
			$opt = $this->options;

			//echo $args['search'].' '.$opt['evosr_default_search_on'];

			// check if default search is off && shortcode is no
			if( (!evo_settings_check_yn($opt, 'evosr_default_search_on') && !evo_settings_check_yn($args, 'search')) || 
				(evo_settings_check_yn($opt, 'evosr_default_search_on') && (!empty($args['search']) && $args['search']=='no'))
			)
				return $array;
						
			if(!empty($opt['EVOSR_showfield']) && $opt['EVOSR_showfield']=='yes'){
				return $array;
			}else{
				$new['evo-search']='';
				$array = array_merge($new, $array);
			}

			return $array;
		}

	// header search bar
		function header_search_bar($array, $args){
			$opt = $this->options;

			// check if search is enabled from settings or via shortcode
			if( (!evo_settings_check_yn($opt, 'evosr_default_search_on') && !evo_settings_check_yn($args, 'search')) || 
				(evo_settings_check_yn($opt, 'evosr_default_search_on') && (!empty($args['search']) && $args['search']=='no'))
			)
				return $array;

			$hidden = (!empty($opt['EVOSR_showfield']) && $opt['EVOSR_showfield']=='yes')? 
				'':'evo_hidden';
			ob_start();
			?>					
				<div class='evo_search_bar <?php echo $hidden;?>'>
					<div class='evo_search_bar_in' >
						<input type="text" placeholder='<?php echo eventon_get_custom_language('', 'evoSR_001', 'Search Events');?>' data-role="none"/>
						<a class="evosr_search_btn"><i class="fa fa-search"></i></a>
					</div>
				</div>

			<?php
			$content = ob_get_clean();
			$array['evo-search']= $content;
			return $array;
		}
	// remove search bar
		function remove_search_bar(){
			//remove_filter('evo_cal_above_header_btn',array($this, 'header_search_button'));
			//remove_filter('evo_cal_above_header_content',array($this, 'header_search_bar'));
		}

	//shortcode defaults
		function add_shortcode_defaults($arr){
			return array_merge($arr, array(
				'search'=>'',
			));		
		}


}
new evo_search();