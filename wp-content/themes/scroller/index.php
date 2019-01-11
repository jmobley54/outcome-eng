<?php get_header(); ?>

<div class="resmode-No section_template "
style="  <?php if(get_option('themnific_blog_image')) { ?>background-image:url(<?php echo esc_url(get_option('themnific_blog_image'));?>);<?php } else {}?> ">

	<div class="container">
    
    	<h2><?php wp_title(''); ?></h2>
		
	</div>

</div>


<div id="portfolio-filter" class="body3">
 
	<div class="container">
    
        <ul>
        
            <li><a class="current" href="<?php echo stripslashes(get_option('themnific_url_blog'));?>">
            
            <?php _e('All','themnific');?></a></li>
            
            <?php wp_list_categories('depth=1&title_li='); ?> 
            
        </ul>

	</div>

</div>

<div class="hrlineB"></div>

		<div class="container"> 

        <div id="content" class="eightcol">

          <ul class="medpost">

                	<?php
						$temp = $wp_query;
						$limit = get_option('posts_per_page');
						$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
						$wp_query= null;
						$wp_query = new WP_Query();
						$wp_query->query('showposts=' . $limit . '&paged=' . $paged);
						$wp_query->is_home = false;
					?>
					<?php if (have_posts()) : ?>
                                        
                    <?php while (have_posts()) : the_post(); ?>
            
						<?php if(has_post_format('gallery'))  {
                            echo get_template_part( '/includes/post-types/medpost' );
                        }elseif(has_post_format('video')){
                            echo get_template_part( '/includes/post-types/medpost' );
                        }elseif(has_post_format('audio')){
                            echo get_template_part( '/includes/post-types/medpost' );
                        }elseif(has_post_format('image')){
                            echo get_template_part( '/includes/post-types/image' );
                        }elseif(has_post_format('link')){
                            echo get_template_part( '/includes/post-types/link' );
                        }elseif(has_post_format('quote')){
                            echo get_template_part( '/includes/post-types/quote' );
                            } else {
                            echo get_template_part( '/includes/post-types/medpost' );
                        }?>
                            
					<?php endwhile; ?><!-- end post -->
                    
           	</ul><!-- end latest posts section-->
            
            <div style="clear: both;"></div>

					<div class="pagination"><?php pagination('&laquo;', '&raquo;'); ?></div>

					<?php else : ?>
			

                        <h1>Sorry, no posts matched your criteria.</h1>
                        <?php get_search_form(); ?><br/>
					<?php endif; ?>

        </div><!-- end #content-->

        <div id="sidebar"  class="fourcol">
               <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar") ) : ?>
               <?php endif; ?>
        </div><!-- #sidebar -->
        
        </div>

<?php get_footer(); ?>