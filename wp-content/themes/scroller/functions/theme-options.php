<?php

add_action('init','themnific_options');  
function themnific_options(){
	
// VARIABLES
$themename = "Scroller";
$shortname = "themnific";

// Populate option in array for use in theme 
global $themnific_options; 
$themnific_options = get_option('themnific_options');

$GLOBALS['template_path'] = get_template_directory_uri();;

//Access the WordPress Categories via an Array
$themnific_categories = array();  
$themnific_categories_obj = get_categories('hide_empty=0');
foreach ($themnific_categories_obj as $themnific_cat) {
    $themnific_categories[$themnific_cat->cat_ID] = $themnific_cat->cat_name;}
$categories_tmp = array_unshift($themnific_categories, "Select a category:");    
       
//Access the WordPress Pages via an Array
$themnific_pages = array();
$themnific_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($themnific_pages_obj as $themnific_page) {
    $themnific_pages[$themnific_page->ID] = $themnific_page->post_name; }
$themnific_pages_tmp = array_unshift($themnific_pages, "Select a page:");       

// Image Alignment radio box
$options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 

// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 

//Testing 
$options_select = array("one","two","three","four","five"); 
$options_radio = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five"); 


//Stylesheets Reader
$alt_stylesheet_path = get_template_directory() . '/styles/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}
// Set the Options Array
$bgurl =  get_template_directory_uri() . '/functions/images/bg';
//More Options
$all_uploads_path =  home_url() . '/wp-content/uploads/';
$all_uploads = get_option('themnific_uploads');
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");

// THIS IS THE DIFFERENT FIELDS
$options = array();   

$options[] = array( "name" => "General Settings",
                    "type" => "heading");

$options[] = array( "name" => "Main Logo",
					"desc" => "Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png)<br/>
					You need to use bigger logo, eg. theme demo uses 420x150px,",
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload");    

$options[] = array( "name" => "Small Logo",
					"desc" => "Upload a logo for your theme, or specify the image address of your online logo. (http://yoursite.com/logo.png)<br/>
					You need to use bigger logo, eg. theme demo uses 200x40px",
					"id" => $shortname."_logo_small",
					"std" => "",
					"type" => "upload");   

$options[] = array( "name" => "Custom Favicon",
					"desc" => "Upload a 16px x 16px Png/Gif image that will represent your website's favicon.",
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload"); 
                                               
$options[] = array( "name" => "Tracking Code",
					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");  
					

		
// general layout

$options[] = array( "name" => "General Layout",
					"type" => "heading");   
					
$options[] = array( "name" => "Type of Main Slider",
					"desc" => "Choose Featured slider that you want to display in homepage.",
					"id" => $shortname."_type_slider",
					"type" => "select2",
					"options" => array(
					"slider" => "Slider With Caption", 
					"slider2" => "Slider With Content",
					) );

$options[] = array( "name" => "Disable Main Slider",
                    "desc" => "You can disable main/homepage slider here.",
                    "id" => $shortname."_main_slider_dis",
                    "std" => "false",
                    "type" => "checkbox");

$options[] = array( "name" => "Disable Footer Widget Section",
					"desc" => "Check to disable widgets in footer",
					"id" => $shortname."_widget_dis",
					"std" => "false",
					"type" => "checkbox"); 

$options[] = array( "name" => "Replace native gallery [gallery] with slider",
                    "desc" => "Tick to enable classic grid gallery as slider.",
                    "id" => $shortname."_slider_gallery",
                    "std" => "true",
                    "type" => "checkbox");

$options[] = array( "name" => "Portfolio URL",
					"desc" => "Enter full URL of your Portfolio page (Create page using 'Portfolio' template)",
					"id" => $shortname."_url_portfolio",
					"std" => "",
					"type" => "text");
					
$options[] = array( "name" => "Portfolio Archives Featured Image",
					"desc" => "Upload large image for blog header",
					"id" => $shortname."_portfolio_image",
					"std" => "",
					"type" => "upload");  
					
$options[] = array( "name" => "Blog URL",
					"desc" => "Enter full URL of your Blog page. (Create page using Blog template)<br />If is Blog template used as homepage add main URL of your website!",
					"id" => $shortname."_url_blog",
					"std" => "",
					"type" => "text");
					
$options[] = array( "name" => "Blog Featured Image",
					"desc" => "Upload large image for blog header",
					"id" => $shortname."_blog_image",
					"std" => "",
					"type" => "upload");  
					
					
// primary styling

$options[] = array( "name" => " Primary Styling",
					"type" => "heading");

			
$options[] = array( "name" => "General Text Font Style",
					"desc" => "Select the typography used in general text. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_text",
					"std" => array('size' => '14','face' => 'Arial','style' => '400','color' => '#757575'),
					"type" => "typography"); 
					
					
$options[] = array( "name" =>  "Body Background Color",
					"desc" => "Pick a custom color for background color of the theme e.g. #32333d",
					"id" => "themnific_body_color",
					"std" => "#fff",
					"type" => "color");

					
$options[] = array( "name" =>  "Link Color",
					"desc" => "Pick a custom color for links. e.g. #585a66",
					"id" => "themnific_link_color",
					"std" => "#333",
					"type" => "color");     

$options[] = array( "name" =>  "Hover Color",
					"desc" => "Pick a custom color for links (hover). #73b8f5",
					"id" => "themnific_link_hover_color",
					"std" => "#4DC8E3",
					"type" => "color");  
					
$options[] = array( "name" =>  "Borders",
					"desc" => "Pick a custom color for text shadows. e.g. #fff",
					"id" => "themnific_border_color",
					"std" => "#e3e3e3",
					"type" => "color"); 
				
	
// secondary styling	
	
$options[] = array( "name" => "Header Styling",
					"type" => "heading");

$options[] = array( "name" => "Navigation Font Style",
					"desc" => "Select the typography you want for heading H1. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_nav",
					"std" => array('size' => '12','face' => 'Open Sans','style' => '700','color' => '#222'),
					"type" => "typography");  							
 
$options[] = array( "name" => "Navigation Background Color",
					"desc" => "Pick a custom color for background color of the theme",
					"id" => "themnific_custom_color",
					"std" => "#fff",
					"type" => "color"); 
					
$options[] = array( "name" => "Navigation Hover/Current Link Color",
					"desc" => "Pick a hover color header links",
					"id" => "themnific_sec_link_hover_color",
					"std" => "#4DC8E3",
					"type" => "color"); 
					

					
$options[] = array( "name" => "Logo - Width limitation",
					"desc" => "Add plain number value. Measure is in pixels (px)",
					"id" => $shortname."_logo_width",
					"std" => "200",
					"type" => "text");
					
					
$options[] = array( "name" => "Logo - Vertical margins",
					"desc" => "Add plain number value. Measure is in pixels (px)",
					"id" => $shortname."_logo_margin",
					"std" => "10",
					"type" => "text");
					
$options[] = array( "name" => "Navigation - Vertical padding",
					"desc" => "Add plain number value. Measure is in pixels (px)",
					"id" => $shortname."_nav_padding",
					"std" => "20",
					"type" => "text");
					

	
// other styling	
	
$options[] = array( "name" => "Other Styling",
					"type" => "heading");
 

$options[] = array( "name" =>  "Elements Color",
					"desc" => "Pick a custom color for background color of e.g. slider caption & navigation hovers,",
					"id" => "themnific_for_body_color",
					"std" => "#4DC8E3",
					"type" => "color");
					
$options[] = array( "name" =>  "Aleternative Background Color",
					"desc" => "Pick a custom color for Footer, Portfolio slider(Shortcode), Services boxes",
					"id" => "themnific_thi_body_color",
					"std" => "#f9f9f9",
					"type" => "color");		

$options[] = array( "name" => "Show Uppercase Fonts",
                    "desc" => "You can disable general uppercase here.",
                    "id" => $shortname."_upper",
                    "std" => "true",
                    "type" => "checkbox");
					

// typography

$options[] = array( "name" => "Headings Typography",
					"type" => "heading");    


$options[] = array( "name" => "H1 Font Style",
					"desc" => "Select the typography you want for heading H1. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h1",
					"std" => array('size' => '70','face' => 'Montserrat','style' => '700','color' => '#000000'),
					"type" => "typography");  

$options[] = array( "name" => "H2 Style - Homepage",
					"desc" => "Select the typography you want for heading H2. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h2_home",
					"std" => array('size' => '80','face' => 'Montserrat','style' => '700','color' => '#000000'),
					"type" => "typography");  

$options[] = array( "name" => "H2 Style - Posts & Items",
					"desc" => "Select the typography you want for heading H2. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h2",
					"std" => array('size' => '55','face' => 'Montserrat','style' => '700','color' => '#000000'),
					"type" => "typography");  

$options[] = array( "name" => "H3 Font Style + Slider Caption",
					"desc" => "Select the typography you want for heading H3. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h3",
					"std" => array('size' => '14','face' => 'Open Sans','style' => '700','color' => '#4a4a4a'),
					"type" => "typography"); 

$options[] = array( "name" => "H4 Font Style",
					"desc" => "Select the typography you want for heading H4. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h4",
					"std" => array('size' => '18','face' => 'Raleway','style' => '600','color' => '#000000'),
					"type" => "typography");  
					
$options[] = array( "name" => "H5 & H6 Font Style",
					"desc" => "Select the typography you want for heading H5 and H6. <br />* semi-safe font <br />*** Google Webfonts (make sure that selected weight is supported!!).",
					"id" => $shortname."_font_h5",
					"std" => array('size' => '14','face' => 'Raleway','style' => '400','color' => '#4a4a4a'),
					"type" => "typography"); 
		


		
// social networks	

$options[] = array( "name" => "Social Networks",
    				"type" => "heading");
			

$options[] = array( "name" => "Rss Feed",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_rss",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Facebook",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_fa",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Twitter",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_tw",
					"std" => "",
					"type" => "text");
					
$options[] = array( "name" => "Google+",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_googleplus",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Pinterest",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_pinterest",
					"std" => "",
					"type" => "text");
					

$options[] = array( "name" => "Instagram",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_instagram",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "You Tube",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_yo",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Vimeo",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_vi",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Tumblr",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_tu",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Flickr",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_fl",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "LinkedIn",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_li",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Foursquare",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_foursquare",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Dribbble",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_dribbble",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Skype",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_sk",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Stumbleupon",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_stumbleupon",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Github",
					"desc" => "Enter full URL (http:// including)",
					"id" => $shortname."_socials_github",
					"std" => "",
					"type" => "text");

					
// footer
$options[] = array( "name" => "Footer",
                    "type" => "heading");
					
$options[] = array( "name" => "Enable Custom Footer (Left)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_left",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Left)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_left_text",
					"std" => "<p></p>",
					"type" => "textarea");
	
						
$options[] = array( "name" => "Enable Custom Footer (Right)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_right",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Right)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_right_text",
					"std" => "<p></p>",
					"type" => "textarea");


							                        
update_option('themnific_template',$options);      
update_option('themnific_themename',$themename);   
update_option('themnific_shortname',$shortname);

                                     
// Themnific Metabox Options
$themnific_metaboxes = array();

$themnific_metaboxes[] = array (	"name" => "image",
							"label" => "Image",
							"type" => "upload",
							"desc" => "Upload file here...");							
    
update_option('themnific_custom_template',$themnific_metaboxes);      

}

?>