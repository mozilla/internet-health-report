<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('General Settings', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row" style="width:55%;"><label for="wc_quick_tags"><?php _e('Enable Quicktags', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->isQuickTagsEnabled == 1) ?> value="1" name="wc_quick_tags" id="wc_quick_tags" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="isUserByEmail"><?php _e('Use guest email to detect registered account', 'wpdiscuz'); ?></label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;">
                        <?php _e('Sometimes registered users comment as guest using the same email address. wpDiscuz can detect the account role using guest email and display commenter label correctly.', 'wpdiscuz'); ?>
                    </p>
                </th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->isUserByEmail == 1) ?> value="1" name="isUserByEmail" id="isUserByEmail" /></td>
            </tr>            
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment author name min length (for guests only)', 'wpdiscuz'); ?>
                </th>
                <td>                                
                    <label for="commenterNameMinLength">
                        <?php _e('Min', 'wpdiscuz'); ?>: <input type="number" value="<?php echo $this->optionsSerialized->commenterNameMinLength; ?>" name="commenterNameMinLength" id="commenterNameMinLength" style="width:70px;" />
                    </label>
                    <label for="commenterNameMaxLength">
                        &nbsp; <?php _e('Max', 'wpdiscuz'); ?>: <input type="number" value="<?php echo $this->optionsSerialized->commenterNameMaxLength; ?>" name="commenterNameMaxLength" id="commenterNameMaxLength" style="width:70px;" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="storeCommenterData"><?php _e('Keep guest commenter credentials in browser cookies for x days', 'wpdiscuz'); ?></label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;">
                        <?php _e('Set this option value -1 to make it unlimited.', 'wpdiscuz'); ?><br /> 
                        <?php _e('Set this option value 0 to clear those data when user closes browser.', 'wpdiscuz'); ?>
                    </p>
                </th>
                <td><input type="number" value="<?php echo isset($this->optionsSerialized->storeCommenterData) ? $this->optionsSerialized->storeCommenterData : -1; ?>" name="storeCommenterData" id="storeCommenterData" style="width:100px;" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment text length', 'wpdiscuz'); ?>
                </th>
                <td>
                    <label for="wc_comment_text_min_length">
                        <?php _e('Min', 'wpdiscuz'); ?>: <input type="number" value="<?php echo isset($this->optionsSerialized->commentTextMinLength) ? $this->optionsSerialized->commentTextMinLength : 10; ?>" name="wc_comment_text_min_length" id="wc_comment_text_min_length" style="width:70px;" />
                    </label>
                    <label for="wc_comment_text_max_length">
                        <?php _e('Max', 'wpdiscuz'); ?>: <input type="number" value="<?php echo isset($this->optionsSerialized->commentTextMaxLength) ? $this->optionsSerialized->commentTextMaxLength : ''; ?>" name="wc_comment_text_max_length" id="wc_comment_text_max_length" style="width:70px;" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="commentWordsLimit"><?php _e('The number of words before breaking comment text and showing "Read more" link', 'wpdiscuz'); ?></label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;">
                        <?php _e('Set this option value 0, to turn off comment text breaking function.', 'wpdiscuz'); ?>
                    </p>
                </th>
                <td><input type="number" value="<?php echo isset($this->optionsSerialized->commentReadMoreLimit) ? $this->optionsSerialized->commentReadMoreLimit : 100; ?>" name="commentWordsLimit" id="commentWordsLimit" style="width:100px;" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Comment text size in pixels', 'wpdiscuz'); ?></th>
                <td>
                    <select id="wc_comment_text_size" name="wc_comment_text_size">
                        <?php $wc_comment_text_size = isset($this->optionsSerialized->commentTextSize) ? $this->optionsSerialized->commentTextSize : '14px'; ?>
                        <option value="12px" <?php selected($wc_comment_text_size, '12px'); ?>>12px</option>
                        <option value="13px" <?php selected($wc_comment_text_size, '13px'); ?>>13px</option>
                        <option value="14px" <?php selected($wc_comment_text_size, '14px'); ?>>14px</option>
                        <option value="15px" <?php selected($wc_comment_text_size, '15px'); ?>>15px</option>
                        <option value="16px" <?php selected($wc_comment_text_size, '16px'); ?>>16px</option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><?php _e('Allow comment editing for', 'wpdiscuz'); ?></th>
                <td>
                    <select id="wc_comment_editable_time" name="wc_comment_editable_time">
                        <?php $wc_comment_editable_time = isset($this->optionsSerialized->commentEditableTime) ? $this->optionsSerialized->commentEditableTime : 0; ?>
                        <option value="0" <?php selected($wc_comment_editable_time, '0'); ?>><?php _e('Do not allow', 'wpdiscuz'); ?></option>
                        <option value="900" <?php selected($wc_comment_editable_time, '900'); ?>>15 <?php _e('Minutes', 'wpdiscuz'); ?></option>
                        <option value="1800" <?php selected($wc_comment_editable_time, '1800'); ?>>30 <?php _e('Minutes', 'wpdiscuz'); ?></option>
                        <option value="3600" <?php selected($wc_comment_editable_time, '3600'); ?>>1 <?php _e('Hour', 'wpdiscuz'); ?></option>
                        <option value="10800" <?php selected($wc_comment_editable_time, '10800'); ?>>3 <?php _e('Hours', 'wpdiscuz'); ?></option>
                        <option value="86400" <?php selected($wc_comment_editable_time, '86400'); ?>>24 <?php _e('Hours', 'wpdiscuz'); ?></option>
                        <option value="unlimit" <?php selected($wc_comment_editable_time, 'unlimit'); ?>><?php _e('Unlimit', 'wpdiscuz'); ?></option>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Redirect first commenter to', 'wpdiscuz'); ?>
                </th>
                <td>
                    <?php
                    wp_dropdown_pages(array(
                        'name' => 'wpdiscuz_redirect_page',
                        'selected' => isset($this->optionsSerialized->redirectPage) ? $this->optionsSerialized->redirectPage : 0,
                        'show_option_none' => __('Do not redirect','wpdiscuz'),
                        'option_none_value' => 0
                    ));
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wc_is_guest_can_vote"><?php _e('Allow guests to vote on comments', 'wpdiscuz'); ?></label></th>
                <td><input type="checkbox" <?php checked($this->optionsSerialized->isGuestCanVote == 1) ?> value="1" name="wc_is_guest_can_vote" id="wc_is_guest_can_vote" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comments loading/pagination type', 'wpdiscuz'); ?>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;">
                        <?php _e('You can manage the number of comments for [Load more] option in Settings > Discussion page, using "Break comments into pages with [X] top level comments per page" option. To show the default Wordpress comment pagination you should enable the checkbox on bigining of the same option.', 'wpdiscuz'); ?>
                    </p>
                </th>
                <td>
                    <fieldset class="commentListLoadType">
                        <?php $commentListLoadType = isset($this->optionsSerialized->commentListLoadType) ? $this->optionsSerialized->commentListLoadType : 0; ?>
                        <label title="<?php _e('[Load more] Button', 'wpdiscuz') ?>">
                            <input type="radio" value="0" <?php checked('0' == $commentListLoadType); ?> name="commentListLoadType" id="commentListLoadDefault" /> 
                            <span><?php _e('[Load more] Button', 'wpdiscuz') ?></span>
                        </label> &nbsp;<br/>
                        <label title="<?php _e('[Load rest of all comments] Button', 'wpdiscuz') ?>">
                            <input type="radio" value="1" <?php checked('1' == $commentListLoadType); ?> name="commentListLoadType" id="commentListLoadRest" /> 
                            <span><?php _e('[Load rest of all comments] Button', 'wpdiscuz') ?></span>
                        </label><br>    
                        <label title="<?php _e('Lazy load comments on scrolling', 'wpdiscuz') ?>">
                            <input type="radio" value="2" <?php checked('2' == $commentListLoadType); ?> name="commentListLoadType" id="commentListLoadLazy" /> 
                            <span><?php _e('Lazy load comments on scrolling', 'wpdiscuz') ?></span>
                        </label> &nbsp;<br/>          
                    </fieldset>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row">
                    <label for="wc_simple_comment_date"><?php _e('Use WordPress Date/Time format', 'wpdiscuz'); ?></label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;"><?php _e('wpDiscuz shows Human Readable date format. If you check this option it\'ll show the date/time format set in WordPress General Settings.', 'wpdiscuz'); ?></p>
                </th>
                <td>                                
                    <input type="checkbox" <?php checked($this->optionsSerialized->simpleCommentDate == 1) ?> value="1" name="wc_simple_comment_date" id="wc_simple_comment_date" />&nbsp;
                    <span style="font-size:13px; color:#999999; padding-left:0px; margin-left:0px; line-height:15px">
                        <?php echo date(get_option('date_format')); ?> / <?php echo date(get_option('time_format')); ?><br />
                        <?php _e('Current Wordpress date/time format', 'wpdiscuz'); ?>
                    </span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" >
                    <label for="wc_is_use_po_mo"><?php _e('Use Plugin .PO/.MO files', 'wpdiscuz'); ?></label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;"><?php _e('wpDiscuz phrase system allows you to translate all front-end phrases. However if you have a multi-language website it\'ll not allow you to add more than one language translation. The only way to get it is the plugin translation files (.PO / .MO). If wpDiscuz has the languages you need you should check this option to disable phrase system and it\'ll automatically translate all phrases based on language files according to current language.', 'wpdiscuz'); ?></p>
                </th>
                <td colspan="3"><input type="checkbox" <?php checked($this->optionsSerialized->isUsePoMo == 1) ?> value="1" name="wc_is_use_po_mo" id="wc_is_use_po_mo" /></td>
            </tr>
            <tr valign="top">
                <th scope="row" >
                    <label for="wc_show_plugin_powerid_by">
                        <?php _e('Help wpDiscuz to grow allowing people to recognize which comment plugin you use', 'wpdiscuz'); ?>
                    </label>
                    <p style="font-size:13px; color:#999999; width:80%; padding-left:0px; margin-left:0px;"><?php _e('Please check this option on to help wpDiscuz get more popularity as your thank to the hard work we do for you totally free. This option adds a very small (16x16px) icon under the comment section which will allow your site visitors recognize the name of comment solution you use.', 'wpdiscuz'); ?></p>
                </th>
                <td colspan="3">                                
                    <label for="wc_show_plugin_powerid_by">
                        <input type="checkbox" <?php checked($this->optionsSerialized->showPluginPoweredByLink == 1) ?> value="1" name="wc_show_plugin_powerid_by" id="wc_show_plugin_powerid_by" />
                        <span id="wpdiscuz_thank_you" style="color:#006600; font-size:13px;"><?php _e('Thank you!', 'wpdiscuz'); ?></span>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>