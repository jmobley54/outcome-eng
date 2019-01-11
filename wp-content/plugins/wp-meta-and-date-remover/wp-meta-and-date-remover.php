<?php
/*
Plugin Name: WP Meta and Date Remover
Plugin URI: mailto:prasadkirpekar@outlook.com
Description: Remove Meta information such as Author and Date from posts and pages.
Version: 1.7.3
Author: Prasad Kirpekar
Author URI: http://twitter.com/kirpekarprasad
License: GPL v2
Copyright: Prasad Kirpekar

	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function wpmdr_extra_links($links){
	$donate="<a href='http://bit.ly/PKDonate'>Support Development</a>";
	$fiverr="<a href='http://bit.ly/2bzAUb6'>More Customization</a>";
$setting_link = '<a href="../wp-admin/options-general.php?page=wp-meta-and-date-remover.php">Settings</a>';
  
  array_unshift($links, $setting_link);
 
  array_unshift($links, $fiverr);
  array_unshift($links,$donate);
  return $links;
}
$plugin = plugin_basename(__FILE__);

function wpmdr_reg_scripts(){
	wp_register_script( 'wpmdr_md_script',plugin_dir_url(__FILE__).'include/materialize/materialize.min.js' );
	wp_register_script( 'wpmdr_cm_script',plugin_dir_url(__FILE__).'include/codemirror/codemirror.js' );
	wp_register_script('wpmdr_cm_lang',plugin_dir_url(__FILE__).'include/codemirror/css.js' );	
	wp_register_script( 'wpmdr_cm_init',plugin_dir_url(__FILE__).'include/codemirror/cm_init.js' );	
	wp_register_script( 'wpmdr_cm_ar',plugin_dir_url(__FILE__).'include/codemirror/autorefresh.js' );	
}
function wpmdr_enqueue_scripts(){
	wp_enqueue_script('wpmdr_md_script');
	wp_enqueue_script('wpmdr_cm_script');
	wp_enqueue_script('wpmdr_cm_lang');
	wp_enqueue_script('wpmdr_cm_init');
	wp_enqueue_script('wpmdr_cm_ar');
	wp_enqueue_style('wpmdr_md_style',plugin_dir_url(__FILE__).'include/materialize/materialize.min.css');
	wp_enqueue_style('wpmdr_cm_style',plugin_dir_url(__FILE__).'include/codemirror/codemirror.css');
	wp_enqueue_style('wpmdr_cm_theme',plugin_dir_url(__FILE__).'include/codemirror/dracula.css');
	wp_enqueue_style('wpmdr_css',plugin_dir_url(__FILE__).'include/wpmdr.css');
}
add_action('admin_init','wpmdr_reg_scripts');

//Removal using css

function wpmdr_inline_style(){
	if(get_option('wpmdr_disable_css')=="0"){
		echo "<style>/* CSS added by WP Meta and Date Remover*/".get_option('wpmdr_css')."</style>";
	}
}
function wpmdr_settings()
{
	$css=get_option('wpmdr_css');
	$disable_php=get_option('wpmdr_disable_php');
	$disable_css=get_option('wpmdr_disable_css');
	$from_=get_option('wpmdr_from_');
	$indi_op=get_option('wpmdr_individual_post');
	
	if(isset($_POST['submitted']))
	{
		if(isset($_POST['wpmdr_from_home'])) $from_['home']="1";
		else $from_['home']="0";

		if(isset($_POST['wpmdr_css'])) $css=$_POST['wpmdr_css'];
		
		if(isset($_POST['wpmdr_disable_php'])) $disable_php="1";
		else $disable_php="0";

		if(isset($_POST['wpmdr_disable_css'])) $disable_css="1";
		else $disable_css="0";

		if(isset($_POST['wpmdr_individual_post'])) $indi_op="1";
		else $indi_op="0";
		
		update_option('wpmdr_css',$css);
		update_option('wpmdr_disable_php',$disable_php);
		update_option('wpmdr_disable_css',$disable_css);
		update_option('wpmdr_from_',$from_);
		update_option('wpmdr_individual_post',$indi_op);
		echo '<div class="updated fade"><p>Settings Saved! </p></div>';
	}
	$action_url = $_SERVER['REQUEST_URI'];
	include "admin/wpmdr-options.php";
}

function wpmdr_admin_settings()
{
			$page=add_options_page('WP Meta and Date Remover', 'WP Meta and Date Remover', 'manage_options', basename(__FILE__), 'wpmdr_settings');
	add_action('admin_print_scripts-' . $page, 'wpmdr_enqueue_scripts');
}
function wpmdr_init_option(){
	$css=".entry-meta {display:none !important;}
	.home .entry-meta { display: none; }
	.entry-footer {display:none !important;}
	.home .entry-footer { display: none; }";
	

	if(!add_option('wpmdr_from_',array('home'=>'1','help_notice'=>'0'))){
		update_option('wpmdr_from_',array('home'=>'1','help_notice'=>'0'));
	}
	add_option('wpmdr_css',$css);
	add_option('wpmdr_disable_php',"0");
	add_option('wpmdr_disable_css',"0");
	add_action( 'admin_notices', 'wpmdr_notice' );
	add_option('wpmdr_individual_post',"0");
}



function wpmdr_menu()
{
    global $post;

    /* check if this is a post, if not then we won't add the custom field */
    /* change this post type to any type you want to add the custom field to */
    if (get_post_type($post) != 'post') return false;

    /* get the value corrent value of the custom field */
    $value = get_post_meta($post->ID, 'wpmdr_menu', true);
	if(empty($value)){
		add_post_meta($post->ID, 'wpmdr_menu', 1, true );
		$value=1;
		
	}
	
    ?>
        <div class="misc-pub-section">
            <label><input type="checkbox"<?php echo (($value==1)? ' checked="checked"' :null ) ?> value="1" name="wpmdr_menu" /> Remove Meta and Date</label>
        </div>
    <?php
}

function wpmdr_save_postdata($postid)
{
  
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

    
    if ( !current_user_can( 'edit_page', $postid ) ) return false;

   
    if(empty($postid) || $_POST['post_type'] != 'post' ) return false;

    if(isset($_POST['wpmdr_menu'])){
       
        update_post_meta($postid, 'wpmdr_menu',1);
    }
    else{
       
        update_post_meta($postid, 'wpmdr_menu',2);
    }
}
if(get_option('wpmdr_individual_post')=="1"){
add_action( 'post_submitbox_misc_actions', 'wpmdr_menu' );
add_action( 'save_post', 'wpmdr_save_postdata');}

function wpmdr_php_filter_option(){
	$from_=get_option('wpmdr_from_');
	if(is_home() || is_front_page() ){
		if($from_['home']=="1") wpmdr_remove_meta_php();
		else return;
	}
	else{
			global $post;
			$value = get_post_meta($post->ID, 'wpmdr_menu', true);
			if(empty($value)){
				add_post_meta($post->ID, 'wpmdr_menu', 1, true );
				$value=1;
			}
			
			if (get_post_type($post) == 'post'){
				if(get_post_meta($post->ID, 'wpmdr_menu', true)!=1&&get_option('wpmdr_individual_post')=="1")
				return;
				else wpmdr_remove_meta_php();
			}
			return;
	}
}


function wpmdr_css_filter_option(){
	$from_=get_option('wpmdr_from_');
	if(is_home() || is_front_page()){
		if($from_['home']=="1") wpmdr_inline_style();
		else return;
	}
	else{
		global $post;
			$value = get_post_meta($post->ID, 'wpmdr_menu', true);
			if(empty($value)){
				add_post_meta($post->ID, 'wpmdr_menu', 1, true );
				$value=1;
			}
		if (get_post_type($post) == 'post'){
			if(get_post_meta($post->ID, 'wpmdr_menu', true)!=1&&get_option('wpmdr_individual_post')=="1")
			return;
			else wpmdr_inline_style();
		}
		return;
	}
}
function wpmdr_notice() {
	$from_=get_option('wpmdr_from_');
	
	if($from_['help_notice']=="0"){
    echo '
    <div class="notice notice-success is-dismissible">
        <p>Thank you for Installing WP Meta and Date Remover. Read this post see <a href="http://bit.ly/wpmdrplugin" target="_blank"><b>how to configure this plugin.</b></a><br/>
		You may support development of this plugin by donating tiny amount <a href="http://bit.ly/PKDonate" target="_blank"><b>here</b></a>.
		</p>
    </div>';;
	$from_['help_notice']="1";
	update_option('wpmdr_from_',$from_);
	}
    
}
add_action( 'admin_notices', 'wpmdr_notice' );

// removal using php.
//some times css removal don't work for every theme.
function wpmdr_remove_meta_php() {
	
		if(get_option('wpmdr_disable_php')=="0"){
			add_filter('the_date', '__return_false');
			add_filter('the_author', '__return_false');
			add_filter('the_time', '__return_false');
			add_filter('the_modified_date', '__return_false');
			add_filter('get_the_date', '__return_false');
			add_filter('get_the_author', '__return_false');
			add_filter('get_the_title', '__return_false');
			add_filter('get_the_time', '__return_false');
			add_filter('get_the_modified_date', '__return_false');
		}
	
} 


//do everything 
register_activation_hook(__FILE__, 'wpmdr_init_option');
	
add_action('wp_head','wpmdr_css_filter_option');
add_filter("plugin_action_links_$plugin", 'wpmdr_extra_links' );
add_action('loop_start', 'wpmdr_php_filter_option');
add_action('admin_menu','wpmdr_admin_settings');



