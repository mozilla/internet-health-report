<?php
if (!defined('ABSPATH')) {
    exit();
}
$wpcCurrentThemDir = get_template_directory();
$wpdiscuzWalkerThemePath = $wpcCurrentThemDir . DIRECTORY_SEPARATOR. 'wpdiscuz'. DIRECTORY_SEPARATOR .'class.WpdiscuzWalker.php';
if (file_exists($wpdiscuzWalkerThemePath)) {
    include_once $wpdiscuzWalkerThemePath;
} else {
    include_once apply_filters('wpdiscuz_walker_include', WPDISCUZ_DIR_PATH . DIRECTORY_SEPARATOR . 'templates'. DIRECTORY_SEPARATOR .'comment'. DIRECTORY_SEPARATOR .'class.WpdiscuzWalker.php');
}
