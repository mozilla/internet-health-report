<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div class="wrap wpdiscuz_options_page">
    <div style="float:left; width:50px; height:55px; margin:10px 10px 20px 0px;">
        <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/plugin-icon/plugin-icon-48.png'); ?>" style="border:2px solid #fff;"/>
    </div>
    <h1 style="padding-bottom:20px; padding-top:15px;"><?php _e('wpDiscuz General Settings', 'wpdiscuz'); ?></h1>
    <br style="clear:both" />
    <?php do_action('wpdiscuz_option_page'); ?>
    <table width="100%" border="0" cellspacing="1" class="widefat" style="background-color:#FdFdFd;">
        <tr>
            <td valign="top" style="padding:3px;">
                <table width="100%" border="0" cellspacing="2">
                    <tr>
                        <th style="font-size:16px;"><a href="https://wordpress.org/support/view/plugin-reviews/wpdiscuz?filter=5" target="_blank" title="We'd greatly appreciate your feedback on WordPress.org"><?php _e('Like wpDiscuz?','wpdiscuz');?></a></th>
                        <th style="font-size:16px; width:135px; text-align:center;"><a href="http://wpdiscuz.com/wpdiscuz-documentation/" style="color:#008EC2; overflow:hidden; outline:none;" target="_blank"><?php _e('Documentation','wpdiscuz');?></a></th>
                        <th style="font-size:16px; width:75px; text-align:center;"><a href="http://gvectors.com/forum/" style="color:#008EC2; overflow:hidden; outline:none;" target="_blank"><?php _e('Support','wpdiscuz');?></a></th>
                        <th style="font-size:16px; width:75px; text-align:center;"><a href="http://wpdiscuz.com/addons/" style="color:#008EC2; overflow:hidden; outline:none;" target="_blank"><?php _e('Addons','wpdiscuz');?></a></th>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?php
    if (isset($_GET['_wpnonce']) && isset($_GET['wpdiscuz_reset_options']) && wp_verify_nonce($_GET['_wpnonce'], 'wpdiscuz_reset_options_nonce') && $_GET['wpdiscuz_reset_options'] == 1 && current_user_can('manage_options')) {
        delete_option(WpdiscuzCore::OPTION_SLUG_OPTIONS);
        $this->optionsSerialized->shareButtons = array('fb', 'twitter', 'google');
        $this->optionsSerialized->addOptions();
        $this->optionsSerialized->initOptions(get_option(WpdiscuzCore::OPTION_SLUG_OPTIONS));
        $this->optionsSerialized->blogRoles['post_author'] = '#00B38F';
        $blogRoles = get_editable_roles();
        foreach ($blogRoles as $roleName => $roleInfo) {
            $this->optionsSerialized->blogRoles[$roleName] = '#00B38F';
        }
        $this->optionsSerialized->blogRoles['guest'] = '#00B38F';
        $this->optionsSerialized->showPluginPoweredByLink = 1;
        $this->optionsSerialized->updateOptions();
        do_action('wpdiscuz_reset_options');
    }
    ?>

    <form action="<?php echo admin_url(); ?>edit-comments.php?page=<?php echo WpdiscuzCore::PAGE_SETTINGS; ?>" method="post" name="<?php echo WpdiscuzCore::PAGE_SETTINGS; ?>" class="wc-main-settings-form wc-form" enctype="multipart/form-data">
        <?php
        if (function_exists('wp_nonce_field')) {
            wp_nonce_field('wc_options_form');
        }
        ?>
        <h2>&nbsp;</h2>
        <div id="optionsTab">
            <ul class="resp-tabs-list options_tab_id">
                <li><?php _e('General Settings', 'wpdiscuz'); ?></li>
                <li><?php _e('Live Update', 'wpdiscuz'); ?></li>
                <li><?php _e('Show/Hide', 'wpdiscuz'); ?></li>
                <li><?php _e('Subscription', 'wpdiscuz'); ?> <?php if (class_exists('Prompt_Comment_Form_Handling')): ?> <?php _e('and Postmatic', 'wpdiscuz'); ?> <?php endif; ?></li>
                <li><?php _e('Styling', 'wpdiscuz'); ?></li>
                <li><?php _e('Social Login', 'wpdiscuz'); ?></li>
                <li><?php _e('Integrations', 'wpdiscuz'); ?></li>
                <li><?php _e('Addons', 'wpdiscuz'); ?></li>
            </ul>
            <div class="resp-tabs-container options_tab_id">
                <?php
                include 'options-layouts/settings-general.php';
                include 'options-layouts/settings-live-update.php';
                include 'options-layouts/settings-show-hide.php';
                include 'options-layouts/settings-subscription.php';
                include 'options-layouts/settings-style.php';
                include 'options-layouts/settings-social.php';
                include 'options-layouts/settings-integrations.php';
                include 'options-layouts/settings-addons.php';
                ?>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                var width = 0;
                var optionsTabsType = 'default';
                $('#optionsTab ul.resp-tabs-list.options_tab_id li').each(function () {
                    width += $(this).outerWidth(true);
                });

                if (width > $('#optionsTab').innerWidth()) {
                    optionsTabsType = 'vertical';
                }

                var url = '<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/social-icons/'); ?>';
                $('.wpdiscuz-share-buttons').each(function () {
                    setBG($(this));
                });
                $('.wpdiscuz-share-buttons').click(function () {
                    setBG($(this));
                });
                function setBG(field) {
                    if ($('.wc_share_button', field).is(':checked')) {
                        $(field).css('background', 'url("' + url + $('.wc_share_button', field).val() + '-18x18-orig.png")');
                    } else {
                        $(field).css('background', 'url("' + url + $('.wc_share_button', field).val() + '-18x18.png")');
                    }
                }
                //Horizontal Tab
                $('#optionsTab').easyResponsiveTabs({
                    type: optionsTabsType, //Types: default, vertical, accordion
                    width: 'auto', //auto or any width like 600px
                    fit: true, // 100% fit in a container
                    tabidentify: 'options_tab_id' // The tab groups identifier
                });


                // Child Tab
                $('#integrationsChild').easyResponsiveTabs({
                    type: 'vertical',
                    width: 'auto',
                    fit: true,
                    tabidentify: 'integrationsChild', // The tab groups identifier
                });

                // Child Tab
                $('#wpdiscuz-addons-options').easyResponsiveTabs({
                    type: 'vertical',
                    width: 'auto',
                    fit: true,
                    tabidentify: 'wpdiscuz-addons-options', // The tab groups identifier
                });

                $(document).delegate('.options_tab_id .resp-tab-item', 'click', function () {
                    var activeTabIndex = $('.resp-tabs-list.options_tab_id li.resp-tab-active').index();
                    $.cookie('optionsActiveTabIndex', activeTabIndex, {expires: 30});
                });
                var savedIndex = $.cookie('optionsActiveTabIndex') >= 0 ? $.cookie('optionsActiveTabIndex') : 0;
                $('.resp-tabs-list.options_tab_id li').removeClass('resp-tab-active');
                $('.resp-tabs-container.options_tab_id > div').removeClass('resp-tab-content-active');
                $('.resp-tabs-container.options_tab_id > div').css('display', 'none');
                $('.resp-tabs-list.options_tab_id li').eq(savedIndex).addClass('resp-tab-active');
                $('.resp-tabs-container.options_tab_id > div').eq(savedIndex).addClass('resp-tab-content-active');
                $('.resp-tabs-container.options_tab_id > div').eq(savedIndex).css('display', 'block');
            });
        </script>
        <table class="form-table wc-form-table">
            <tbody>
                <tr valign="top">
                    <td colspan="4">
                        <p class="submit">
                            <?php $resetOptionsUrl = admin_url() . 'edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS . '&wpdiscuz_reset_options=1';
                             $resetOptionsUrl = wp_nonce_url($resetOptionsUrl,'wpdiscuz_reset_options_nonce');
                             ?>
                            <a style="float: left;" class="button button-secondary" href="<?php echo $resetOptionsUrl;?>"><?php _e('Reset Options', 'wpdiscuz'); ?></a>
                            <?php $clearChildrenUrl = admin_url('admin-post.php/?action=clearChildrenData&clear=1'); ?>
                            <a href="<?php echo wp_nonce_url($clearChildrenUrl, 'clear_children_data'); ?>" class="button button-secondary" title="Use this button if wpDiscuz has been deactivated for a while." style="margin-left: 5px;" id="wpdiscuz_synch_comments"><?php _e('Refresh comment optimization', 'wpdiscuz'); ?></a>
                            <?php $voteUrl = admin_url('admin-post.php/?action=removeVoteData&remove=1'); ?>
                            <a href="<?php echo wp_nonce_url($voteUrl, 'remove_vote_data'); ?>" class="button button-secondary" style="margin-left: 5px;" id="wpdiscuz_clear_votes"><?php _e('Remove vote data', 'wpdiscuz'); ?></a>
                            <input style="float: right;" type="submit" class="button button-primary" name="wc_submit_options" value="<?php _e('Save Changes', 'wpdiscuz'); ?>" />                                
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="action" value="update" />
    </form>
</div>