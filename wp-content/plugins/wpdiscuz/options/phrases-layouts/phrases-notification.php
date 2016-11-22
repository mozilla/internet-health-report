<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Notification Phrases', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <?php _e("You\'re subscribed to", 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_subscribed_to">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_subscribed_to']; ?>" name="wc_subscribed_to" id="wc_subscribed_to" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You\'ve successfully subscribed.', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_subscribe_message">
                        <textarea name="wc_subscribe_message" id="wc_subscribe_message"><?php echo $this->optionsSerialized->phrases['wc_subscribe_message']; ?></textarea>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You\'ve successfully unsubscribed.', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_unsubscribe_message">
                        <textarea name="wc_unsubscribe_message" id="wc_unsubscribe_message"><?php echo $this->optionsSerialized->phrases['wc_unsubscribe_message']; ?></textarea>
                    </label>
                </td>
            </tr>
            <?php if (class_exists('Prompt_Comment_Form_Handling') && $this->optionsSerialized->usePostmaticForCommentNotification) { ?>
                <tr valign="top">
                    <th scope="row">
                        <?php _e("Postmatic subscription label", 'wpdiscuz'); ?>
                    </th>
                    <td colspan="3">                                
                        <label for="wc_postmatic_subscription_label">
                            <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_postmatic_subscription_label']; ?>" name="wc_postmatic_subscription_label" id="wc_postmatic_subscription_label" />
                        </label>
                    </td>
                </tr>
            <?php } ?> 
            <tr valign="top">
                <th scope="row">
                    <?php _e('Error message for empty field', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_error_empty_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_error_empty_text']; ?>" name="wc_error_empty_text" id="wc_error_empty_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Error message for invalid email field', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_error_email_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_error_email_text']; ?>" name="wc_error_email_text" id="wc_error_email_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Error message for invalid website url field', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_error_url_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_error_url_text']; ?>" name="wc_error_url_text" id="wc_error_url_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You must be', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_you_must_be_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_you_must_be_text']; ?>" name="wc_you_must_be_text" id="wc_you_must_be_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Logged in as', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_logged_in_as">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_logged_in_as']; ?>" name="wc_logged_in_as" id="wc_logged_in_as" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Log out', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_log_out">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_log_out']; ?>" name="wc_log_out" id="wc_log_out" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Logged In', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_logged_in_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_logged_in_text']; ?>" name="wc_logged_in_text" id="wc_logged_in_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('To post a comment', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_to_post_comment_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_to_post_comment_text']; ?>" name="wc_to_post_comment_text" id="wc_to_post_comment_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Vote Counted', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_vote_counted">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_vote_counted']; ?>" name="wc_vote_counted" id="wc_vote_counted" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You can vote only 1 time', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_vote_only_one_time">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_vote_only_one_time']; ?>" name="wc_vote_only_one_time" id="wc_vote_only_one_time" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Voting Error', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_voting_error">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_voting_error']; ?>" name="wc_voting_error" id="wc_voting_error" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Login To Vote', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_login_to_vote">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_login_to_vote']; ?>" name="wc_login_to_vote" id="wc_login_to_vote" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You Cannot Vote On Your Comment', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_self_vote">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_self_vote']; ?>" name="wc_self_vote" id="wc_self_vote" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('You are not allowed to vote for this comment (Voting from same IP)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_deny_voting_from_same_ip">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_deny_voting_from_same_ip']) ? $this->optionsSerialized->phrases['wc_deny_voting_from_same_ip'] : 'You are not allowed to vote for this comment'; ?>" name="wc_deny_voting_from_same_ip" id="wc_deny_voting_from_same_ip" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Invalid Captcha Code', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_invalid_captcha">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_invalid_captcha']; ?>" name="wc_invalid_captcha" id="wc_invalid_captcha" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Some of field value is invalid', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_invalid_field">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_invalid_field']; ?>" name="wc_invalid_field" id="wc_invalid_field" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment waiting moderation', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_held_for_moderate">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_held_for_moderate']; ?>" name="wc_held_for_moderate" id="wc_held_for_moderate" />
                    </label>
                </td>
            </tr>            
            <tr valign="top">
                <th scope="row">
                    <?php _e('Message if input text length is too short', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_msg_input_min_length">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_msg_input_min_length']; ?>" name="wc_msg_input_min_length" id="wc_msg_input_min_length" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Message if input text length is too long', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_msg_input_max_length">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_msg_input_max_length']; ?>" name="wc_msg_input_max_length" id="wc_msg_input_max_length" />
                    </label>
                </td>
            </tr>
            
            <tr valign="top">
                <th scope="row">
                    <?php _e('Message if comment was not updated', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_not_updated">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_comment_not_updated']) ? $this->optionsSerialized->phrases['wc_comment_not_updated'] : __('Sorry, the comment was not updated', 'wpdisucz'); ?>" name="wc_comment_not_updated" id="wc_comment_not_updated" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Message if comment no longer possible to edit', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_edit_not_possible">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_comment_edit_not_possible']) ? $this->optionsSerialized->phrases['wc_comment_edit_not_possible'] : __('Sorry, this comment no longer possible to edit', 'wpdisucz'); ?>" name="wc_comment_edit_not_possible" id="wc_comment_edit_not_possible" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Message if comment text not changed', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_not_edited">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_comment_not_edited']) ? $this->optionsSerialized->phrases['wc_comment_not_edited'] : __('TYou\'ve not made any changes', 'wpdisucz'); ?>" name="wc_comment_not_edited" id="wc_comment_not_edited" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Please fill out required fields', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_msg_required_fields">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_msg_required_fields']) ? $this->optionsSerialized->phrases['wc_msg_required_fields'] : __('Please fill out required fields', 'wpdisucz'); ?>" name="wc_msg_required_fields" id="wc_msg_required_fields" />
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>