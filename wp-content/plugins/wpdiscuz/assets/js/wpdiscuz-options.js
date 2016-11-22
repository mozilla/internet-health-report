jQuery(document).ready(function ($) {
    if (location.href.indexOf('wpdiscuz_options_page') >= 0) {
        $('.wpdiscuz-color-picker').colorPicker();

        if (!$('ul.wpdiscuz-addons-options').html().trim()) {
            $('#wpdiscuz-addons-options').remove();
        }
    }

    if ($('#show_sorting_buttons').attr('checked')) {
        $('#row_mostVotedByDefault').removeClass('wc-hidden');
    } else {
        $('#row_mostVotedByDefault').addClass('wc-hidden');
    }

    $('#show_sorting_buttons').change(function () {
        if ($(this).is(':checked')) {
            $('#row_mostVotedByDefault').removeClass('wc-hidden');
        } else {
            $('#row_mostVotedByDefault').addClass('wc-hidden');
        }
    });   
    
    $('#wc_share_button_fb').click(function(){
        if ($(this).is(':checked')) {
            $('#wpc-fb-api-cont').attr('style','');
        } else {
            $('#wpc-fb-api-cont').attr('style','display:none');
        }
    });   
});