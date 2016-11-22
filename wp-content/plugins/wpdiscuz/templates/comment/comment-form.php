<?php
if (!defined('ABSPATH')) {
    exit();
}
global $wpdiscuz, $post;

if (!function_exists('wpdiscuz_close_divs')) {

    function wpdiscuz_close_divs($html) {
        global $wpdiscuz;
        @preg_match_all('|<div|is', $html, $wc_div_open, PREG_SET_ORDER);
        @preg_match_all('|</div|is', $html, $wc_div_close, PREG_SET_ORDER);
        $wc_div_open = count((array) $wc_div_open);
        $wc_div_close = count((array) $wc_div_close);
        $wc_div_delta = $wc_div_open - $wc_div_close;
        if ($wc_div_delta) {
            $wc_div_end_html = str_repeat('</div>', $wc_div_delta);
            $html = $html . $wc_div_end_html;
        }
        //Custom case for social login plugin
        if (strpos($html, 'champ_login') !== FALSE) {
            if (preg_match_all('|<li[^><]*>.+?</li>|is', $html, $wc_social_buttons, PREG_SET_ORDER)) {
                foreach ($wc_social_buttons as $wc_social_button) {
                    $wc_social_buttons_array[] = $wc_social_button[0];
                }
                $html = '<style type="text/css">#wpcomm .wc_social_plugin_wrapper .wp-social-login-connect-with_by_the_champ{float:left;font-size:13px;padding:5px 7px 0 0;text-transform:uppercase}#wpcomm .wc_social_plugin_wrapper ul.wc_social_login_by_the_champ{list-style:none outside none!important;margin:0!important;padding-left:0!important}#wpcomm .wc_social_plugin_wrapper ul.wc_social_login_by_the_champ .theChampLogin{width:24px!important;height:24px!important}#wpcomm .wc-secondary-forms-social-content ul.wc_social_login_by_the_champ{list-style:none outside none!important;margin:0!important;padding-left:0!important}#wpcomm .wc-secondary-forms-social-content ul.wc_social_login_by_the_champ .theChampLogin{width:24px!important;height:24px!important}#wpcomm .wc-secondary-forms-social-content ul.wc_social_login_by_the_champ li{float:right!important}#wpcomm .wc_social_plugin_wrapper .theChampFacebookButton{ display:block!important; }#wpcomm .theChampTwitterButton{background-position:-4px -68px!important}#wpcomm .theChampGoogleButton{background-position:-36px -2px!important}#wpcomm .theChampVkontakteButton{background-position:-35px -67px!important}#wpcomm .theChampLinkedinButton{background-position:-34px -34px!important;}.theChampCommentingTabs #wpcomm li{ margin:0px 1px 10px 0px!important; }</style>
				<div class="wp-social-login-widget"><div class="wp-social-login-connect-with_by_the_champ">' . $wpdiscuz->optionsSerialized->phrases['wc_connect_with'] . ':</div><div class="wp-social-login-provider-list"><ul class="wc_social_login_by_the_champ">' . implode('', $wc_social_buttons_array) . '</ul><div class="wpdiscuz_clear"></div></div></div>';
            }
        }
        return $html;
    }

}
$current_user = wp_get_current_user();
do_action('wpdiscuz_before_load', $post, $current_user, null);
if (!post_password_required($post->ID)) {
    if (!$wpdiscuz->optionsSerialized->votingButtonsShowHide) {
        $wpdiscuz->dbManager->checkVoteData($post->ID);
    }
    $commentsCount = $wpdiscuz->dbManager->getCommentsCount($post->ID);
    $header_text = '<span class="wc_header_text_count">' . $commentsCount . '</span> ';
    $header_text .= ($commentsCount > 1) ? $wpdiscuz->optionsSerialized->phrases['wc_header_text_plural'] : $wpdiscuz->optionsSerialized->phrases['wc_header_text'];
    $header_text .= ' ' . $wpdiscuz->optionsSerialized->phrases['wc_header_on_text'];
    $header_text .= ' "' . get_the_title($post) . '"';

    $wpCommClasses = $current_user && $current_user->ID ? 'wpdiscuz_auth' : 'wpdiscuz_unauth';
    $wpCommClasses .= $wpdiscuz->optionsSerialized->wordpressShowAvatars ? '' : ' wpdiscuz_no_avatar';

    $ob_stat = ini_get('output_buffering');
    if ($ob_stat || $ob_stat === '' || $ob_stat == '0') {
        $wc_ob_allowed = true;
        ob_start();
        do_action('comment_form_top');
        do_action('wpdiscuz_comment_form_top', $post, $current_user, $commentsCount);
        $wc_comment_form_top_content = ob_get_contents();
        ob_clean();
        $wc_comment_form_top_content = wpdiscuz_close_divs($wc_comment_form_top_content);
    } else {
        $wc_ob_allowed = false;
    }

    if (isset($_GET['wpdiscuzSubscribeID']) && isset($_GET['key'])) {
        $wpdiscuz->dbManager->unsubscribe($_GET['wpdiscuzSubscribeID'], $_GET['key']);
        ?>
        <div id="wc_unsubscribe_message">
            <span class="wc_unsubscribe_message"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_unsubscribe_message']; ?></span>
        </div>
        <?php
    }

    if (isset($_GET['wpdiscuzConfirmID']) && isset($_GET['wpdiscuzConfirmKey']) && isset($_GET['wpDiscuzComfirm'])) {
        $wpdiscuz->dbManager->notificationConfirm($_GET['wpdiscuzConfirmID'], $_GET['wpdiscuzConfirmKey']);
        ?>
        <div id="wc_unsubscribe_message">
            <span class="wc_unsubscribe_message"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_comfirm_success_message']; ?></span>
        </div>
        <?php
    }

    if (isset($_GET['subscriptionSuccess'])) {
        $errorClass = 'wpdiscuz-sendmail-error';
        if ($_GET['subscriptionSuccess'] == -1) {
            $subscriptionMsg = __('Unable to send an email', 'wpdiscuz');
        } elseif (!$_GET['subscriptionSuccess']) {
            $subscriptionMsg = __('Subscription not successed', 'wpdiscuz');
        } else {
            if (isset($_GET['subscriptionID']) && ($subscriptionID = trim($_GET['subscriptionID']))) {
                $noNeedMemberConfirm = ($current_user->ID && $wpdiscuz->optionsSerialized->disableMemberConfirm);
                $noNeedGuestsConfirm = (!$current_user->ID && $wpdiscuz->optionsSerialized->disableGuestsConfirm && $wpdiscuz->dbManager->hasConfirmedSubscriptionByID($subscriptionID));
                if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                    $subscriptionMsg = $wpdiscuz->optionsSerialized->phrases['wc_subscribe_message'];
                } else {
                    $subscriptionMsg = $wpdiscuz->optionsSerialized->phrases['wc_confirm_email'];
                }
            } else {
                $errorClass = '';
            }
        }
        ?>
        <div id="wc_unsubscribe_message" class="<?php echo $errorClass; ?>">
            <span class="wc_unsubscribe_message"><?php echo $subscriptionMsg; ?></span>
        </div>
        <?php
    }
    ?>

    <div class="wpdiscuz_top_clearing"></div>
    <?php
    if ($post->comment_status == 'open') {
        $wpdiscuz->helper->superSocializerFix();
        $form = $wpdiscuz->wpdiscuzForm->getForm($post->ID);
        $formCustomCss = $form->getCustomCSS();
        if ($formCustomCss) {
            echo '<style type="text/css">'. $formCustomCss . '</style>';
        }
        ?>
        <h3 id="wc-comment-header"><?php echo $form->getHeaderText(); ?></h3>
        <?php
        if ($wpdiscuz->optionsSerialized->showHideLoggedInUsername) {
            if ($current_user && $current_user->ID) {
                $user_url = get_author_posts_url($current_user->ID);
                ?>
                <div id="wc_show_hide_loggedin_username">
                    <span class="wc_show_hide_loggedin_username">
                        <?php
                        $logout = wp_loginout(get_permalink(), false);
                        $logout = preg_replace('!>([^<]+)!is', '>' . $wpdiscuz->optionsSerialized->phrases['wc_log_out'], $logout);
                        echo $wpdiscuz->optionsSerialized->phrases['wc_logged_in_as'] . ' <a href="' . $user_url . '">' . $wpdiscuz->helper->getCurrentUserDisplayName($current_user) . '</a> | ' . $logout;
                        ?>
                    </span>
                </div>
                <?php
            }
        }
        ?>
        <div id="wpcomm" class="<?php echo $wpCommClasses; ?>">
            <?php if (!$wpdiscuz->optionsSerialized->headerTextShowHide) { ?>
                <div class="wc-comment-bar">
                    <p class="wc-comment-title">
                        <?php echo ($commentsCount) ? $header_text : $wpdiscuz->optionsSerialized->phrases['wc_be_the_first_text']; ?>
                    </p>
                    <div class="wpdiscuz_clear"></div>
                </div>
            <?php } ?>
            <?php do_action('comment_form_before'); ?>
            <div class="wc_social_plugin_wrapper">
                <?php
                if ($wc_ob_allowed) {
                    echo $wc_comment_form_top_content;
                } else {
                    do_action('comment_form_top');
                    do_action('wpdiscuz_comment_form_top', $post, $current_user, $commentsCount);
                }
                ?>
            </div>
            <?php
            $isPostmaticActive = !class_exists('Prompt_Comment_Form_Handling') || (class_exists('Prompt_Comment_Form_Handling') && !$wpdiscuz->optionsSerialized->usePostmaticForCommentNotification);
            if ($form->isShowSubscriptionBar() && $isPostmaticActive) {
                $subscriptionData = $wpdiscuz->dbManager->hasSubscription($post->ID, $current_user->user_email);
                $subscriptionType = null;
                if ($subscriptionData) {
                    $isConfirmed = $subscriptionData['confirm'];
                    $subscriptionType = $subscriptionData['type'];
                    if ($subscriptionType == WpdiscuzCore::SUBSCRIPTION_POST || $subscriptionType == WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT) {
                        $unsubscribeLink = $wpdiscuz->dbManager->unsubscribeLink($post->ID, $current_user->user_email);
                    }
                }
                ?>
                <div class="wpdiscuz-subscribe-bar">
                    <?php
                    if ($subscriptionType != WpdiscuzCore::SUBSCRIPTION_POST) {
                        ?>
                        <form action="<?php echo admin_url('admin-ajax.php') . '?action=addSubscription'; ?>" method="post" id="wpdiscuz-subscribe-form">
                            <div class="wpdiscuz-subscribe-form-intro"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_of']; ?> </div>
                            <div class="wpdiscuz-subscribe-form-option" style="width:<?php echo (!$current_user->ID) ? '40%' : '65%'; ?>;">
                                <select class="wpdiscuz_select" name="wpdiscuzSubscriptionType" >
                                    <?php if ($wpdiscuz->optionsSerialized->subscriptionType != 3) { ?>
                                        <option value="<?php echo WpdiscuzCore::SUBSCRIPTION_POST; ?>"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_on_new_comment']; ?></option>
                                    <?php } ?>
                                    <?php if ($wpdiscuz->optionsSerialized->subscriptionType != 2) { ?>
                                        <option value="<?php echo WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT; ?>" <?php echo (isset($unsubscribeLink) || !$wpdiscuz->optionsSerialized->wordpressThreadComments) ? 'disabled' : ''; ?>><?php echo $wpdiscuz->optionsSerialized->phrases['wc_notify_on_all_new_reply']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php if (!$current_user->ID) { ?>
                                <div class="wpdiscuz-item wpdiscuz-subscribe-form-email">
                                    <input id="" class="email" type="email" name="wpdiscuzSubscriptionEmail" required="required" value="" placeholder="<?php echo $wpdiscuz->optionsSerialized->phrases['wc_email_text']; ?>"/>
                                </div>
                            <?php } ?>
                            <div class="wpdiscuz-subscribe-form-button">
                                <input id="wpdiscuz_subscription_button" type="submit" value="&rsaquo;" name="wpdiscuz_subscription_button" />
                            </div> 
                            <?php wp_nonce_field('wpdiscuz_subscribe_form_nonce_action', 'wpdiscuz_subscribe_form_nonce'); ?>
                            <input type="hidden" value="<?php echo $post->ID; ?>" name="wpdiscuzSubscriptionPostId" />
                        </form>
                    <?php } ?>
                    <div class="wpdiscuz_clear"></div>
                    <?php
                    if (isset($unsubscribeLink)) {
                        $subscribeMessage = $isConfirmed ? $wpdiscuz->optionsSerialized->phrases['wc_unsubscribe'] : $wpdiscuz->optionsSerialized->phrases['wc_ignore_subscription'];
                        if ($subscriptionType == 'all_comment')
                            $introText = $wpdiscuz->optionsSerialized->phrases['wc_subscribed_to'] . ' ' . $wpdiscuz->optionsSerialized->phrases['wc_notify_on_all_new_reply'];
                        elseif ($subscriptionType == 'post')
                            $introText = $wpdiscuz->optionsSerialized->phrases['wc_subscribed_to'] . ' ' . $wpdiscuz->optionsSerialized->phrases['wc_notify_on_new_comment'];
                        echo '<div class="wpdiscuz_subscribe_status">' . $introText . " | <a href='$unsubscribeLink'>" . $subscribeMessage . "</a></div>";
                    }
                    ?>
                </div>
                <?php
            }
            $wpdiscuz->wpdiscuzForm->renderFrontForm($commentsCount, $current_user);
            do_action('comment_form_after');
            do_action('wpdiscuz_comment_form_after', $post, $current_user, $commentsCount);
            ?>
            

        <?php } else { ?>
            <?php
            if ($commentsCount > 0) {
                $wpdiscuz->helper->superSocializerFix();
            } else {
                ?>
                <div id="comments" class="comments-area" style="display:none">
                    <div id="respond"></div>
                <?php } ?>
                <?php
                do_action('comment_form_closed');
                do_action('wpdiscuz_comment_form_closed', $post, $current_user, $commentsCount);
                ?>
                <div id="wpcomm" class="<?php echo $wpCommClasses; ?>" style="border:none;">
                <?php } ?>
                <?php do_action('wpdiscuz_before_comments', $post, $current_user, $commentsCount); ?>

                <?php if ($commentsCount && $wpdiscuz->optionsSerialized->showSortingButtons && !$wpdiscuz->optionsSerialized->wordpressIsPaginate) { ?>
                    <div class="wpdiscuz-front-actions">
                        <div class="wpdiscuz-sort-buttons" style="font-size:14px;"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_sort_by']; ?>: &nbsp;
                            <span class="wpdiscuz-sort-button wpdiscuz-date-sort-desc"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_newest']; ?></span> | 
                            <span class="wpdiscuz-sort-button wpdiscuz-date-sort-asc"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_oldest']; ?></span>
                            <?php if (!$wpdiscuz->optionsSerialized->votingButtonsShowHide) { ?>
                                | <span class="wpdiscuz-sort-button wpdiscuz-vote-sort-up"><?php echo $wpdiscuz->optionsSerialized->phrases['wc_most_voted']; ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($wpdiscuz->optionsSerialized->commentListUpdateType == 2) { ?>
                    <div class="wc_new_comment_and_replies">
                        <div class="wc_new_comment wc-update-on-click"></div>
                        <div class="wc_new_reply wc-update-on-click"></div>
                        <div class="wpdiscuz_clear"></div>
                    </div>
                    <div class="wpdiscuz_clear"></div>
                <?php } ?>
                <div id="wcThreadWrapper" class="wc-thread-wrapper">
                    <?php
                    $args = array();
                    $showLoadeMore = 1;
                    if (isset($_GET['_escaped_fragment_'])) {
                        parse_str($_GET['_escaped_fragment_'], $query_array);
                        $lastParentId = isset($query_array['parentId']) ? intval($query_array['parentId']) : 0;
                        if ($lastParentId) {
                            $args['last_parent_id'] = $lastParentId--;
                        }
                    }

                    if ($wpdiscuz->optionsSerialized->showSortingButtons && $wpdiscuz->optionsSerialized->mostVotedByDefault && !$wpdiscuz->optionsSerialized->votingButtonsShowHide) {
                        $args['orderby'] = 'by_vote';
                    }
                    $commentData = $wpdiscuz->getWPComments($args);
                    echo $commentData['comment_list'];
                    ?>                
                    <div class="wpdiscuz-comment-pagination">
                        <?php
                        if (!$wpdiscuz->optionsSerialized->wordpressIsPaginate && $commentData['is_show_load_more']) {
                            $loadMoreButtonText = ($wpdiscuz->optionsSerialized->commentListLoadType == 1) ? $wpdiscuz->optionsSerialized->phrases['wc_load_rest_comments_submit_text'] : $wpdiscuz->optionsSerialized->phrases['wc_load_more_submit_text'];
                            ?>
                            <div class="wc-load-more-submit-wrap">
                                <a class="wc-load-more-link" href="<?php echo get_permalink($post->ID) . '#!parentId=' . $commentData['last_parent_id']; ?>">
                                    <button name="submit"  class="wc-load-more-submit wc-loaded button">
                                        <?php echo $loadMoreButtonText; ?>
                                    </button>
                                </a>
                            </div>
                            <input id="wpdiscuzHasMoreComments" type="hidden" value="<?php echo $commentData['is_show_load_more']; ?>" />
                            <?php
                        } else {
                            paginate_comments_links();
                        }
                        ?>
                    </div>
                </div>
                <div class="wpdiscuz_clear"></div>
                <?php do_action('wpdiscuz_after_comments', $post, $current_user, $commentsCount); ?>
                <?php if ($commentsCount) { ?>
                    <?php if ($wpdiscuz->optionsSerialized->showPluginPoweredByLink) { ?>
                        <div class="by-wpdiscuz">
                            <span id="awpdiscuz" onclick='javascript:document.getElementById("bywpdiscuz").style.display = "inline";
                                                    document.getElementById("awpdiscuz").style.display = "none";'>
                                <img alt="wpdiscuz" src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/plugin-icon/icon_info.png'); ?>"  align="absmiddle" class="wpdimg"/>
                            </span>&nbsp;
                            <a href="http://wpdiscuz.com/" target="_blank" id="bywpdiscuz" title="wpDiscuz v<?php echo get_option(WpdiscuzCore::OPTION_SLUG_VERSION); ?> - Supercharged native comments">wpDiscuz</a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
        <div class="wpdiscuz-loading-bar <?php echo ($current_user->ID) ? 'wpdiscuz-loading-bar-auth' : 'wpdiscuz-loading-bar-unauth'; ?>"><img class="wpdiscuz-loading-bar-img" alt="<?php _e('wpDiscuz', 'wpdiscuz'); ?>" src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/loading.gif'); ?>" width="32" height="25" /></div>
        <?php
    }
    ?>