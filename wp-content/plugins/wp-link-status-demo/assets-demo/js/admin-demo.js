;jQuery(document).ready(function($) {



	var busy = {}, nonce = $('#wplnst-results').attr('data-nonce'), nonce_advanced_display = $('#wplnst-results').attr('data-nonce-advanced-display');;



	$('.wplnst-results-action').click(function() {
		
		var action = $(this).attr('data-action');
		var loc_id = $(this).attr('data-loc-id');
		if ('undefined' == typeof action || '' == action || 'undefined' == typeof loc_id || '' == loc_id)
			return false;
		
		var key = action + '_' + loc_id;
		if (key in busy && false !== busy[key])
			return false;
		
		busy[key] = {}
		busy[key]['div-actions'] = $(this).closest('.row-actions');
		busy[key]['action'] = action;
		busy[key]['loc_id'] = loc_id;
		busy[key]['desc'] = $(this).text();
		
		$('.wplnst-row-actions-' + loc_id).hide();
		results_bulkactions_disable();
		
		var output = $($('#wplnst-results-output').html());
		output.addClass('wplnst-results-action-' + key);
		output.attr('data-action-key', key);
		output.insertAfter(busy[key]['div-actions']);
		
		if ('url_edit' == action) {
			output.html($('#wplnst-results-edit-url').html().replace('%s', $('#wplnst-results-url-loc-' + loc_id).text().esc_html()));
			output.find('.wplnst-results-update-box-edit input').eq(0).focus();
			
		} else if ('url_unlink' == action) {
			output.html($('#wplnst-results-unlink-confirm').html());
			
		} else if ('url_ignore' == action) {
			output.html($('#wplnst-results-ignore-confirm').html());
			
		} else if ('url_unignore' == action) {
			output.html($('#wplnst-results-unignore-confirm').html());
			
		} else if ('url_redir' == action) {
			output.html($('#wplnst-results-redir-confirm').html());
			
		} else if ('url_nofollow' == action) {
			output.html($('#wplnst-results-nofollow-confirm').html());
			
		} else if ('url_dofollow' == action) {
			output.html($('#wplnst-results-dofollow-confirm').html());
			
		} else if ('url_status' == action) {
			results_bulkactions_enable();
			rollback_actions(key);
			submit(key, action, loc_id, '');
			return false;
			
		} else if ('url_headers' == action) {
			results_bulkactions_enable();
			rollback_actions(key);
			submit(key, action, loc_id, '');
			return false;
			
		} else if ('anchor_edit' == action) {
			output.html($('#wplnst-results-edit-anchor').html().replace('%s', $('#wplnst-results-anchor-loc-' + loc_id).text().esc_html()));
			output.find('.wplnst-results-update-box-edit input').eq(0).focus();
		}
		
		output.show();
		return false;
	});



	$(document).on('click', '.wplnst-results-output-update', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {
				
				if ('bulk_url' == key || 'bulk_anchor' == key) {
					submit_bulk(output);
				
				} else if (key in busy && false !== busy[key]) {
					
					var value = '';
					if ('url_edit' == busy[key]['action'] || 'anchor_edit' == busy[key]['action']) {
						
						var value = false;
						var field = output.find('.wplnst-results-update-box-edit input').eq(0);
						if ('undefined' != typeof field)
							value = $(field).val();
						
						if ('undefined' == typeof value || false === value) {
							alert('Entered value error');
							return;
						}
						
						busy[key]['value'] = value;
					}
					
					submit(key, busy[key]['action'], busy[key]['loc_id'], value);
				}
			}
		}
		
		return false;
	});



	$(document).on('keydown', '.wplnst-results-update-box-edit input', function(e) {
		if (e.keyCode == 13) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-update').eq(0).click();
			return false;
		} else if (e.keyCode == 27) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-cancel').eq(0).click();
			return false;
		}
	});
	
	$(document).on('keydown', '.wplnst-results-bulkactions-area .wplnst-results-update-box-edit input', function(e) {
		if (e.keyCode == 27) {
			$(this).closest('.wplnst-results-output-container').find('.wplnst-results-output-cancel-bulk').eq(0).click();
			return false;
		}
	});



	$(document).on('click', '.wplnst-results-output-confirm', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {

				if (key in busy && false !== busy[key])
					submit(key, busy[key]['action'], busy[key]['loc_id'], '');
			}
		}
		
		return false;
	});



	$(document).on('click', '.wplnst-results-output-cancel', function(e) {
		
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length) {
			
			var key = output.attr('data-action-key');
			if ('undefined' != typeof key && key.length) {
				output.remove();
				results_bulkactions_enable();
				rollback_actions(key);
			}
		}
		
		return false;
	});



	function submit(key, action, loc_id, value) {
		wplnst_paywall_show($);
		return false;
	}



	function rollback_actions(key) {
		if (key in busy && false !== busy[key]) {
			$('.wplnst-row-actions-' + busy[key]['loc_id']).removeClass('visible').show();
			busy[key] = false;
		}
	}



	$('#bulk-action-selector-top').change(function() {
		results_bulkactions('top');
		return false;
	});
	
	$('#bulk-action-selector-bottom').change(function() {
		results_bulkactions('bottom');
		return false;
	});
	
	$('.wplnst-results-bulkactions-top .button.action').click(function() {
		results_bulkactions('top');
		return false;
	});
	
	$('.wplnst-results-bulkactions-bottom .button.action').click(function() {
		results_bulkactions('bottom');
		return false;
	});
	
	function results_bulkactions(which) {
		
		var action = $('#bulk-action-selector-' + which).val();
		if ('bulk_unlink' == action || 'bulk_ignore' == action || 'bulk_unignore' == action || 'bulk_anchor' == action || 'bulk_url' == action || 'bulk_status' == action || 'bulk_redir' == action || 'bulk_nofollow' == action || 'bulk_dofollow' == action) {
			
			$('.wplnst-results-update-box-error').closest('.wplnst-results-output-container').each(function() {
				var key = $(this).attr('data-action-key');
				if ('undefined' != typeof key && key.length)
					rollback_actions(key);
				$(this).remove();
			});
			
			$('.wplnst-row-actions').hide();
			results_bulkactions_disable();
			
			var output = $($('#wplnst-results-output').html());
			output.attr('data-action-key', action);

			if ('bulk_url' == action) {
				output.html($('#wplnst-results-edit-url').html().replace('%s', ''));
				output.find('.wplnst-results-output-cancel').eq(0).removeClass('wplnst-results-output-cancel').addClass('wplnst-results-output-cancel-bulk');
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				output.find('.wplnst-results-update-box-edit input').eq(0).focus();
				
			} else if ('bulk_unlink' == action) {
				output.html($('#wplnst-results-unlink-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_ignore' == action) {
				output.html($('#wplnst-results-ignore-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_unignore' == action) {
				output.html($('#wplnst-results-unignore-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_anchor' == action) {
				output.html($('#wplnst-results-edit-anchor').html().replace('%s', ''));
				output.find('.wplnst-results-output-cancel').eq(0).removeClass('wplnst-results-output-cancel').addClass('wplnst-results-output-cancel-bulk');
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				output.find('.wplnst-results-update-box-edit input').eq(0).focus();
				
			} else if ('bulk_status' == action) {
				output.html($('#wplnst-results-recheck-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_redir' == action) {
				output.html($('#wplnst-results-redir-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_nofollow' == action) {
				output.html($('#wplnst-results-nofollow-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
				
			} else if ('bulk_dofollow' == action) {
				output.html($('#wplnst-results-dofollow-confirm-bulk').html());
				$('#wplnst-results-bulkactions-area-' + which).append(output).show();
			}
		}
	}
	
	$(document).on('click', '.wplnst-results-output-confirm-bulk', function(e) {
		submit_bulk($(this).closest('.wplnst-results-output-container'));
		return false;
	});
	
	function submit_bulk(output) {
		wplnst_paywall_show($);
		return false;
	}
	
	$(document).on('click', '.wplnst-results-output-cancel-bulk', function(e) {
		var output = $(this).closest('.wplnst-results-output-container');
		if ('undefined' != typeof output && output.length)
			output.remove();
		results_bulkactions_enable();
		results_bulkactions_restore();
		return false;
	});
	
	function results_bulkactions_restore() {
		$('.wplnst-results-bulkactions-area').hide();
		$('.wplnst-row-actions').removeClass('visible').show();
	}
	
	function results_bulkactions_disable() {
		$('#bulk-action-selector-top').attr('disabled', 'disabled');
		$('#bulk-action-selector-bottom').attr('disabled', 'disabled');
		$('.wplnst-results-bulkactions-top .button.action').attr('disabled', 'disabled');
		$('.wplnst-results-bulkactions-bottom .button.action').attr('disabled', 'disabled');
	}
	
	function results_bulkactions_enable() {
		$('#bulk-action-selector-top').removeAttr('disabled');
		$('#bulk-action-selector-bottom').removeAttr('disabled');
		$('.wplnst-results-bulkactions-top .button.action').removeAttr('disabled');
		$('.wplnst-results-bulkactions-bottom .button.action').removeAttr('disabled');
	}



	$('#wplnst-results-filters-toggle').click(function() {
		$(this).addClass('wplnst-display-none');
		$('#wplnst-results-filters').addClass('wplnst-display-none');
		$('#wplnst-results-filters-advanced').removeClass('wplnst-display-none');
		$.post(ajaxurl, { 'action' : 'wplnst_results_advanced_display', 'nonce' : nonce_advanced_display, 'display' : 'on' }, function(e) {});
		return false;
	});
	
	$('#wplnst-results-filters-advanced-close').click(function() {
		$('#wplnst-results-filters').removeClass('wplnst-display-none');
		$('#wplnst-results-filters-advanced').addClass('wplnst-display-none');
		$('#wplnst-results-filters-toggle').removeClass('wplnst-display-none');
		$.post(ajaxurl, { 'action' : 'wplnst_results_advanced_display', 'nonce' : nonce_advanced_display, 'display' : 'off' }, function(e) {});
		return false;
	});
	
	$('#wplnst-results-filters-advanced-reset').click(function() {
		if (confirm($(this).attr('data-confirm'))) {
			$('#wplnst-results-filters-advanced select').each(function() {
				this.selectedIndex = 0;
			});
			$('#wplnst-results-filters-advanced input:text').each(function() {
				$(this).val('');
			});
		}
		$(this).blur();
		return false;
	});
	
	$('.wplnst-filter-advanced-text').keydown(function(e) {
		if (e.keyCode == 13) {
			$('#wplnst-filter-advanced-button').click();
			return false;
		}
	});
	
	$('#wplnst-filter-advanced-button').click(function() {
		wplnst_paywall_show($);
		return false;
	});



	function data_label(name) {
		return $('#wplnst-results').attr('data-label-' + name);
	}

	function data_label_bulk(name) {
		return $('#wplnst-results-bulkactions-area-top').attr('data-label-' + name);
	}


});