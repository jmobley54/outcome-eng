<?php

// Load parent class
require_once(dirname(dirname(__FILE__)).'/admin/scans.php');

/**
 * WP Link Status Pro Admin Scans class
 *
 * @package WP Link Status
 * @subpackage WP Link Status Pro Admin
 */
class WPLNST_Admin_Pro_Scans extends WPLNST_Admin_Scans {



	// Scan results
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show a list table for scan results
	 */
	protected function scans_results_views_table($args) {
		wplnst_require('views-pro', 'scans-results');
		$list = new WPLNST_Views_Pro_Scans_Results($args['results']);
		$list->prepare_items();
		$list->display();
	}



	/**
	 * Extra elements of results display
	 */
	public function scans_results_view_display() {
		
		// Scan results row actions
		add_filter('wplnst_results_actions_url_extended', 	 array(&$this, 'scans_results_actions_url_extended'),    10, 2);
		add_filter('wplnst_results_actions_status_extended', array(&$this, 'scans_results_actions_status_extended'), 10, 2);
		add_filter('wplnst_results_actions_anchor_extended', array(&$this, 'scans_results_actions_anchor_extended'), 10, 2);
		
		// Transversal div
		?><div id="wplnst-results-output" class="wplnst-display-none">
			<div class="wplnst-results-output-container" data-action-key=""></div>
		</div><?php
		
		// Processing div
		?><div id="wplnst-results-processing" class="wplnst-display-none">
			<div class="wplnst-results-processing-container">
				<p class="wplnst-results-processing-action wplnst-results-processing-action-run"><?php _e('Processing <b>%s</b>', 'wplnst'); ?></p>
			</div>
		</div><?php
		
		// Error output
		?><div id="wplnst-results-error" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-results-update-box-error wplnst-clearfix">
				<p class="alignleft">%s</p>
				<div class="alignright"><a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Close', 'wplnst'); ?></a></div>
			</div>
		</div><?php
		
		// Error output bulk
		?><div id="wplnst-results-error-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<p class="alignleft">%s</p>
				<div class="alignright"><a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Close', 'wplnst'); ?></a></div>
			</div>
		</div><?php
		
		// Editable URL
		?><div id="wplnst-results-edit-url" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="wplnst-results-update-box-edit"><input type="text" class="regular-text" value="%s" /></div>
				<div class="wplnst-results-update-box-buttons alignright">
					<a href="#" class="wplnst-results-output-update button button-primary button-small"><?php _e('Update URL', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm unlink
		?><div id="wplnst-results-unlink-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Unlink this URL</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Unlink', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm unlink bulk
		?><div id="wplnst-results-unlink-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Unlink selected URLs</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Unlink', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm ignore
		?><div id="wplnst-results-ignore-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Ignore this link</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Ignore', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm ignore bulk
		?><div id="wplnst-results-ignore-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Ignore selected results</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Ignore', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Undo Confirm ignore
		?><div id="wplnst-results-unignore-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Undo Ignore this link</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Undo Ignore', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Undo Confirm ignore bulk
		?><div id="wplnst-results-unignore-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Undo Ignore selected results</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Undo Ignore', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm redirection
		?><div id="wplnst-results-redir-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Apply Redirection</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Set Redirection', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm redirection bulk
		?><div id="wplnst-results-redir-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Bulk apply redirections</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Set Redirections', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm nofollow
		?><div id="wplnst-results-nofollow-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Add nofollow</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Add nofollow', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm nofollow bulk
		?><div id="wplnst-results-nofollow-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Bulk add nofollow</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Add nofollow', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm dofollow
		?><div id="wplnst-results-dofollow-confirm" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Remove nofollow</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm button button-primary button-small"><?php _e('Remove nofollow', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Confirm dofollow bulk
		?><div id="wplnst-results-dofollow-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Bulk remove nofollow</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Remove nofollow', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Editable anchor
		?><div id="wplnst-results-edit-anchor" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="wplnst-results-update-box-edit"><input type="text" class="regular-text" value="%s" /></div>
				<div class="wplnst-results-update-box-buttons alignright">
					<a href="#" class="wplnst-results-output-update button button-primary button-small"><?php _e('Update anchor text', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Recheck bulk
		?><div id="wplnst-results-recheck-confirm-bulk" class="wplnst-display-none">
			<div class="wplnst-results-update-box wplnst-clearfix">
				<div class="alignleft"><p><?php _e('Confirm <b>Recheck URL status</b>', 'wplnst'); ?></p></div>
				<div class="alignright">
					<a href="#" class="wplnst-results-output-confirm-bulk button button-primary button-small"><?php _e('Recheck status', 'wplnst'); ?></a>
					<a href="#" class="wplnst-results-output-cancel-bulk button button-small"><?php _e('Cancel', 'wplnst'); ?></a>
				</div>
			</div>
		</div><?php
		
		// Headers window
		?><div id="wplnst-results-headers" class="wplnst-display-none" data-caption="<?php esc_attr(_e('Link headers', 'wplnst')); ?>"></div><?php
		
		// Headers window content template
		?><div id="wplnst-results-headers-template" class="wplnst-display-none"><div>
			<div class="wplnst-results-headers-container">
				<p class="wplnst-results-headers-url"></p>
				<h3><?php _e('Response headers', 'wplnst'); ?></h3>
				<div class="wplnst-results-headers-response"></div>
				<h3><?php _e('Request headers', 'wplnst'); ?></h3>
				<div class="wplnst-results-headers-request"></div>
			</div>
		</div></div><?php
		
		// Template window
		?><div id="wplnst-window-template" class="wplnst-display-none"><div>
			<div class="wplnst-results-headers-top">
				<h2>%s</h2>
				<a class="wplnst-results-headers-top-close wplnst_lightboxed_close" href="#">&nbsp;</a>
			</div>
			<div class="wplnst-results-headers-content"></div>
		</div></div><?php
	}



	/**
	 * Define column URL row actions
	 */
	public function scans_results_actions_url_extended($actions, $item) {
		
		// Prepare location identifier
		$data_loc_id = 'data-loc-id="'.((int) $item['loc_id']).'"';
		
		// Prepare unlinkable
		$unlinkable= !('blogroll' == $item['object_type'] || ('comments' == $item['object_type'] && 'comment_author_url' == $item['object_field']) || ('posts' == $item['object_type'] && 0 === strpos($item['object_field'], 'custom_field_url_')));
		
		// Edit link action
		$actions['wplnst-action-url-edit'] = '<a href="#" '.$data_loc_id.' data-action="url_edit" class="wplnst-results-action">'.__('Edit URL', 'wplnst').'</a>';
		
		// Redirection action
		if ('3' == $item['status_level'] && !empty($item['redirect_url']) && !empty($item['redirect_url_id']))
			$actions['wplnst-action-url-redir'] = '<a href="#" id="wplnst-action-url-redir-'.$item['loc_id'].'" '.$data_loc_id.' data-action="url_redir" class="wplnst-results-action">'.__('Apply Redirection', 'wplnst').'</a>';
		
		// Unlink action
		if ($unlinkable && ('links' == $item['link_type'] || 'images' == $item['link_type']))
			$actions['wplnst-action-url-unlink'] = '<span class="trash"><a href="#" '.$data_loc_id.' data-action="url_unlink" class="wplnst-results-action" title="'.(('links' == $item['link_type'])? __('Remove link but leave anchor text', 'wplnst') : __('Remove image from content', 'wplnst')).'">'.(('links' == $item['link_type'])? __('Unlink', 'wplnst') : __('Remove', 'wplnst')).'</a></span>';
		
		// Nofollow
		if ($unlinkable && 'links' == $item['link_type']) {
			$actions['wplnst-action-url-nofollow']  = '<a href="#" id="wplnst-action-url-nofollow-'.$item['loc_id'].'" '.$data_loc_id.' data-action="url_nofollow" class="wplnst-results-action'.($item['nofollow']? ' wplnst-display-none' : '').'">'.__('Add nofollow', 'wplnst').'</a>';
			$actions['wplnst-action-url-nofollow'] .= '<a href="#" id="wplnst-action-url-dofollow-'.$item['loc_id'].'" '.$data_loc_id.' data-action="url_dofollow" class="wplnst-results-action'.($item['nofollow']? '' : ' wplnst-display-none').'">'.__('Remove nofollow', 'wplnst').'</a>';
		}
		
		// Ignore or unignore result
		$actions['wplnst-action-url-ignore']   = '<a href="#" id="wplnst-action-url-ignore-'.$item['loc_id'].'" '.$data_loc_id.' data-action="url_ignore" class="wplnst-results-action'.($item['ignored']? ' wplnst-display-none' : '').'">'.__('Ignore', 'wplnst').'</a>';
		$actions['wplnst-action-url-ignore']  .= '<a href="#" id="wplnst-action-url-unignore-'.$item['loc_id'].'" '.$data_loc_id.' data-action="url_unignore" class="wplnst-results-action'.($item['ignored']? '' : ' wplnst-display-none').'">'.__('Undo Ignore', 'wplnst').'</a>';
		
		// Visit URL
		$actions['wplnst-action-url-visit'] = '<a href="'.esc_url($item['url']).'" target="_blank">'.__('Visit', 'wplnst').'</a>';
		
		// Done
		return $actions;
	}



	/**
	 * Define column Status row actions
	 */
	public function scans_results_actions_status_extended($actions, $item) {
		
		// Prepare location identifier
		$data_loc_id = 'data-loc-id="'.((int) $item['loc_id']).'"';
		
		// Recheck status
		$actions['wplnst-action-url-status'] = '<a href="#" '.$data_loc_id.' data-action="url_status" target="_blank" class="wplnst-results-action">'.__('Recheck status', 'wplnst').'</a>';
		
		// Show headers
		$actions['wplnst-action-url-headers'] = '<a href="#" '.$data_loc_id.' data-action="url_headers" target="_blank" class="wplnst-results-action">'.__('Show headers', 'wplnst').'</a>';
		
		// Done
		return $actions;
	}



	/**
	 * Define column Anchor row actions
	 */
	public function scans_results_actions_anchor_extended($actions, $item) {
		
		// Prepare location identifier
		$data_loc_id = 'data-loc-id="'.((int) $item['loc_id']).'"';
		
		// Edit link action
		$actions['wplnst-action-anchor-edit'] = '<a href="#" '.$data_loc_id.' data-action="anchor_edit" class="wplnst-results-action">'.__('Edit anchor text', 'wplnst').'</a>';
		
		// Done
		return $actions;
	}



	// Scan crawler
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Wrapper function to run scan from Alive class
	 */
	protected function scans_crawler_run($scan_id, $hash) {
		WPLNST_Core_Pro_Alive::run($scan_id, $hash);
	}



}