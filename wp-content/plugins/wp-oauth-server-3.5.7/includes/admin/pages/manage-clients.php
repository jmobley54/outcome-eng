<?php
function wo_admin_manage_clients_page() {
	wp_enqueue_style( 'wo_admin' );
	wp_enqueue_script( 'wo_admin' );
	?>
    <div class="wrap" id="profile-page">

        <h2><?php _e( 'Clients', 'wp-oauth' ); ?>
            <a class="add-new-h2 "
               href="<?php echo admin_url( 'admin.php?page=wo_add_client' ); ?>"
               title="Batch"><?php _e( 'Add New Client', 'wp-oauth' ); ?></a>
        </h2>

        <div class="section group">
            <div class="col span_4_of_6">
				<?php $CodeTableList = new WO_Table();
				$CodeTableList->prepare_items();
				$CodeTableList->display(); ?>
            </div>

            <div class="col span_2_of_6 sidebar">
                <div class="module">
                    <h3>Technical Support</h3>
                    <div class="inner">
                        <p>
                            For technical support please submit a ticket at
                            <a href="https://wp-oauth.com/support/submit-ticket/">https://wp-oauth.com/support/submit-ticket/</a>.
                            Be sure to include as much information as possible.
                        </p>
                        <strong>Build <?php echo _WO()->version; ?></strong>
                    </div>
                </div>

                <div class="module hire-us">
                    <h3>Hire a Developer</h3>
                    <div class="inner">
                        <p>
                            If you are looking for a developer for your project, why not hire the professionals that
                            built this plugin!
                        </p>
                        <p>
                            <strong>Get a Free Quote</strong>
                        </p>
						<?php
						$current_user = wp_get_current_user();
						?>
                        <form action="https://wp-oauth.com/professional-services-request/">
                            <input type="text" name="yourname" placeholder="Enter Your Name" value="<?php echo $current_user->user_firstname; ?>" required/>
                            <input type="hidden" name="email" value="<?php echo $current_user->user_email; ?>"/>
                            <input type="hidden" name="website" value="<?php echo site_url(); ?>"/>

                            <input type="submit" class="button button-primary" value="Request more information"/>
                            <br/><br/>
                            <small>
                                Your information is private and is not shared with anyone other than our development
                                team.
                            </small>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php }