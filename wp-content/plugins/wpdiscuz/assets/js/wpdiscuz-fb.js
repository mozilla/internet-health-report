(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {
        return;
    }
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function () {
    FB.init({
        appId: wpdiscuzAjaxObj.wpdiscuz_options.facebookAppID,
        xfbml: true,
        version: 'v2.7'
    });
};

//============================== FACEBOOK SHARE ============================== //
jQuery(document).ready(function ($) {
    $(document).delegate('.wc-comment-link .fa-facebook', 'click', function () {
        var commentID = $(this).parents('.wc-comment').find('.wc-comment-right').attr('id');
        var postUrl = window.location.href;
        if (postUrl.indexOf('#') !== -1) {
            postUrl = postUrl.substring(0, postUrl.indexOf('#'));
        }
        postUrl += '#' + commentID;
        var commentContent = $(this).parents('.wc-comment-right').find('.wc-comment-text').text();
        wpcShareCommentFB(postUrl, commentContent);
    });
});


function wpcShareCommentFB(url, quote) {
    FB.ui({
        method: 'share',
        href: url,
        quote: quote,
    }, function (response) {});
}