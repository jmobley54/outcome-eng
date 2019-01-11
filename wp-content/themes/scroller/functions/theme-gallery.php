<?php



/***********  replace default gallery shortcode with tmnf-slideshow-gallery *************/
add_action( 'wp_head', 'add_tmnf_gallery' );
function add_tmnf_gallery() {
  $tmnf = get_option( 'tmnf_gallery' );

	if ( isset( $tmnf[ 'pages' ] ) && $tmnf[ 'pages' ] == '1' || is_page_template( 'template-fullwidth.php' ) ) {

		remove_shortcode( 'gallery', 'gallery_shortcode' );
		add_shortcode( 'gallery', 'tmnf_gallery_shortcode' );

	} else {

		if ( is_single() || is_page()||is_home()||is_front_page() || is_page_template( 'template-fullwidth.php' ) ) {
			remove_shortcode( 'gallery', 'gallery_shortcode' );
			add_shortcode( 'gallery', 'tmnf_gallery_shortcode' );
		}
	}
}

//replace default gallery shortcode by image slider if not blog category
function tmnf_gallery_shortcode( $attr ) {
	$tmnf = get_option( 'tmnf_gallery' );


	$post = get_post();

	static $instance = 0;
	$instance++;

	if ( ! empty( $attr[ 'ids' ] ) ) {
		// 'ids' is explicitly ordered, unless you specify otherwise.
		if ( empty( $attr[ 'orderby' ] ) )
			$attr[ 'orderby' ] = 'post__in';
		$attr[ 'include' ] = $attr[ 'ids' ];
	}

	// Allow plugins/themes to override the default gallery template.
	$output = apply_filters( 'post_gallery', '', $attr );
	if ( $output != '' )
		return $output;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr[ 'orderby' ] ) ) {
		$attr[ 'orderby' ] = sanitize_sql_orderby( $attr[ 'orderby' ] );
		if ( !$attr[ 'orderby' ] )
			unset( $attr[ 'orderby' ] );
	}

	extract( shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'columns'    => 3,
		'size'       => 'tabs',
		'include'    => '',
		'exclude'    => ''
	), $attr ) );

	$id = intval( $id );
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty( $include ) ) {
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty( $exclude ) ) {
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty( $attachments ) )
		return '';


	ob_start();
	echo '<div class="flexslider singleslider">',"\n",
	        '<div class="tmnf_slideshow_menu" class="clearfix">',

                         "\t",'<div class="slideshow_nav">',"\n",
                            "\t",'<ul class="flex-direction-nav">',"\n",
                            "\t",'<li>',"\n",
        						"\t\t",'<a href="#" class="prev" title="Previous">Previous</a>',"\n",
        						"\t",'</li>',"\n",
        						"\t",'<li>',"\n",
        						"\t\t",'<a href="#" class="next" title="Next">Next</a>',"\n",
        						"\t",'</li>',"\n",
        					"\t",'</ul>',"\n",
        					"\t",'</div>',"\n",

				'</div>',
				'<div class="clear"></div>';

		echo "\n",'<ul class="slides">',"\n";

		foreach ( $attachments as $id => $attachment ) {
			$attachmentimage = wp_get_attachment_image( $id, 'folio_slider');
			$description = $attachment->post_title;
			echo "\t",'<li>',"\n";
			echo "\t\t",$attachmentimage.apply_filters( 'the_title', isset( $parent->post_title ) );
			if ( isset( $description ) )
				echo "\t\t",'<div class="flex-caption">'.$description.'</div>';
			echo "\t",'</li>',"\n";
		}
		echo '</ul><!-- .slides -->',


					'<div class="clearfix"></div>',
					'<div id="slideshowloader"></div>';


		echo '</div><!-- .flexslider -->',"\n";
				echo '<ul class="tmnf_slideshow_thumbnails">';

		foreach ( $attachments as $id => $attachment ) {
			$attachmentimage = wp_get_attachment_image( $id, 'tabs' );
			echo "\t",'<li><a href="#">';
			echo "\t\t",$attachmentimage.apply_filters( 'the_title', isset( $parent->post_title ) );
			echo "\t",'</a></li>';
		}

		echo	'</ul>',"\n";
		
		wp_enqueue_script('jquery.flexslider-min', get_template_directory_uri() .'/js/jquery.flexslider-min.js','','', true);
		wp_enqueue_script('jquery.flexslider.start.single', get_template_directory_uri() .'/js/jquery.flexslider.start.single.js','','', true);
		$gallery = ob_get_clean();
		return $gallery;
		ob_end_clean();
 }
?>