<?php
if (!defined('ABSPATH')) {
    exit();
}
/*
  1. WordPress Social Login
  2. Social Login
  3  Super Socializer
  4. Social Connect
 */

$pluginsDir = plugins_url();
$html = '';
// WordPress Social Login
$wpSLDirName = 'wordpress-social-login';
$wpSLFileName = "$wpSLDirName/wp-social-login.php";
$wpSLDirPath = WP_PLUGIN_DIR . '/' . $wpSLDirName . '/';
$wpSLOptionsPage = 'options-general.php?page=wordpress-social-login';

// Social Login
$slDirName = 'oa-social-login';
$slFileName = "$slDirName/oa-social-login.php";
$slDirPath = WP_PLUGIN_DIR . '/' . $slDirName . '/';
$slOptionsPage = 'admin.php?page=oa_social_login_setup';

// Super Socializer
$ssDirName = 'super-socializer';
$ssFileName = "$ssDirName/super_socializer.php";
$ssDirPath = WP_PLUGIN_DIR . '/' . $ssDirName . '/';
$ssOptionsPage = 'admin.php?page=heateor-ss-general-options';

// Social Connect
$scDirName = 'social-connect';
$scFileName = "$scDirName/social-connect.php";
$scDirPath = WP_PLUGIN_DIR . '/' . $scDirName . '/';
$scOptionsPage = 'options-general.php?page=social-connect-id';

add_thickbox();
if (function_exists('wsl_activate')) {
    $html = "<tr valign='top'><td>WordPress Social Login</td><td><a href='$wpSLOptionsPage' class='button button-primary'>" . __('Settings', 'default') . "</a></td></tr>";
} else if (function_exists('oa_social_login_activate')) {
    $html = "<tr valign='top'><td>Social Login</td><td><a href='$slOptionsPage' class='button button-primary'>" . __('Settings', 'default') . "</a></td></tr>";
} else if (function_exists('the_champ_init')) {
    $html = "<tr valign='top'><td>Super Socializer</td><td><a href='$ssOptionsPage' class='button button-primary'>" . __('Settings', 'default') . "</a></td></tr>";
} else if (function_exists('sc_activate')) {
    $html = "<tr valign='top'><td>Social Connect</td><td><a href='$scOptionsPage' class='button button-primary'>" . __('Settings', 'default') . "</a></td></tr>";
} else {
    // wordpress social login
    if (file_exists($wpSLDirPath)) {
        $wc_wordpress_social_login_text = __('Activate', 'wpdiscuz');
        $wc_wordpress_social_login_link = 'edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS . '&wc_social_action=' . $wpSLDirName;
        $wc_wordpress_social_login_thickbox = '';
    } else {
        $wc_wordpress_social_login_text = __('View details/Install', 'wpdiscuz');
        $wc_wordpress_social_login_link = 'plugin-install.php?tab=plugin-information&plugin=wordpress-social-login&TB_iframe=true&width=772&height=342';
        $wc_wordpress_social_login_thickbox = 'thickbox';
    }

    // social login
    if (file_exists($slDirPath)) {
        $wc_oa_social_login_text = __('Activate', 'wpdiscuz');
        $wc_oa_social_login_link = 'edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS . '&wc_social_action=' . $slDirName;
        $wc_oa_social_login_thickbox = '';
    } else {
        $wc_oa_social_login_text = __('View details/Install', 'wpdiscuz');
        $wc_oa_social_login_link = 'plugin-install.php?tab=plugin-information&plugin=oa-social-login&TB_iframe=true&width=772&height=342';
        $wc_oa_social_login_thickbox = 'thickbox';
    }

    // super socializer
    if (file_exists($ssDirPath)) {
        $wc_super_socializer_text = __('Activate', 'wpdiscuz');
        $wc_super_socializer_link = 'edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS . '&wc_social_action=' . $ssDirName;
        $wc_super_socializer_thickbox = '';
    } else {
        $wc_super_socializer_text = __('View details/Install', 'wpdiscuz');
        $wc_super_socializer_link = 'plugin-install.php?tab=plugin-information&plugin=super-socializer&TB_iframe=true&width=772&height=342';
        $wc_super_socializer_thickbox = 'thickbox';
    }

    // social connect
    if (file_exists($scDirPath)) {
        $wc_social_connect_text = __('Activate', 'wpdiscuz');
        $wc_social_connect_link = 'edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS . '&wc_social_action=' . $scDirName;
        $wc_social_connect_thickbox = '';
    } else {
        $wc_social_connect_text = __('View details/Install', 'wpdiscuz');
        $wc_social_connect_link = 'plugin-install.php?tab=plugin-information&plugin=social-connect&TB_iframe=true&width=772&height=342';
        $wc_social_connect_thickbox = 'thickbox';
    }

    $html = '<tr valign="top"><td>WordPress Social Login</td><td><a href="' . $wc_wordpress_social_login_link . '" class="button button-primary ' . $wc_wordpress_social_login_thickbox . '">' . $wc_wordpress_social_login_text . '</a></td></tr>';
    $html .= '<tr valign="top"><td>Social Login</td><td><a href="' . $wc_oa_social_login_link . '" class="button button-primary ' . $wc_oa_social_login_thickbox . '">' . $wc_oa_social_login_text . '</a></td></tr>';
    $html .= '<tr valign="top"><td>Super Socializer</td><td><a href="' . $wc_super_socializer_link . '" class="button button-primary ' . $wc_super_socializer_thickbox . '">' . $wc_super_socializer_text . '</a></td></tr>';
    $html .= '<tr valign="top"><td>Social Connect</td><td><a href="' . $wc_social_connect_link . '" class="button button-primary ' . $wc_social_connect_thickbox . '">' . $wc_social_connect_text . '</a></td></tr>';
}

if (isset($_GET['wc_social_action'])) {
    $plugin_name = $_GET['wc_social_action'];
    $wc_activation_redirect_url = '';
    $wc_social_plugin_file = '';
    switch ($plugin_name) {
        case $wpSLDirName:
            $wc_activation_redirect_url = $wpSLOptionsPage;
            $wc_social_plugin_file = $wpSLFileName;
            break;
        case $slDirName:
            $wc_activation_redirect_url = $slOptionsPage;
            $wc_social_plugin_file = $slFileName;
            break;
        case $ssDirName:
            $wc_activation_redirect_url = $ssOptionsPage;
            $wc_social_plugin_file = $ssFileName;
            break;
        case $scDirName:
            $wc_activation_redirect_url = $scOptionsPage;
            $wc_social_plugin_file = $scFileName;
            break;
    }
    activate_plugin($wc_social_plugin_file, $wc_activation_redirect_url);
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Social Login', 'wpdiscuz'); ?> </h2>
    <p style="padding-bottom:10px; padding-left:10px;"><?php _e('You can use one of these most popular Social Login Plugins to allow your visitors login and comment with Facebook, Twitter, Google+, Wordpress, VK, OK and lots of other social network service accounts. All social login buttons will be fully integrated with wpDiscuz comment forms.', 'wpdiscuz'); ?> </p>
    <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none;">
        <tbody>
            <?php echo $html; ?>
        </tbody>
    </table>
</div>
