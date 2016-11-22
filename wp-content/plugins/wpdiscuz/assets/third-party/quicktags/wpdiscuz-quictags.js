jQuery(document).ready(function ($) {
    var id = 'wc-textarea-0_0';
    settings = {
        id: id,
        buttons: 'strong,em,link,img,ul,ol,li'
    }
    QTags.addButton('wpdiscuz_blockquot', 'b-quote', '<blockquote>', '</blockquote>', '', 'Blockquote', 40);
    QTags.addButton('wpdiscuz_underline', 'U', '<u>', '</u>', '', 'Underline', 50);
    QTags.addButton('wpdiscuz_code', 'code', '`', '`', '', 'Code', 110);
    quicktags(settings);

    $(document).delegate('.wc-reply-button', 'click', function () {
        var uniqueId = 'wc-textarea-' + wpdiscuzGetUniqueId($(this));
        if (uniqueId) {
            var settings = {
                id: uniqueId,
                buttons: 'strong,em,link,img,ul,ol,li'
            }
            quicktags(settings);
            QTags._buttonsInit();
        }
    });
    function wpdiscuzGetUniqueId(field) {
        var uniqueId = 0;
        if (field.parents('.wc-comment').attr('id')) {
            uniqueId = field.parents('.wc-comment').attr('id');
        }
        if (uniqueId !== 0 && uniqueId.length) {
            uniqueId = uniqueId.substring(uniqueId.lastIndexOf('-') + 1);
        }
        return uniqueId;
    }
});

