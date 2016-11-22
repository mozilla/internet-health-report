jQuery(document).ready(function($){
    $('#menu-comments, #menu-comments > a').removeClass('wp-not-current-submenu');
    $('#menu-comments, #menu-comments > a').addClass('wp-has-current-submenu');
    $('#wpd-form-menu-item').parents('li').addClass('current');
});


