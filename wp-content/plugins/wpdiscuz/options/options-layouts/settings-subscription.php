<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Email Subscription Settings', 'wpdiscuz'); ?> </h2>
    <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row" style="width:55%;"><label for="wc_disable_member_confirm" style="line-height:22px;"><span style="line-height:22px;"><?php _e('Disable subscription confirmation for registered users', 'wpdiscuz'); ?></span></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->disableMemberConfirm == 1) ?> value="1" name="wc_disable_member_confirm" id="wc_disable_member_confirm" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:55%;"><label for="disableGuestsConfirm" style="line-height:22px;"><span style="line-height:22px;"><?php _e('Disable subscription confirmation for guests', 'wpdiscuz'); ?></span></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->disableGuestsConfirm == 1) ?> value="1" name="disableGuestsConfirm" id="disableGuestsConfirm" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:55%;"><?php _e('Show subscription types in dropdown', 'wpdiscuz'); ?></th>
                <td>
                    <fieldset>
                        <?php $subscriptionType = isset($this->optionsSerialized->subscriptionType) ? $this->optionsSerialized->subscriptionType : 1; ?>
                        <label title="<?php _e('Both', 'wpdiscuz') ?>">
                            <input type="radio" value="1" <?php checked(1 == $subscriptionType); ?> name="subscriptionType" id="subscriptionTypeBoth" /> 
                            <span><?php _e('Both post and all comments subscription', 'wpdiscuz') ?></span>
                        </label> &nbsp;<br/>
                        <label title="<?php _e('Show new comment/reply buttons to update manualy', 'wpdiscuz') ?>">
                            <input type="radio" value="2" <?php checked(2 == $subscriptionType); ?> name="subscriptionType" id="subscriptionTypePost" /> 
                            <span><?php _e('Post subscription', 'wpdiscuz') ?></span>
                        </label><br>    
                        <label title="<?php _e('Always update', 'wpdiscuz') ?>">
                            <input type="radio" value="3" <?php checked(3 == $subscriptionType); ?> name="subscriptionType" id="subscriptionTypeAllComments" /> 
                            <span><?php _e('All comments subscription', 'wpdiscuz') ?></span>
                        </label> &nbsp;<br/>          
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:55%;">
                    <label for="wc_show_hide_reply_checkbox" style="line-height:22px;"><span style="line-height:22px;"><?php _e('Show "Notify of new replies to this comment"', 'wpdiscuz'); ?></span></label><br />
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;">
                        <?php _e('wpDiscuz is the only comment plugin which allows you to subscribe to certain comment replies. This option is located above [Post Comment] button in comment form. You can disable this subscription way by unchecking this option.', 'wpdiscuz') ?>
                    </p>                    
                </th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->showHideReplyCheckbox == 1) ?> value="1" name="wc_show_hide_reply_checkbox" id="wc_show_hide_reply_checkbox" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" style="width:55%;">
                    <label for="isReplyDefaultChecked" style="line-height:22px;"><span style="line-height:22px;"><?php _e('"Notify of new replies to this comment" checked by default', 'wpdiscuz'); ?></span></label><br />
                </th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->isReplyDefaultChecked == 1) ?> value="1" name="isReplyDefaultChecked" id="isReplyDefaultChecked" /></td>
            </tr>
            <?php if (class_exists('Prompt_Comment_Form_Handling')) { ?>
                <tr valign="top">
                    <th scope="row" style="width:55%;">
                        <?php _e('Use Postmatic for subscriptions and commenting by email', 'wpdiscuz'); ?>
                        <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;"><?php _e('Postmatic allows your users subscribe to comments. Instead of just being notified, they add a reply right from their inbox.', 'wpdiscuz'); ?></p>
                    </th>
                    <td>                                
                        <label for="wc_use_postmatic_for_comment_notification">
                            <input type="checkbox" <?php checked($this->optionsSerialized->usePostmaticForCommentNotification == 1) ?> value="1" name="wc_use_postmatic_for_comment_notification" id="wc_use_postmatic_for_comment_notification" />
                        </label>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>