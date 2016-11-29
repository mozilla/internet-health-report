/*globals jQuery, ajaxurl, wpml_st_disable_notices_strings */

function wpml_st_hide_strings_scan_notices(element, callback) {
	"use strict";

		var buttonNo = wpml_st_disable_notices_strings.no;
		var buttonYes = wpml_st_disable_notices_strings.yes;

		var dialog = jQuery('<div title="' + wpml_st_disable_notices_strings.title + '"><p>' + wpml_st_disable_notices_strings.message + '</p></div>');
		dialog.css('display', 'none');
		dialog.dialog({
										resizable: false,
										height:    "auto",
										width:     "auto",
										modal:     true,
										buttons:   {
											buttonNo:  {
												text:  buttonNo,
												click: function () {
													if (typeof callback === 'function') {
														callback();
													}
													dialog.dialog("close");
												}
											},
											buttonYes: {
												text:  buttonYes,
												class: 'button-primary',
												click: function () {
													jQuery.ajax({
																				url:      ajaxurl,
																				type:     'POST',
																				data:     {
																					action: 'hide_strings_scan_notices'
																				},
																				dataType: 'json',
																				complete: function () {
																					if (typeof callback === 'function') {
																						callback();
																					}
																					dialog.dialog("close");
																				}
																			});

												}
											}
										}
									});
}