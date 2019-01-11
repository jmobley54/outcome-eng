<?php


if ( ! isset( $content_width ) )
	$content_width = 960;
	
	
/*-----------------------------------------------------------------------------------
- Start Themnific Functions - Please refrain from editing this section 
----------------------------------------------------------------------------------- */

// Set path to Themnific Framework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/functions/';

// Framework
require_once ($functions_path . 'admin-init.php');						// Framework Init

// Theme specific functionality
require_once ($includes_path . 'theme-options.php'); 					// Options panel settings and custom settings
require_once ($includes_path . 'theme-actions.php');					// Theme actions & user defined hooks
require_once ($includes_path . 'theme-scripts.php'); 					// Load JavaScript via wp_enqueue_script

if (get_option('themnific_slider_gallery') <> "false") {require_once ($functions_path . 'theme-gallery.php'); } // Replace [gallery] shortcode with slider


//Add Custom Post Types
require_once ($includes_path . 'posttypes/ptype-portfolio.php'); 		// portfolio post type
require_once ($includes_path . 'posttypes/ptype-layout.php'); 			// layout post type
require_once ($includes_path . 'posttypes/ptype-slider.php'); 			// slider post type
require_once ($includes_path . 'posttypes/ptype-services.php'); 		// services post type
require_once ($includes_path . 'posttypes/ptype-staff.php'); 			// staff post type
require_once ($includes_path . 'posttypes/ptype-clients.php'); 			// clients post type
require_once ($includes_path . 'posttypes/ptype-pricing-tabs.php'); 	// pricing tabs post type
require_once ($includes_path . 'posttypes/post-metabox.php'); 			// custom meta box

/*-----------------------------------------------------------------------------------
- Loads all the .php files found in /admin/widgets/ directory
----------------------------------------------------------------------------------- */

	$widgets_dir = get_template_directory() . "/functions/widgets/";
    
    if (@is_dir($widgets_dir)) {
		$widgets_dh = opendir($widgets_dir);
		while (($widgets_file = readdir($widgets_dh)) !== false) {
  	
			if(strpos($widgets_file,'.php') && $widgets_file != "widget-blank.php") {
				include_once($widgets_dir . $widgets_file);
			
			}
		}
		closedir($widgets_dh);
	}



// Post thumbnail support
if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');
	add_image_size('block_2', 640, 440, true ); //(cropped)
	add_image_size('format-image', 645, 9999);
	add_image_size('format-single', 645, 300, true); //(cropped)
	add_image_size('folio', 290, 280, true ); //(cropped)
	add_image_size('folio4', 208, 190, true ); //(cropped)
	add_image_size('folio_carousel', 293, 310, true ); //(cropped)
	add_image_size('folio_slider', 662, 9999 ); //(cropped)
	add_image_size('item_blog', 235, 150, true ); //(cropped)
	add_image_size('clients', 197, 140, true); //(cropped)
	add_image_size('staff', 212, 245, true); //(cropped)
	add_image_size('tabs', 55, 55, true); //(cropped)
}

function thumb_url(){
$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), array( 2100,2100 ));
return $src[0];
}


// Add Theme Support Functions
add_editor_style();
add_theme_support( 'post-formats', array( 'video','audio', 'gallery', 'image', 'quote', 'link' ) );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'custom-background' );
add_theme_support( 'woocommerce' );

// widgets
if ( function_exists('register_sidebar') ) 
{ 

	// sidebar widget
	register_sidebar(array('name' => 'Sidebar','id' => 'sidebar-1','before_widget' => '','after_widget' => '','before_title' => '<h2>','after_title' => '</h2>')); 


	//footer widgets
	register_sidebar(array('name' => 'Footer 1','id' => 'footer-1','before_widget' => '','after_widget' => '','before_title' => '<h2 class="widget"><span>','after_title' => '</span></h2>'));   
	register_sidebar(array('name' => 'Footer 2','id' => 'footer-2','before_widget' => '','after_widget' => '','before_title' => '<h2 class="widget"><span>','after_title' => '</span></h2>'));
	register_sidebar(array('name' => 'Footer 3','id' => 'footer-3','before_widget' => '','after_widget' => '','before_title' => '<h2 class="widget"><span>','after_title' => '</span></h2>'));
}

// Make theme available for translation
	load_theme_textdomain( 'themnific', get_template_directory() . '/lang' );



// Shordcodes
require_once (get_template_directory().'/functions/admin-shortcodes.php' );				// Shortcodes
require_once (get_template_directory().'/functions/admin-shortcode-generator.php' ); 	// Shortcode generator 
require_once (get_template_directory().'/functions/admin-shortcodes-loops.php' ); 		// Shortcode queries 

// Use shortcodes in text widgets.
add_filter('widget_text', 'do_shortcode');

// navigation menu
function register_main_menus() {
	register_nav_menus(
		array(
			'home-menu' => "Scroll Menu" ,
			'main-menu' => "Main Menu"
		)
	);
};
if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );



// Shorten post title
function short_title($after = '', $length) {
	$mytitle = explode(' ', get_the_title(), $length);
	if (count($mytitle)>=$length) {
		array_pop($mytitle);
		$mytitle = implode(" ",$mytitle). $after;
	} else {
		$mytitle = implode(" ",$mytitle);
	}
	return $mytitle;
}


// icons - font awesome
function tmnf_icon() {
	
	if(has_post_format('video')) {return '<i class="fa fa-play-circle"></i>';
	}elseif(has_post_format('audio')) {return '<i class="fa fa-music"></i>';
	}elseif(has_post_format('gallery')) {return '<i class="fa fa-picture-o"></i>';	
	}elseif(has_post_format('link')) {return '<i class="fa fa-sign-out"></i>';	
	}elseif(has_post_format('image')) {return '<i class="fa fa-camera"></i></i>';		
	}elseif(has_post_format('quote')) {return '<i class="fa fa-quote-right"></i>';	
	} else {'';}	
	
}


// icons ribbons - font awesome
function tmnf_ribbon() {
	if(has_post_format('video')) {return '<span class="ribbon video-ribbon"></span><span class="ribbon_icon"><i class="fa fa-play-circle"></i></span>';
	}elseif(has_post_format('audio')) {return '<span class="ribbon audio-ribbon"></span><span class="ribbon_icon"><i class="fa fa-microphone"></i></span>';
	}elseif(has_post_format('gallery')) {return '<span class="ribbon gallery-ribbon"></span><span class="ribbon_icon"><i class="fa fa-picture-o"></i></span>';
	}elseif(has_post_format('link')) {return '<span class="ribbon link-ribbon"></span><span class="ribbon_icon"><i class="fa fa-link"></i></span>';
	}elseif(has_post_format('image')) {return '<span class="ribbon image-ribbon"></span><span class="ribbon_icon"><i class="fa fa-camera"></i></span>';
	}elseif(has_post_format('quote')) {return '<span class="ribbon quote-ribbon"></span><span class="ribbon_icon"><i class="fa fa-quote-right"></i></span>';
	} else {}	
	
}



// managed excerpt

function tmnf_excerptlength_teaser($length) {
    return 90;
    }
function tmnf_excerptlength_index($length) {
    return 13;
    }
function tmnf_excerptmore($more) {
    return '...';
    }

add_filter( 'wp_get_attachment_link', 'gallery_prettyPhoto');


// new excerpt function

function tmnf_excerpt($length_callback='', $more_callback='') {
    global $post;
    if(function_exists($length_callback)){
    add_filter('excerpt_length', $length_callback);
    }
    if(function_exists($more_callback)){
    add_filter('excerpt_more', $more_callback);
    }
    $output = get_the_excerpt();
    $output = apply_filters('wptexturize', $output);
    $output = apply_filters('convert_chars', $output);
    $output = '<p>'.$output.'</p>';
    echo $output;
    }



// Old Shorten Excerpt text for use in theme
function themnific_excerpt($text, $chars = 1620) {
	$text = $text." ";
	$text = substr($text,0,$chars);
	$text = substr($text,0,strrpos($text,' '));
	$text = $text."";
	return $text;
}

function trim_excerpt($text) {
     $text = str_replace('[', '', $text);
     $text = str_replace(']', '', $text);
     return $text;
    }
add_filter('get_the_excerpt', 'trim_excerpt');



// automatically add prettyPhoto rel attributes to embedded images

function gallery_prettyPhoto ($content) {

	// add checks if you want to add prettyPhoto on certain places (archives etc).

	return str_replace("<a", "<a rel='prettyPhoto[gallery]'", $content);

}

function insert_prettyPhoto_rel($content) {
	$pattern = '/<a(.*?)href="(.*?).(bmp|gif|jpeg|jpg|png)"(.*?)>/i';
  	$replacement = '<a$1href="$2.$3" rel=\'prettyPhoto\'$4>';
	$content = preg_replace( $pattern, $replacement, $content );
	return $content;
}
add_filter( 'the_content', 'insert_prettyPhoto_rel' );


// pagination

function pagination($prev = '&laquo;', $next = '&raquo;') {
    global $wp_query, $wp_rewrite;
    $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
    $pagination = array(
        'base' => @add_query_arg('paged','%#%'),
        'format' => '',
        'total' => $wp_query->max_num_pages,
        'current' => $current,
        'prev_text' => __('Previous','themnific'),
        'next_text' => __('Next','themnific'),
        'type' => 'plain'
);
    if( $wp_rewrite->using_permalinks() )
        $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

    if( !empty($wp_query->query_vars['s']) )
        $pagination['add_args'] = array( 's' => get_query_var( 's' ) );

    echo paginate_links( $pagination );
};

// get featured image on posts screen  
function tmnf_get_featured_image($post_ID) {  
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);  
    if ($post_thumbnail_id) {  
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'thumbnail');  
        return $post_thumbnail_img[0];  
    }  
} 
    // ADD NEW COLUMN  
    function tmnf_columns_head($defaults) {  
        $defaults['featured_image'] = 'Featured Image';  
        return $defaults;  
    }  
    // SHOW THE FEATURED IMAGE  
    function tmnf_columns_content($column_name, $post_ID) {  
        if ($column_name == 'featured_image') {  
            $post_featured_image = tmnf_get_featured_image($post_ID);  
            if ($post_featured_image) {  
                echo '<img style=" width:100px;" src="' . $post_featured_image . '" />';  
            }  
        }  
    }  
add_filter('manage_posts_columns', 'tmnf_columns_head');  
add_action('manage_posts_custom_column', 'tmnf_columns_content', 10, 2); 

//Breadcrumbs
function the_breadcrumb() {
	if (!is_home()) {

		echo '<a href="'. home_url().'">';
		echo '<i class="fa fa-home"></i> ';
		echo "</a> &raquo; ";
		if (is_category() || is_single()) {
		the_category(', ');
		if (is_single()) {
		echo " &raquo; ";
	echo short_title('...', 6);
	}
	} elseif (is_page()) {
	echo the_title();}
	}
}

// srcset hotfix
function tmnf_disable_srcset( $sources ) {return false;}
add_filter( 'wp_calculate_image_srcset', 'tmnf_disable_srcset' );

?>