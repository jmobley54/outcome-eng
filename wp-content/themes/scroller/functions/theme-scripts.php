<?php
if ( ! is_admin() ) { add_action( 'wp_print_scripts', 'themnific_add_javascript' ); }

function themnific_add_javascript() {

		// Load Common scripts	
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery.hoverIntent.minified', get_template_directory_uri().'/js/jquery.hoverIntent.minified.js','','', true);
		wp_enqueue_script('prettyPhoto', get_template_directory_uri() . '/js/jquery.prettyPhoto.js','','', true);
		wp_enqueue_script('jquery.scrollTo', get_template_directory_uri() . '/js/jquery.scrollTo.js','','', true);
		wp_enqueue_script('jquery.nav.', get_template_directory_uri() . '/js/jquery.nav.js','','', true);
		wp_enqueue_script('jquery.parallax-1.1.3', get_template_directory_uri() . '/js/jquery.parallax-1.1.3.js','','', true);
		wp_enqueue_script('superfish', get_template_directory_uri().'/js/superfish.js','','', true);
		wp_enqueue_script('jquery.hoverIntent.minified', get_template_directory_uri().'/js/jquery.hoverIntent.minified.js','','', true);
		wp_enqueue_script('ownScript', get_template_directory_uri() .'/js/ownScript.js','','', true);
		
		// Load homepage slider scripts		
		if (is_home()||is_single()||is_front_page()||is_page_template('template-fullwidth.php')||is_page_template('template-fullwidth-image.php')||is_page_template('index.php')||is_page_template('homepage.php')||is_page_template('homepage-alt.php')) {
		wp_enqueue_script('jquery.flexslider-min', get_template_directory_uri() .'/js/jquery.flexslider-min.js','','', true);
		wp_enqueue_script('jquery.flexslider.start.main', get_template_directory_uri() .'/js/jquery.flexslider.start.main.js','','', true);
		}

		// Load folio item slider scripts		
		if ( 'myportfoliotype' == get_post_type() ) {
		wp_enqueue_script('jquery.flexslider-min', get_template_directory_uri() .'/js/jquery.flexslider-min.js','','', true);
		wp_enqueue_script('jquery.flexslider.start.folio', get_template_directory_uri() .'/js/jquery.flexslider.start.folio.js','','', true);
		}

		
		
		// Singular comment script		
		if ( is_singular() ) wp_enqueue_script( 'comment-reply','','', true );

	}
?>