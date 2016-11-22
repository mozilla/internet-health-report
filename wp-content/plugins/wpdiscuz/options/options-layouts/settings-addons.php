<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Addons', 'wpdiscuz'); ?></h2>   
    <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row">

                    <p style="padding:10px; font-style:italic;"><?php _e('Here you can find wpDiscuz Addons\' setting options in vertical subTabs with according addon titles. All wpDiscuz addons are listed on wpDiscuz', 'wpdiscuz'); ?> &gt; <a href="<?php echo admin_url('edit-comments.php?page=' . WpdiscuzCore::PAGE_ADDONS) ?>"><?php _e('Addons subMenu', 'wpdiscuz'); ?></a>. <?php _e('We\'ll add new free and paid addons with almost every wpDiscuz release. There will be dozens of very useful addons in near future. Currently wpDiscuz consists of about 70 free features/addons like "Live Update", "First comment redirection", "Comment sorting", "Simple CAPTCHA", "AJAX Pagination", "Lazy Load", "Comment Likes", "Comment Share" and dozens of other addons and there will be more. All new and free addons will be built-in with wpDiscuz plugin and all paid addons will be listed separately on', 'wpdiscuz'); ?> <a href="<?php echo admin_url('edit-comments.php?page=' . WpdiscuzCore::PAGE_ADDONS) ?>"><?php _e('Addons subMenu','wpdiscuz');?></a>.</p>
                    <hr style="margin-bottom:25px; border-color:#fff;" />

                    <div id="wpdiscuz-addons-options">
                        <ul class="resp-tabs-list wpdiscuz-addons-options">
                            <?php do_action('wpdiscuz_addon_tab_title'); ?>
                        </ul>
                        <div class="resp-tabs-container wpdiscuz-addons-options">
                            <?php do_action('wpdiscuz_addon_tab_content'); ?>
                        </div>
                    </div>
                </th>
            </tr>
        </tbody>
    </table>
</div>