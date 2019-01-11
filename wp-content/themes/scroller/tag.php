<?php get_header(); ?>

<div class="container container_block"> 

    <div id="content" class="eightcol">
    
    <?php if (have_posts()) : ?>
    
		<?php $post = $posts[0]; ?>
        <?php if (is_category()) { ?>
        
        	<h2 class="itemtitle"><?php _e('Archive for the','themnific');?> &#8216;<?php single_cat_title(); ?>&#8217; <?php _e('Category','themnific');?></h2>
        
        <?php } elseif( is_tag() ) { ?>
        
        	<h2 class="itemtitle"><?php _e('Tagged','themnific');?> &#8216;<?php single_tag_title(); ?>&#8217;</h2>
        
        <?php } ?>
            
            <div class="hrline"><span></span></div>

      		<ul class="archivepost">
          
    			<?php while (have_posts()) : the_post(); ?>
                                              		
            		<?php get_template_part('/includes/post-types/archivepost');?>
                    
   				<?php endwhile; ?>   <!-- end post -->
                    
     		</ul><!-- end latest posts section-->
            
            <div style="clear: both;"></div>

					<div class="pagination"><?php pagination('&laquo;', '&raquo;'); ?></div>

					<?php else : ?>
			

                        <h1>Sorry, no posts matched your criteria.</h1>
                        <?php get_search_form(); ?><br/>
					<?php endif; ?>

        </div><!-- end #homesingle-->

        <div id="sidebar"  class="fourcol">
               <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar") ) : ?>
               <?php endif; ?>
        </div><!-- #sidebar -->
        
   
</div>
   

<?php get_footer(); ?>