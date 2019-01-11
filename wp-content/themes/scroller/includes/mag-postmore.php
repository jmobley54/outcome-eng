		<div class="postauthor body3">
        	<h4 class="leading"><i class="icon-edit"></i> <?php _e('About the Author','themnific');?>: <?php the_author_posts_link(); ?></h4>
			<?php  echo get_avatar( get_the_author_meta('ID'), '75' );   ?>
 			<div class="authordesc"><?php the_author_meta('description'); ?></div>
		</div>