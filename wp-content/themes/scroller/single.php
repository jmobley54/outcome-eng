<?php get_header(); ?> 

    <div class="container container_block"> 
        
            <div id="content" class="eightcol">
                    <?php get_template_part('single-s-right' ); ?>
            </div><!-- #homecontent -->
        
            <div id="sidebar"  class="fourcol">
                   <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Sidebar") ) : ?>
                   <?php endif; ?>
            </div><!-- #sidebar -->
    
    </div>

<?php get_footer(); ?>