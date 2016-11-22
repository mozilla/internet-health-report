jQuery(document).on( 'click', '.wpdiscuz_addon_note .notice-dismiss', function() {
	jQuery.ajax({url: ajaxurl, data: { action: 'dismiss_wpdiscuz_addon_note'}})
})