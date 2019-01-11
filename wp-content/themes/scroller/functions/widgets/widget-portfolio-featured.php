<?php
add_action('widgets_init', 'tmnf_folio_featured_widget');

function tmnf_folio_featured_widget()
{
	register_widget('tmnf_folio_featured_widget');
}

class tmnf_folio_featured_widget extends WP_Widget {
	
	function tmnf_folio_featured_widget()
	{
		$widget_ops = array('classname' => 'tmnf_folio_featured_widget', 'description' => 'folio_featured posts widget.');

		$control_ops = array('id_base' => 'tmnf_folio_featured_widget');

		$this->__construct('tmnf_folio_featured_widget', 'Themnific - Portfolio Featured', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)
	{
		extract($args);
		
		$title = $instance['title'];
		$post_type = 'all';
		$categories = $instance['categories'];
		$posts = $instance['posts'];
		
		echo $before_widget;
		?>
		
		<?php
		$post_types = get_post_types();
		unset($post_types['page'], $post_types['attachment'], $post_types['revision'], $post_types['nav_menu_item']);
		
		if($post_type == 'all') {
			$post_type_array = $post_types;
		} else {
			$post_type_array = $post_type;
		}
		?>
		
			<h2 class="widget"><?php echo $title; ?></h2>
			
			<?php
			$recent_posts = new WP_Query(array(
				'showposts' => $posts,
				'post_type' => 'myportfoliotype',
				'tax_query' => array(
					array(
						'taxonomy' => 'categories',
						'terms' => $categories,
						'field' => 'term_id',
					)
				),
			));
			?>
            <ul class="featured fea_folio">
			<?php  while($recent_posts->have_posts()): $recent_posts->the_post(); ?>

			<li>
            
                <div class="">
            
                    <div class="imgwrap">
                            
                            <a href="<?php the_permalink(); ?>">
                                    
                                <?php the_post_thumbnail('folio',array('title' => "")); ?>
                            
                            </a>
                            
                    </div>	
                    
                    <div style="clear:both"></div>
        
                    <h2><a href="<?php the_permalink(); ?>"><?php echo short_title('...', 8); ?></a></h2>
            
                </div>
                        
			</li>

			<?php  endwhile; ?>
			</ul>
			<div style="clear: both;"></div>
		
		<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['post_type'] = 'all';
		$instance['categories'] = $new_instance['categories'];
		$instance['posts'] = $new_instance['posts'];
		
		return $instance;
	}

	function form($instance)
	{
		$defaults = array('title' => 'Featured Works', 'post_type' => 'all', 'categories' => 'all', 'posts' => 4, 'show_excerpt' => null);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" style="width: 100%;" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('categories'); ?>">Filter by Category:</label> 
			<select id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>" class="widefat categories" style="width:100%;">
				<option value='all' <?php if ('all' == $instance['categories']) echo 'selected="selected"'; ?>></option>
				<?php $categories = get_categories($args = array(
															'type'		=> 'myportfoliotype',
															'orderby'	=> 'name',
															'order'		=> 'ASC',
															'taxonomy'	=> 'categories'
															)) ?>
				<?php foreach($categories as $category) { ?>
				<option value='<?php echo $category->term_id; ?>' <?php if ($category->term_id == $instance['categories']) echo 'selected="selected"'; ?>><?php echo $category->cat_name; ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('posts'); ?>">Number of posts:</label>
			<input class="widefat" style="width: 30px;" id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" value="<?php echo $instance['posts']; ?>" />
		</p>
		

	<?php }
}
?>