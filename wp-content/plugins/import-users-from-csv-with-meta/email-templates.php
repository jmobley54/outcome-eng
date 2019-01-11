<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

function acui_cpt_email_template() {
	if( !get_option( 'acui_enable_email_templates' ) )
		return;

	$labels = array(
		'name'                  => _x( 'Email templates (Import Users From CSV With Meta)', 'Post Type General Name', 'import-users-from-csv-with-meta' ),
		'singular_name'         => _x( 'Email template (Import Users From CSV With Meta)', 'Post Type Singular Name', 'import-users-from-csv-with-meta' ),
		'menu_name'             => __( 'Email templates (Import Users)', 'import-users-from-csv-with-meta' ),
		'name_admin_bar'        => __( 'Email templates (Import Users From CSV With Meta)', 'import-users-from-csv-with-meta' ),
		'archives'              => __( 'Item Archives', 'import-users-from-csv-with-meta' ),
		'attributes'            => __( 'Item Attributes', 'import-users-from-csv-with-meta' ),
		'parent_item_colon'     => __( 'Parent Item:', 'import-users-from-csv-with-meta' ),
		'all_items'             => __( 'All email template', 'import-users-from-csv-with-meta' ),
		'add_new_item'          => __( 'Add new email template', 'import-users-from-csv-with-meta' ),
		'add_new'               => __( 'Add new email template', 'import-users-from-csv-with-meta' ),
		'new_item'              => __( 'New email template', 'import-users-from-csv-with-meta' ),
		'edit_item'             => __( 'Edit email template', 'import-users-from-csv-with-meta' ),
		'update_item'           => __( 'Update email template', 'import-users-from-csv-with-meta' ),
		'view_item'             => __( 'View email template', 'import-users-from-csv-with-meta' ),
		'view_items'            => __( 'View email templates', 'import-users-from-csv-with-meta' ),
		'search_items'          => __( 'Search email template', 'import-users-from-csv-with-meta' ),
		'not_found'             => __( 'Not found', 'import-users-from-csv-with-meta' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'import-users-from-csv-with-meta' ),
		'featured_image'        => __( 'Featured Image', 'import-users-from-csv-with-meta' ),
		'set_featured_image'    => __( 'Set featured image', 'import-users-from-csv-with-meta' ),
		'remove_featured_image' => __( 'Remove featured image', 'import-users-from-csv-with-meta' ),
		'use_featured_image'    => __( 'Use as featured image', 'import-users-from-csv-with-meta' ),
		'insert_into_item'      => __( 'Insert into email template', 'import-users-from-csv-with-meta' ),
		'uploaded_to_this_item' => __( 'Uploaded to this email template', 'import-users-from-csv-with-meta' ),
		'items_list'            => __( 'Items list', 'import-users-from-csv-with-meta' ),
		'items_list_navigation' => __( 'Email template list navigation', 'import-users-from-csv-with-meta' ),
		'filter_items_list'     => __( 'Filter email template list', 'import-users-from-csv-with-meta' ),
	);
	$args = array(
		'label'                 => __( 'Mail template (Import Users From CSV With Meta)', 'import-users-from-csv-with-meta' ),
		'description'           => __( 'Mail templates for Import Users From CSV With Meta', 'import-users-from-csv-with-meta' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor' ),
		'hierarchical'          => true,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 100,
		'menu_icon'             => 'dashicons-email',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'rewrite'               => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'acui_email_template', $args );

}
add_action( 'init', 'acui_cpt_email_template', 0 );

function acui_email_templates_edit_form_after_editor( $post = "" ){
	if( !empty( $post ) && $post->post_type != 'acui_email_template' )
		return;
	?>
<p><?php _e( 'You can use', 'import-users-from-csv-with-meta' ); ?></p>
<ul style="list-style-type:disc; margin-left:2em;">
	<li>**username** = <?php _e( 'username to login', 'import-users-from-csv-with-meta' ); ?></li>
	<li>**password** = <?php _e( 'user password', 'import-users-from-csv-with-meta' ); ?></li>
	<li>**loginurl** = <?php _e( 'current site login url', 'import-users-from-csv-with-meta' ); ?></li>
	<li>**lostpasswordurl** = <?php _e( 'lost password url', 'import-users-from-csv-with-meta' ); ?></li>
	<li>**passwordreseturl** = <?php _e( 'password reset url', 'import-users-from-csv-with-meta' ); ?></li>
	<li>**email** = <?php _e( 'user email', 'import-users-from-csv-with-meta' ); ?></li>
	<li><?php _e( "You can also use any WordPress user standard field or an own metadata, if you have used it in your CSV. For example, if you have a first_name column, you could use **first_name** or any other meta_data like **my_custom_meta**", 'import-users-from-csv-with-meta' ) ;?></li>
</ul>
	<?php
}
add_action( 'edit_form_after_editor', 'acui_email_templates_edit_form_after_editor', 10, 1 );

function acui_refresh_enable_email_templates(){
	check_ajax_referer( 'codection-security', 'security' );
	update_option( 'acui_enable_email_templates', ( $_POST[ 'enable' ] == "true" ) );
	die();
}
add_action( 'wp_ajax_acui_refresh_enable_email_templates', 'acui_refresh_enable_email_templates' );

function acui_email_template_selected(){
	check_ajax_referer( 'codection-security', 'security' );
	$email_template = get_post( intval( $_POST['email_template_selected'] ) );
	
	echo json_encode( array( 'id' => $email_template->ID, 'title' => $email_template->post_title, 'content' => wpautop( $email_template->post_content ) ) );
	die();
}
add_action( 'wp_ajax_acui_email_template_selected', 'acui_email_template_selected' );