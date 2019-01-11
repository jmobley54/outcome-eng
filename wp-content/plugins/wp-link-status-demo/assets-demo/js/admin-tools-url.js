;jQuery(document).ready(function($) {



	var tu_link_index = 0,
		tu_link_pack  = 5,
		tu_odd = false, tu_cancel = false,
		tu_op, tu_op_title, tu_db, tu_links;



	$('#wplnst-tu-form').submit(function() {
		return false;
	});



	$('#wplnst-tu-mode').change(function() {
		tu_button($(this).val());
	});



	$('#wplnst-tu-button-test').click(function() {
		if (!$(this).hasClass('button-disabled'))
			tu_start('test');
		return false;
	});



	$('#wplnst-tu-button-update').click(function() {
		if (!$(this).hasClass('button-disabled')) {
			if (!$(this).hasClass('wplnst-paywall-link'))
				tu_start('update');
		}
		return false;
	});



	function tu_button(value) {
		var hide = ('test' == value)? 'update' : 'test';
		$('#wplnst-tu-button-' + hide).hide();
		$('#wplnst-tu-button-' + value).css('display', 'inline-block');
	}
	
	tu_button($('#wplnst-tu-mode').val());



	function tu_start(mode) {
		
		tu_db = ('update' == mode);
		if (tu_db && !confirm($('#wplnst-tu-form').attr('data-confirm')))
			return false;
		
		tu_op = $('#wplnst-tu-op').val();
		tu_op_title = $('#wplnst-tu-op option:selected').text();
		
		tu_links = [];
		
		var links = $('#wplnst-tu-urls').val().split("\n");
		$.each(links, function(index, link) {
			if ('' !== link && -1 == $.inArray(link, tu_links))
				tu_links[tu_links.length] = link;
		});
		
		if (!tu_links.length) {
			alert($('#wplnst-tu-form').attr('data-nolinks'));
			return;
		}
		
		$('#wplnst-tu-form').hide();
		$('#wplnst-tu-output').hide();
		$('#wplnst-tu-output-rows').html('');
		
		tu_submit();
	};



	function tu_submit() {
		
		var i, j, tr, posts, allfound, pack = [], pack_end = false;
		var td_split = '<td colspan="3" style="padding: 0; font-size: 1px; height: 8px;">&nbsp;</td></tr>';
		
		for (i = tu_link_index; i < tu_link_index + tu_link_pack; i++) {
			pack[pack.length] = tu_links[i];
			if (i >= tu_links.length - 1) {
				pack_end = true;
				break;
			}
		}
		
		if (0 == pack.length) {
			tu_end();
			return;
		}
		
		if (tu_cancel) {
			tu_end();
			return;
		}
		
		$('#wplnst-tu-progress').html( tu_label('processing') + ' <b>' + tu_op_title + '</b> ' + ((tu_link_index + tu_link_pack > tu_links.length)? tu_links.length : tu_link_index + tu_link_pack)  + '/' + tu_links.length + ' - ' + (tu_db? '<b>' + tu_label('updating') + '</b>' : '<span style="color: grey;">' + tu_label('testmode') + '</span>') + ' - <span><a id="wplnst-tu-cancel" href="#">' + tu_label('cancel') + '</a></span>').show();
		
		$.post(ajaxurl, {'action' : $('#wplnst-tu-action').val(), 'nonce' : $('#wplnst-tu-nonce').val(), 'op' : tu_op, 'db' : (tu_db? 1 : 0), 'urls' : pack.join("\n")}, function(e) {
			
			if ('undefined' != typeof e.data && e.data.length) {
				
				$('#wplnst-tu-output').show();
				
				$.each(e.data, function(index, row) {
					
					posts = '';
					tu_odd = !tu_odd;
					tr = '<tr' + (tu_odd? ' class="odd"' : '') + '>';
					
					if (row.posts.length) {
						
						allfound = true;
						$.each(row.posts, function(index2, post) {
							allfound = post.notfound? false : allfound;
							posts += tr + '<td class="wplnst-tu-output-column-link wplnst-tu-url-result"><a href="' + post.permalink + '" target="_blank"' + (post.notfound? ' style="color: orange;"' : (post.previous? ' style="color: green;"' : '')) + '>' + post.permalink + '</a> &nbsp;&#8212;&nbsp; <a href="post.php?post=' + post.ID + '&action=edit" target="_blank">Edit</a>' + ((post.redirect_url || post.redirect_error)? '<br />&rarr;&nbsp;' + (post.redirect_error? '<b style="color: red">' + post.redirect_error + '</b>' + (('' === post.redirect_status)? '' : ' <span class="wplnst-results-url-error-code">' + post.redirect_status + '</span>') : '<a href="' + post.redirect_url + '" target="_blank" style="color: green">' + post.redirect_url + '</a>' + (('' === post.redirect_status)? '' : ' &nbsp; <strong class="">' + post.redirect_status + '</strong>')) : '') + '</td><td class="wplnst-tu-output-column-anchor">' + post.text + '</td><td class="wplnst-tu-output-column-result">' + post.result + '</td></tr>';
						});
						
						$('#wplnst-tu-output-rows').append(tr + '<td colspan="3" class="wplnst-tu-url-' + (allfound? 'found' : 'onlymatch') + '"><b>' + row.url + '</b><br /><span style="color: grey">' + row.base_url + '</span></td></tr>' + posts + tr + td_split);
						
					} else {
						
						$('#wplnst-tu-output-rows').append(tr + '<td colspan="3" class="wplnst-tu-url-notfound"><b>' + row.url + '</b><br /><span style="color: grey">' + row.base_url + '</span></td></tr>' + tr + '<td colspan="3">&nbsp;<span style="color: red;">' + tu_label('nofoundentries') + '</span></td></tr>' + tr + td_split);
					}
					
				});
			}
		
		}).fail(function() {
			alert( tu_label('server-comm-error') );
		
		}).always(function() {
			
			if (tu_cancel) {
				tu_end();
			
			} else if (!pack_end) {
				tu_link_index += tu_link_pack;
				tu_submit();
				
			} else {
				tu_end();
			}
			
		});
	};



	function tu_end() {
		$('#wplnst-tu-progress').hide();
		$('#wplnst-tu-terminated').html( '<div class="wplnst-clearfix"><div class="alignleft">' + (tu_cancel? tu_label('cancelled') : tu_label('finished')) + ' <b>' + tu_op_title.esc_html() + '</b>' + ' - ' + tu_label('processed') + ' ' + (tu_cancel? ((tu_link_index + tu_link_pack > tu_links.length)? tu_links.length : tu_link_index + tu_link_pack) : '100%') + ' ' + tu_label('of') + ' ' + tu_links.length + ' ' + tu_label('links') + ' - ' + (tu_db? '<b>' + tu_label('updating') + '</b>' : '<span style="color: grey;">' + tu_label('testmode') + '</span>') + '</div><div class="alignright">&laquo; <a href="#" id="wplnst-tu-back">' + tu_label('backtotheform') + '</a></div></div>').show();
		tu_link_index = 0;
		tu_cancel = false;
	};



	$(document).on('click', '#wplnst-tu-cancel', function(e) {
		tu_cancel = true;
		$(this).closest('span').html( tu_label('cancelling') + '...' );
		return false;
	});



	$(document).on('click', '#wplnst-tu-back', function(e) {
		$('#wplnst-tu-progress').hide();
		$('#wplnst-tu-terminated').hide();
		$('#wplnst-tu-form').show();
		return false;
	});



	function tu_label(name) {
		return $('#wplnst-tu-form').attr('data-label-' + name);
	}



});