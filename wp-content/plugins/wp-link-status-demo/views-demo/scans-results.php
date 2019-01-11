<?php

// Load parent class
require_once(dirname(dirname(__FILE__)).'/views/scans-results.php');

/**
 * WP Link Status Pro Views Scans Results class
 *
 * @package WP Link Status Pro
 * @subpackage WP Link Status Pro Views
 */
class WPLNST_Views_Pro_Scans_Results extends WPLNST_Views_Scans_Results {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/*
	 * Advanced search check
	 */
	private $is_advanced = false;



	// Column actions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'bulk_url' 		=> __('Edit URL', 			'wplnst'),
			'bulk_unlink' 	=> __('Unlink', 			'wplnst'),
			'bulk_ignore' 	=> __('Ignore', 			'wplnst'),
			'bulk_unignore' => __('Undo Ignore', 		'wplnst'),
			'bulk_anchor' 	=> __('Edit anchor', 		'wplnst'),
			'bulk_status' 	=> __('Recheck status', 	'wplnst'),
			'bulk_redir' 	=> __('Apply Redirection', 	'wplnst'),
			'bulk_nofollow' => __('Add nofollow',	 	'wplnst'),
			'bulk_dofollow' => __('Remove nofollow',	'wplnst'),
		);
	}



	/**
	 * Check if needed a cb column
	 */
	protected function get_columns_cb() {
		return true;
	}



	/**
	 * Generate the table navigation above or below the table
	 */
	protected function display_tablenav($which) {
		
		// Check advanced
		$this->is_advanced = (!empty($_GET['adv']) && 'on' == $_GET['adv']);
		if (!$this->is_advanced) {
			$user_is_advanced = get_user_meta(get_current_user_id(), 'wplnst_advanced_search', true);
			if (empty($user_is_advanced) || 'on' == $user_is_advanced)
				$this->is_advanced = true;
		}
		
		// Top advanced
		if ('top' == $which)
			$this->filters_advanced();
		
		// Tablen navigation
		?><div class="tablenav <?php echo esc_attr($which); ?>">
			<div class="alignleft actions bulkactions wplnst-results-bulkactions-<?php echo $which; ?>">
				<?php $this->bulk_actions($which); ?>
			</div>
			<?php if ('top' == $which) $this->filters(); ?>
			<?php $this->pagination($which); ?>
			<input id="wplnst-results-filters-toggle" class="button<?php echo $this->is_advanced? ' wplnst-display-none' : ''; ?>" type="button" value="&nbsp;" title="<?php _e('Toggle to the advanced search panel', 'wplnst'); ?>" data-caption="<?php __('Advanced Search', 'wplnst'); ?>" />
			<br class="clear" />
		</div>
		<div id="wplnst-results-bulkactions-area-<?php echo $which; ?>" class="wplnst-results-bulkactions-area wplnst-display-none"<?php if ('top' == $which) echo ' data-label-bulk-unlink="'.esc_attr__('Unlink URLs', 'wplnst').'" data-label-bulk-ignore="'.esc_attr__('Ignore results', 'wplnst').'" data-label-bulk-unignore="'.esc_attr__('Undo ignore results', 'wplnst').'" data-label-bulk-anchor="'.esc_attr__('Anchor text edition', 'wplnst').'" data-label-bulk-url="'.esc_attr__('URL edition', 'wplnst').'" data-label-bulk-status="'.esc_attr__('URL recheck status', 'wplnst').'" data-label-bulk-redir="'.esc_attr__('Apply redirections', 'wplnst').'" data-label-bulk-nofollow="'.esc_attr__('Add nofollow', 'wplnst').'" data-label-bulk-dofollow="'.esc_attr__('Remove nofollow', 'wplnst').'"'; ?>></div><?php
	}



	/**
	 * Additional classes
	 */
	protected function filters_classes() {
		return $this->is_advanced? ' wplnst-display-none' : '';
	}



	/**
	 * Advanced search results
	 */
	private function filters_advanced() {
		
		
		/* Filters */
		
		// Filters
		$select = '';
		$fields = $this->filters_fields_advanced();
		foreach ($fields as $key => $field)
			$select .= '<select id="wplnst-filter-advanced-'.$key.'">'.((false !== $field['title'])? '<option value="">'.esc_html($field['title']).'</option>' : '').$field['options'].'</select>';
		
		// Wrap first line
		$select = '<div class="wplnst-results-filters-advanced-row wplnst-clearfix"><label id="wplnst-filter-advanced-label-filters">'.__('Common filters', 'wplnst').'</label> '.$select.'</div>';
		
		
		/* Extended filters */
		$extend = '';
		$fields_ext = $this->filters_fields_extended();
		foreach ($fields_ext as $key => $field)
			$extend .= '<select id="wplnst-filter-advanced-'.$key.'">'.((false !== $field['title'])? '<option value="">'.esc_html($field['title']).'</option>' : '').$field['options'].'</select>';
		
		// Wrap first line
		$extend = '<div class="wplnst-results-filters-advanced-row wplnst-clearfix"><label id="wplnst-filter-extended-label-filters">'.__('Extended', 'wplnst').'</label> '.$extend.'</div>';
		
		
		/* Search by URL */
		
		// URL select
		$options = '';
		$filters = WPLNST_Core_Types::get_url_search_filters();
		foreach ($filters as $type => $name)
			$options .= '<option '.((!empty($this->results->search_url_type) && $this->results->search_url_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		$url_select = '<select id="wplnst-filter-advanced-url-options" class="wplnst-filter-advanced-search-select">'.$options.'</select>';
		
		// URL search
		$url = '';
		$url .= '<label id="wplnst-filter-advanced-label-url" for="wplnst-filter-advanced-url">Search by URL</label> <input type="text" id="wplnst-filter-advanced-url" class="regular-text wplnst-filter-advanced-text" value="'.esc_attr($this->results->search_url).'" maxlength="512" />';
		$url = '<div class="wplnst-results-filters-advanced-row wplnst-clearfix">'.$url.$url_select.'</div>';
		
		
		/* Search by anchor */
		
		// Anchor select
		$options = '';
		$filters = WPLNST_Core_Types::get_anchor_search_filters();
		foreach ($filters as $type => $name)
			$options .= '<option '.((!empty($this->results->search_anchor_type) && $this->results->search_anchor_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		$anchor_select = '<select id="wplnst-filter-advanced-anchor-options" class="wplnst-filter-advanced-search-select">'.$options.'</select>';
		
		// End search button
		$search_button = '<input type="button" id="wplnst-filter-advanced-button" data-fields="'.implode(',', array_keys(array_merge($fields, $fields_ext))).'" data-href="'.esc_attr($this->base_url).'" class="button-primary" value="'.__('Filter Results', 'wplnst').'" maxlength="512" />';
		
		// By anchor
		$anchor = '';
		$anchor .= '<label id="wplnst-filter-advanced-label-anchor" for="wplnst-filter-advanced-anchor">By anchor text</label> <input type="text" id="wplnst-filter-advanced-anchor" class="regular-text wplnst-filter-advanced-text" value="'.esc_attr($this->results->search_anchor).'" />';
		$anchor = '<div class="wplnst-results-filters-advanced-row wplnst-clearfix">'.$anchor.$anchor_select.$search_button.'</div>';
		
		
		// Advanced panel
		?><div id="wplnst-results-filters-advanced"<?php if (!$this->is_advanced) : ?> class="wplnst-display-none"<?php endif; ?>>
			<div class="wplnst-clearfix">
				<div id="wplnst-results-filters-advanced-right">
					<a href="#" id="wplnst-results-filters-advanced-close" title="<?php _e('Close advanced search panel', 'wplnst'); ?>">&nbsp;</a>
					<a href="#" id="wplnst-results-filters-advanced-reset" title="<?php _e('Reset advanced search fields', 'wplnst'); ?>" data-confirm="<?php _e('Reset advanced search fields?', 'wplnst'); ?>">&nbsp;</a>
				</div>
				<?php echo $select.$extend.$url.$anchor; ?>
			</div>
		</div><?php
	}



	/**
	 * Fields for filters, advanced method
	 */
	private function filters_fields_advanced() {
		
		// Basic fields
		$fields = $this->filters_fields();
		
		
		/* Ignored */
		
		$options_ignored = '';
		$ignored_types = WPLNST_Core_Types::get_ignored_types();
		foreach ($ignored_types as $type => $name)
			$options_ignored .= '<option '.((!empty($this->results->ignored_type) && $this->results->ignored_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['ig'] = array(
			'type' => 'select',
			'title' => __('Not ignored results', 'wplnst'),
			'options' => $options_ignored,
		);
		
		
		// Done
		return $fields;
	}



	/**
	 * Fields for filters, extended method
	 */
	private function filters_fields_extended() {
		
		// Basic fields
		$fields = array();
		
		
		/* SEO links */
		
		$options_seolinks = '';
		$seo_links_types = WPLNST_Core_Types::get_seo_link_types();
		foreach ($seo_links_types as $type => $name)
			$options_seolinks .= '<option '.((!empty($this->results->seo_link_type) && $this->results->seo_link_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['slt'] = array(
			'type' => 'select',
			'title' => __('SEO links', 'wplnst'),
			'options' => $options_seolinks,
		);
		
		
		/* Protocol */
		
		$options_protocol = '';
		$protocol_types = WPLNST_Core_Types::get_protocol_types();
		foreach ($protocol_types as $type => $name)
			$options_protocol .= '<option '.((!empty($this->results->protocol_type) && $this->results->protocol_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['pt'] = array(
			'type' => 'select',
			'title' => __('Protocol', 'wplnst'),
			'options' => $options_protocol,
		);
		
		
		/* Special */

		$options_special = '';
		$special_types = WPLNST_Core_Types::get_special_types();
		foreach ($special_types as $type => $name)
			$options_special .= '<option '.((!empty($this->results->special_type) && $this->results->special_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['sp'] = array(
			'type' => 'select',
			'title' => __('Special', 'wplnst'),
			'options' => $options_special,
		);		
		
		
		/* Action */

		$options_action = '';
		$action_types = WPLNST_Core_Types::get_action_types();
		foreach ($action_types as $type => $name)
			$options_action .= '<option '.((!empty($this->results->action_type) && $this->results->action_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['ac'] = array(
			'type' => 'select',
			'title' => __('Action', 'wplnst'),
			'options' => $options_action,
		);
		
		
		/* Destination types */
		
		$options_dtypes = '';
		$dest_types = WPLNST_Core_Types::get_destination_types();
		foreach ($dest_types as $dest_type => $dest_type_name) {
			if ('all' != $dest_type)
				$options_dtypes .= '<option '.((!empty($this->results->destination_type) && $this->results->destination_type == $dest_type)? 'selected' : '').' value="'.esc_attr($dest_type).'">'.esc_html($dest_type_name).'</option>';
		}
		
		// Check filter
		$fields['dtype'] = array(
			'type' => 'select',
			'title' => __('External and internal URLs', 'wplnst'),
			'options' => $options_dtypes,
		);
		
		
		/* Order */
		
		// Defaults options
		$crawl_order_types = WPLNST_Core_Types::get_crawl_order();
		if ('desc' == $this->results->scan->crawl_order) {
			$option_1 = $crawl_order_types['desc'];
			$option_2 = $crawl_order_types['asc'];
			$option_2_key = 'asc';
		} else {
			$option_1 = $crawl_order_types['asc'];
			$option_2 = $crawl_order_types['desc'];
			$option_2_key = 'desc';
		}
		
		$options_order = '<option '.((!empty($this->results->order_type) && $this->results->order_type == $option_2_key)? 'selected' : '').' value="'.esc_attr($option_2_key).'">'.esc_html($option_2).'</option>';;
		$order_types = WPLNST_Core_Types::get_order_types();
		foreach ($order_types as $type => $name)
			$options_order .= '<option '.((!empty($this->results->order_type) && $this->results->order_type == $type)? 'selected' : '').' value="'.esc_attr($type).'">'.esc_html($name).'</option>';
		
		// Check filter
		$fields['or'] = array(
			'type' => 'select',
			'title' => $option_1,
			'options' => $options_order,
		);
		
		
		// Done
		return $fields;
	}



}