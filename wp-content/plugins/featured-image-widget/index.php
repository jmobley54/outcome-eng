<?php
/**
 * Plugin Name: Featured image widget
 * Plugin URI: http://wordpress.org/extend/plugins/featured-image-widget/
 * Description: This widget shows the featured image for posts and pages.
 * Version: 0.4
 * Author: Walter Vos
 * Author URI: http://www.waltervos.nl/
 */

class FeaturedImageWidget extends WP_Widget {
    function __construct() {
        load_plugin_textdomain( 'featured_image_widget', false, trailingslashit(basename(dirname(__FILE__))) . 'languages/');
        parent::__construct(
            'FeaturedImageWidget', // Base ID
            __( 'Featured Image Widget', 'featured_image_widget' ), // Name
            array( 'description' => __( 'This widget shows the featured image for posts and pages', 'featured_image_widget' ), ) // Args
        );
    }

    function form($instance) {
        $title = esc_attr($instance['title']);
        $instance['image-size'] = (!$instance['image-size'] || $instance['image-size'] == '') ? 'post-thumbnail' : $instance['image-size'];
        ?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'featured_image_widget' ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<p>
    <label for="<?php echo $this->get_field_id('image-size'); ?>"><?php _e( 'Image size to display:', 'featured_image_widget' ); ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id('image-size'); ?>" name="<?php echo $this->get_field_name('image-size'); ?>">
                <?php foreach (get_intermediate_image_sizes() as $intermediate_image_size) : ?>
        <?php
        $selected = ($instance['image-size'] == $intermediate_image_size) ? ' selected="selected"' : '';
        ?>
        <option value="<?php echo $intermediate_image_size; ?>"<?php echo $selected; ?>><?php echo $intermediate_image_size; ?></option>
                <?php endforeach; ?>
    </select>
</p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $new_instance['title'] = strip_tags($new_instance['title']);
        return $new_instance;
    }

    function widget($args, $instance) {
        extract($args);
        $size = $instance['image-size'];
        $title = apply_filters('widget_title', $instance['title']);
        global $post;

        if (has_post_thumbnail($post->ID)) {
            echo $before_widget;
            if ( $title ) {
                echo $before_title . $title . $after_title;
            }
            echo get_the_post_thumbnail($post->ID, $size);
            echo $after_widget;
        } elseif ($post->post_parent && has_post_thumbnail($post->post_parent)) {
            echo $before_widget;
            if ( $title ) { 
                echo $before_title . $title . $after_title;
            }
            echo get_the_post_thumbnail($post->post_parent, $size);
            echo $after_widget;
        } else {
            // the current post lacks a thumbnail, we do nothing?
        }
    }
} // End class FeaturedImageWidget

add_action('widgets_init', create_function('', 'return register_widget("FeaturedImageWidget");'));
?>