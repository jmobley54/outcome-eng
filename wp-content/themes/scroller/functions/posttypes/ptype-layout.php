<?php 
//Custom Post Types
add_action('init', 'create_mylayouttype');
function create_mylayouttype() {
    $mylayouttype_args = array(
        'label' => __('Layout','themnific'),
        'singular_label' => __('Layout','themnific'),
        'public' => true,
        'show_ui' => true,
		'menu_position' => 100,
        'capability_type' => 'post',
		'menu_icon' => get_template_directory_uri() . '/functions/images/ptype.png',
        'hierarchical' => false,
		'publicly_queryable' => true,
		'query_var' => true,
        'rewrite' => array( 'slug' => 'layout', 'with_front' => false ),
		'can_export' => true,
        'supports' => array(
			'title', 
			'editor', 
			'post-thumbnails',
			'custom-fields',
			'author',
			'thumbnail'
		  )
       );
  register_post_type('mylayouttype',$mylayouttype_args);
}
	


// Displays post image attachment (sizes: thumbnail, medium, full)
function dp_attachment_image3($postid=0, $size='Large', $attributes='title') {
	if ($postid<1) $postid = get_the_ID();
	if ($images = get_children(array(
		'post_parent' => $postid,
		'post_type' => 'attachment',
		'numberposts' => 1,
		'post_mime_type' => 'image',)))
		foreach($images as $image) {
			$attachment=wp_get_attachment_image_src($image->ID, $size);
			?><img src="<?php echo $attachment[0]; ?>" <?php echo $attributes; ?> /><?php
		}
}



// Custom meta boxes
$prefix = 'themnific_';
$meta_boxes = array();
// first meta box
$meta_boxes[] = array(
	'id' => 'my-meta-box-1',
	'title' => 'Layout Options',
	'pages' => array('mylayouttype'), // multiple post types
	'context' => 'normal',
	'priority' => 'high',
	'fields' => array(
	        
		array(
            'name' => 'Navigation Anchor',
            'desc' => 'e.g. portfolio, blog. Then in custom menu add #portfolio, #blog...',
            'id' => $prefix . 'navigation_anchor',
            'type' => 'text',
            'std' => ''
        ),


        array(
            'name' => 'Display this section in responsive mode?',
            'desc' => '',
            'id' => $prefix . 'responsive_section',
            'type' => 'select',
            'std' => '',
            'options' => array('Yes','No')
        ),	
		

        array(
            'name' => 'Section Intro',
            'desc' => 'Enter plain intro text',
            'id' => $prefix. 'section_text',
            'type' => 'textarea',
            'std' => ''
        ),

        array(
            'name' => 'Shortcode/Map Code area',
            'desc' => 'Enter any shortcode (e.g. Revolution slider) or iframe code (google maps)',
            'id' => $prefix. 'map',
            'type' => 'textarea',
            'std' => ''
        ),

	        
		array(
            'name' => 'Background Color',
            'desc' => 'e.g. #CCCCCC.',
            'id' => 'colorSelector',
            'type' => 'text',
            'std' => ''
        ),
		
	        
		array(
            'name' => 'Text Color',
            'desc' => 'e.g. #FFFFFF.',
            'id' => 'colorSelector2',
            'type' => 'text',
            'std' => ''
        ),
		
	)
);






foreach ($meta_boxes as $meta_box) {
	$my_box = new My_meta_box($meta_box);
}

class My_meta_box_layout {

	protected $_meta_box;

	// create meta box based on given data
	function __construct($meta_box) {
		if (!is_admin()) return;
	
		$this->_meta_box = $meta_box;

		// fix upload bug: http://www.hashbangcode.com/blog/add-enctype-wordpress-post-and-page-forms-471.html
		$current_page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1, -4);
		if ($current_page == 'page' || $current_page == 'page-new' || $current_page == 'post' || $current_page == 'post-new') {
			add_action('admin_head', array(&$this, 'add_post_enctype'));
		}
		
		add_action('admin_menu', array(&$this, 'add'));

		add_action('save_post', array(&$this, 'save'));
	}

}
	
// enqueue scripts and styles, but only if is_admin
function load_custom_wp_admin_style() {
        wp_enqueue_style('colorpicker', get_template_directory_uri().'/functions/posttypes/colorpicker/css/colorpicker.css');
		wp_enqueue_style('layout', get_template_directory_uri().'/functions/posttypes/colorpicker/css/layout.css');
}
add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );


function my_enqueue($hook) {
    if( 'post.php' != $hook & 'post-new.php' != $hook )
        return;
		wp_enqueue_script('eye', get_template_directory_uri().'/functions/posttypes/colorpicker/js/eye.js');
		wp_enqueue_script('utils', get_template_directory_uri().'/functions/posttypes/colorpicker/js/utils.js');
		wp_enqueue_script('bg-cutom', get_template_directory_uri().'/functions/posttypes/colorpicker/js/bg-cutom.js');
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );

	/// Add meta box for multiple post types
	function add() {
		foreach ($this->_meta_box['pages'] as $page) {
			add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
		}
	}



	// Callback function to show fields in meta box
	function show() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
	
		echo '<table class="form-table">';

		foreach ($this->_meta_box['fields'] as $field) {
			// get current post meta data
			$meta = get_post_meta($post->ID, $field['id'], true);
		
			echo '<tr>',
					'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
					'<td>';
			switch ($field['type']) {
				case 'text':
					echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" />',
						'<br />', $field['desc'];
					break;
				case 'textarea':
					echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta ? $meta : $field['std'], '</textarea>',
						'<br />', $field['desc'];
					break;
				case 'select':
					echo '<select name="', $field['id'], '" id="', $field['id'], '">';
					foreach ($field['options'] as $option) {
						echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
					}
					echo '</select>';
					break;
				case 'radio':
					foreach ($field['options'] as $option) {
						echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
					}
					break;
				case 'checkbox':
					echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
					break;
				case 'file':
					echo $meta ? "$meta<br />" : '', '<input type="file" name="', $field['id'], '" id="', $field['id'], '" />',
						'<br />', $field['desc'];
					break;
				case 'image':
					echo $meta ? "<img src=\"$meta\" width=\"150\" height=\"150\" /><br />$meta<br />" : '', '<input type="file" name="', $field['id'], '" id="', $field['id'], '" />',
						'<br />', $field['desc'];
					break;
			}
			echo 	'<td>',
				'</tr>';
		}
	
		echo '</table>';
	}

	// Save data from meta box
	function save($post_id) {
		// verify nonce
		if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
			return $post_id;
		}

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		foreach ($this->_meta_box['fields'] as $field) {
			$name = $field['id'];
			
			$old = get_post_meta($post_id, $name, true);
			$new = $_POST[$field['id']];
			
			if ($field['type'] == 'file' || $field['type'] == 'image') {
				$file = wp_handle_upload($_FILES[$name], array('test_form' => false));
				$new = $file['url'];
			}
			
			if ($new && $new != $old) {
				update_post_meta($post_id, $name, $new);
			} elseif ('' == $new && $old && $field['type'] != 'file' && $field['type'] != 'image') {
				delete_post_meta($post_id, $name, $old);
			}
		}
	}




?>