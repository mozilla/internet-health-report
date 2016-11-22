<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Show/Hide Components', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins" style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row" style="width:55%"><label for="wc_show_hide_loggedin_username"><?php _e('Show logged-in user name and logout link on top of main form', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->showHideLoggedInUsername == 1) ?> value="1" name="wc_show_hide_loggedin_username" id="wc_show_hide_loggedin_username" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_reply_button_guests_show_hide"><?php _e('Hide Reply button for Guests', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->replyButtonGuestsShowHide == 1) ?> value="1" name="wc_reply_button_guests_show_hide" id="wc_reply_button_guests_show_hide" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_reply_button_members_show_hide"><?php _e('Hide Reply button for Members', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->replyButtonMembersShowHide == 1) ?> value="1" name="wc_reply_button_members_show_hide" id="wc_reply_button_members_show_hide" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_author_titles_show_hide"><?php _e('Hide Commenter Labels', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->authorTitlesShowHide == 1) ?> value="1" name="wc_author_titles_show_hide" id="wc_author_titles_show_hide" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_voting_buttons_show_hide"><?php _e('Hide Voting buttons', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->votingButtonsShowHide == 1) ?> value="1" name="wc_voting_buttons_show_hide" id="wc_voting_buttons_show_hide" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Comment voting statistic mode', 'wpdiscuz'); ?></th>
                <td>
                    <fieldset class="votingButtonsStyleSet">
                        <label for="votingButtonsStyleTotal">
                            <input type="radio" <?php checked($this->optionsSerialized->votingButtonsStyle == 0) ?> value="0" name="votingButtonsStyle" id="votingButtonsStyleTotal" class="votingButtonsStyle"/>
                            <span><?php _e('total count', 'wpdiscuz'); ?></span>
                        </label><br/>
                        <label for="votingButtonsStyleSeparate">
                            <input type="radio" <?php checked($this->optionsSerialized->votingButtonsStyle == 1) ?> value="1" name="votingButtonsStyle" id="votingButtonsStyleSeparate" class="votingButtonsStyle"/>
                            <span><?php _e('separate count', 'wpdiscuz'); ?></span>
                        </label><br/>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Show Share Buttons', 'wpdiscuz'); ?></th>
                <td>
                    <?php
                    $shareButtons = $this->shareButtons;
                    foreach ($shareButtons as $btn) {
                        $checked = in_array($btn, $this->optionsSerialized->shareButtons) ? 'checked="checked"' : '';
                        ?>
                        <label class="wpdiscuz-share-buttons share-button-<?php echo $btn; ?>" for="wc_share_button_<?php echo $btn; ?>">
                            <input type="checkbox" <?php echo $checked ?> value="<?php echo $btn; ?>" name="wpdiscuz_share_buttons[]" id="wc_share_button_<?php echo $btn; ?>" class="wc_share_button" />
                        </label>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php  $facbookAppContShow = in_array('fb', $this->optionsSerialized->shareButtons) ? '' : 'style="display:none;"'; 
            ?>
            <tr valign="top" id="wpc-fb-api-cont" <?php echo $facbookAppContShow;?>>
                <th scope="row">
                    <?php _e('Facebook Application ID', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="facebookAppID">
                        <input type="text" value="<?php echo $this->optionsSerialized->facebookAppID; ?>" name="facebookAppID" id="facebookAppID" />
                    </label>
                </td>
            </tr>
            <?php
            $pathToDir = WPDISCUZ_DIR_PATH . WPDISCUZ_DS . 'utils' . WPDISCUZ_DS . 'temp';
            $isWritable = @is_writable($pathToDir);
            if ($isWritable) {
                $disableCaptcha = '';
                $msg = '';
            } else {
                $disableCaptcha = 'disabled="disabled"';
                $msg = '<p style="display: inline;">' . __('The plugin captcha directory is not writable! Please set writable permissions on "wpdiscuz/utils/temp" directory in order to use the first type of captcha generation', 'wpdiscuz') . '</p>';
            }
            ?>
            <tr valign="top">
                <th scope="row"><?php _e('Captcha generation type', 'wpdiscuz'); ?></th>
                <td>
                    <fieldset class="commentListLoadType">
                        <?php $isCaptchaInSession = isset($this->optionsSerialized->isCaptchaInSession) ? $this->optionsSerialized->isCaptchaInSession : 0; ?>
                        <label>
                            <input <?php echo $disableCaptcha; ?> type="radio" value="0" <?php checked('0' == $isCaptchaInSession); ?> name="isCaptchaInSession" id="captchaByImageFile" />
                            <span><?php _e('use file system', 'wpdiscuz') ?></span>
                        </label> &nbsp;<br/>
                        <div><?php echo $msg; ?></div>
                        <label>
                            <input type="radio" value="1" <?php checked('1' == $isCaptchaInSession); ?> name="isCaptchaInSession" id="captchaInSession" /> 
                            <span><?php _e('use wordpress session', 'wpdiscuz') ?></span>
                        </label><br>        
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_header_text_show_hide"><?php _e('Hide header text', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->headerTextShowHide == 1) ?> value="1" name="wc_header_text_show_hide" id="wc_header_text_show_hide" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="show_sorting_buttons"><?php _e('Show sorting buttons', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->showSortingButtons == 1) ?> value="1" name="show_sorting_buttons" id="show_sorting_buttons" /></td>
            </tr>
            <tr valign="top" id="row_mostVotedByDefault">
                <th scope="row"><label for="mostVotedByDefault"><?php _e('Set comments ordering to "Most voted" by default ', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->mostVotedByDefault == 1) ?> value="1" name="mostVotedByDefault" id="mostVotedByDefault" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="showHideCommentLink"><?php _e('Hide comment link', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->showHideCommentLink == 1) ?> value="1" name="showHideCommentLink" id="showHideCommentLink" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="enableImageConversion"><?php _e('Enable automatic image URL to image HTML conversion', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->enableImageConversion == 1) ?> value="1" name="enableImageConversion" id="enableImageConversion" /></td>
            </tr>
        </tbody>
    </table>
</div>