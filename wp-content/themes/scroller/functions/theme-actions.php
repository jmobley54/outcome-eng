<?php 


// Register Styles
function register_styles(){
	
	wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css' );
	
	wp_register_style('prettyPhoto', get_template_directory_uri() .	'/styles/prettyPhoto.css');
		wp_enqueue_style( 'prettyPhoto');
}
add_action('themnific_head', 'register_styles');


/*-----------------------------------------------------------------------------------*/
/* Custom functions */
/*-----------------------------------------------------------------------------------*/


	global $themnific_options;
	$output = '';

// Add custom styling
add_action('wp_head','themnific_custom_styling');
function themnific_custom_styling() {
	
	// Get options
	$home = home_url();
	$home_theme  = get_template_directory_uri();
	
	$sec_body_color = get_option('themnific_custom_color');
	$thi_body_color = get_option('themnific_thi_body_color');
	$for_body_color = get_option('themnific_for_body_color');
	$body_color = get_option('themnific_body_color');
	$text_color = get_option('themnific_text_color');
	$body_color_sec = get_option('themnific_body_color_sec');
	$thi_text_color = get_option('themnific_thi_text_color');
	$link = get_option('themnific_link_color');
	$hover = get_option('themnific_link_hover_color');
	$sec_hover = get_option('themnific_sec_link_hover_color');
	$body_bg = get_option('themnific_body_bg');
	$body_bg_sec = get_option('themnific_body_bg_sec');
	$border = get_option('themnific_border_color');
	$logo_width = get_option('themnific_logo_width');
	$logo_margin = get_option('themnific_logo_margin');
	$nav_padding = get_option('themnific_nav_padding');
	    $custom_css = get_option('themnific_custom_css');
		
	// Add CSS to output
		if ($custom_css)
		$output .= $custom_css ;
		$output = '';
	
	if ($body_color)
		$output .= 'body,.section,.item_full,.item_carousel,.item_slider,.pricing ul,#footer .fourcol{background-color:'.$body_color.'}' . "\n";
		$output .= '.scroll li a,.navi li a{border-color:'.$body_color.' !important}' . "\n";
	if ($sec_body_color)
		$output .= '
		.body2,#header,.scroll ul.sub-menu,.navi ul.sub-menu,.navi li ul.children{background-color:'.$sec_body_color.'}' . "\n";
		$output .= '.scroll li a,.navi li a{border-color:'.$sec_body_color.' !important}' . "\n";
	if ($thi_body_color)
		$output .= '
		.body3,.nav li ul,li.normal h2,ul.medpost li.format-quote{background-color:'.$thi_body_color.'}' . "\n";
	if ($for_body_color)
		$output .= '#serinfo-nav li.current,.wpcf7-submit,a#navtrigger,.stuff span.slidebg,.flex-direction-nav li a,span.ribbon,.block-wrap a.blogmore,#folio-wrap a.blogmore,.imgwrap,a.hoverstuff-link,a.hoverstuff-zoom,li.main h2,.page-numbers.current,a.mainbutton,#submit,#comments .navigation a,.contact-form .submit,.plan-bottom a,a.comment-reply-link,.imageformat{background-color:'.$for_body_color.'}' . "\n";
		$output .= '.section>.container>h3,#servicesbox li,.nav li ul{border-color:'.$for_body_color.' !important}' . "\n";
		$output .= '#servicesbox li:hover h3 i,#portfolio-filter li.current-cat a,.section>.container>h2:after{color:'.$for_body_color.' !important}' . "\n";
	if ($text_color)
		$output.= 'body,.body1 {color:'.$text_color.'}' . "\n";	
	if ($link)
		$output .= '.body1 a, a:link, a:visited,.nav>li>ul>li>a {color:'.$link.'}' . "\n";
	if ($hover)
		$output .= '.entry a,a:hover,.body1 a:hover,#serinfo a:hover,#portfolio-filter a.current,li.current-cat a,#portfolio-filter li.active a,.tagline a,a.slant {color:'.$hover.'}' . "\n";
		$output .= '#main-nav>li:hover,#main-nav>li.current-cat,#main-nav>li.current_page_item {border-color:'.$hover.' !important}' . "\n";
	if ($sec_hover)
		$output .= '
		#navigation a:hover,.scroll>li.current>a,ul.sub-menu>li.current>a,ul.children>li.current>a,#main-nav>li.current-cat a,#main-nav>li.current_page_item>a,#header a:hover{color:'.$sec_hover.'!important}' . "\n";
		


if ($nav_padding)$output .= '
.scroll li a, .navi li a {padding-top:'.$nav_padding.'px;padding-bottom:'.$nav_padding.'px;}' . "\n";

if ($logo_margin)$output .= '
#header h1{margin-top:'.$logo_margin.'px;margin-bottom:'.$logo_margin.'px;}' . "\n";	


if ($logo_width)$output .= '
#header h1{max-width:'.$logo_width.'px}' . "\n";


		
	if ($border)
		$output .= '#clients li,#header,#portfolio-filter,.searchform input.s,.fullbox,.pagination,input, textarea,input checkbox,input radio,select, file{border-color:'.$border.' !important}' . "\n";	




		// General Typography		
		$font_text = get_option('themnific_font_text');	
		$font_text_sec = get_option('themnific_font_text_sec');	
		
		$font_nav = get_option('themnific_font_nav');
		$font_h1 = get_option('themnific_font_h1');	
		$font_h2 = get_option('themnific_font_h2');	
		$font_h2_home = get_option('themnific_font_h2_home');
		$font_h3 = get_option('themnific_font_h3');	
		$font_h4 = get_option('themnific_font_h4');	
		$font_h5 = get_option('themnific_font_h5');	
		$font_h6 = get_option('themnific_font_h5');	
		
		
		$font_h2_tagline = get_option('themnific_font_h2_tagline');	
	
	
		if ( $font_text )
			$output .= 'body,input, textarea,input checkbox,input radio,select, file {font:'.$font_text["style"].' '.$font_text["size"].'px/1.8em '.stripslashes($font_text["face"]).';color:'.$font_text["color"].'}' . "\n";
			
		if ( $font_text_sec )
			$output .= '.body2 {font:'.$font_text_sec["style"].' '.$font_text_sec["size"].'px/2.2em '.stripslashes($font_text_sec["face"]).';color:'.$font_text_sec["color"].'}' . "\n";
			$output .= '.body2 h2,.body2 h3 {color:'.$font_text_sec["color"].'}' . "\n";

		if ( $font_h1 )
			$output .= 'h1 {font:'.$font_h1["style"].' '.$font_h1["size"].'px/1.1em '.stripslashes($font_h1["face"]).';color:'.$font_h1["color"].'}';
		if ( $font_h2 )
			$output .= 'h2 {font:'.$font_h2["style"].' '.$font_h2["size"].'px/1.2em '.stripslashes($font_h2["face"]).';color:'.$font_h2["color"].'}';
			$output .= 'p.special {font-family:'.stripslashes($font_h2["face"]).'}';
		if ( $font_h3 )
			$output .= 'h3,a.tmnf-sc-button.xl,.mainbutton.bigone,.flexhead p {font:'.$font_h3["style"].' '.$font_h3["size"].'px/1.5em '.stripslashes($font_h3["face"]).';color:'.$font_h3["color"].'}';
		if ( $font_h4 )
			$output .= 'h4 {font:'.$font_h4["style"].' '.$font_h4["size"].'px/1.5em '.stripslashes($font_h4["face"]).';color:'.$font_h4["color"].'}';	
		if ( $font_h5 )
			$output .= 'h5 {font:'.$font_h5["style"].' '.$font_h5["size"].'px/1.5em '.stripslashes($font_h5["face"]).';color:'.$font_h5["color"].'}';	
		if ( $font_h6 )
			$output .= 'h6 {font:'.$font_h6["style"].' '.$font_h6["size"].'px/1.5em '.stripslashes($font_h6["face"]).';color:'.$font_h6["color"].'}' . "\n";
			
		if ( $font_nav )
			$output .= '.scroll li a,.navi li a {font:'.$font_nav["style"].' '.$font_nav["size"].'px/1em '.stripslashes($font_nav["face"]).';color:'.$font_nav["color"].'}';
			$output .= '#header h1 a {color:'.$font_nav["color"].'}';	

		if ( $font_h2_home )
			$output .= '.section h2,.section_template h2 {font:'.$font_h2_home["style"].' '.$font_h2_home["size"].'px/1.2em '.stripslashes($font_h2_home["face"]).';color:'.$font_h2_home["color"].'}';
		
		
	// custom stuff	
		if ( $font_text )
			$output .= '.tab-post small a,.taggs a,.ei-slider-thumbs li a {color:'.$font_text["color"].'}' . "\n";	
	
	// Output styles
		if ($output <> '') {
			$output = "<!-- Themnific Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
	}
		
} 


// Add custom styling
add_action('themnific_head','themnific_mobile_styling');
	function themnific_mobile_styling() {
		echo "<!-- Themnific Mobile & Special CSS -->\n";
		
		// google fonts link generator
		get_template_part('/functions/admin-fonts');
		wp_register_style('style-custom', get_template_directory_uri() .	'/style-custom.css');
			wp_enqueue_style( 'style-custom');	
		
		wp_register_style('font-awesome.min', get_template_directory_uri() .	'/styles/font-awesome.min.css');
			wp_enqueue_style( 'font-awesome.min');
		wp_register_style('mobile', get_stylesheet_directory_uri() .	'/style-mobile.css');
			wp_enqueue_style( 'mobile');

} 
?>