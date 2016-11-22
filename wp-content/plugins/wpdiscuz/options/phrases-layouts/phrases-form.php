<?php 
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Form Template Phrases', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment Field Start', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_start_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_comment_start_text']; ?>" name="wc_comment_start_text" id="wc_comment_start_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment Field Join', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_join_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_comment_join_text']; ?>" name="wc_comment_join_text" id="wc_comment_join_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Email Field', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_email_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_email_text']; ?>" name="wc_email_text" id="wc_email_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Notify of', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_notify_of">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_notify_of']) ? $this->optionsSerialized->phrases['wc_notify_of'] : __('Notify of', 'wpdiscuz'); ?>" name="wc_notify_of" id="wc_notify_of" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Notify on new comments', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_notify_on_new_comment">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_notify_on_new_comment']) ? $this->optionsSerialized->phrases['wc_notify_on_new_comment'] : __('new follow-up comments', 'wpdiscuz'); ?>" name="wc_notify_on_new_comment" id="wc_notify_on_new_comment" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Notify on all new replies', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_notify_on_all_new_reply">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_notify_on_all_new_reply']) ? $this->optionsSerialized->phrases['wc_notify_on_all_new_reply'] : __('new replies to all my comments', 'wpdiscuz'); ?>" name="wc_notify_on_all_new_reply" id="wc_notify_on_all_new_reply" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Notify on new replies (checkbox)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_notify_on_new_reply">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_notify_on_new_reply']) ? $this->optionsSerialized->phrases['wc_notify_on_new_reply'] : __('Notify of new replies to this comment', 'wpdiscuz'); ?>" name="wc_notify_on_new_reply" id="wc_notify_on_new_reply" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Sort by', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_sort_by">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_sort_by']) ? $this->optionsSerialized->phrases['wc_sort_by'] : __('Sort by', 'wpdiscuz'); ?>" name="wc_sort_by" id="wc_sort_by" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('newest', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_newest">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_newest']) ? $this->optionsSerialized->phrases['wc_newest'] : __('newest', 'wpdiscuz'); ?>" name="wc_newest" id="wc_newest" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('oldest', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_oldest">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_oldest']) ? $this->optionsSerialized->phrases['wc_oldest'] : __('oldest', 'wpdiscuz'); ?>" name="wc_oldest" id="wc_oldest" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('most voted', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_most_voted">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_most_voted']) ? $this->optionsSerialized->phrases['wc_most_voted'] : __('most voted', 'wpdiscuz'); ?>" name="wc_most_voted" id="wc_most_voted" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Subscribed on this comment replies', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_subscribed_on_comment">
                        <textarea name="wc_subscribed_on_comment" id="wc_subscribed_on_comment"><?php echo $this->optionsSerialized->phrases['wc_subscribed_on_comment']; ?></textarea>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Subscribed on all your comments replies', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_subscribed_on_all_comment">
                        <textarea name="wc_subscribed_on_all_comment" id="wc_subscribed_on_all_comment"><?php echo $this->optionsSerialized->phrases['wc_subscribed_on_all_comment']; ?></textarea>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Subscribed on this post', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_subscribed_on_post">
                        <textarea name="wc_subscribed_on_post" id="wc_subscribed_on_post"><?php echo $this->optionsSerialized->phrases['wc_subscribed_on_post']; ?></textarea>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Connect with', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_connect_with">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_connect_with']) ? $this->optionsSerialized->phrases['wc_connect_with'] : __('Connect with', 'wpdiscuz'); ?>" name="wc_connect_with" id="wc_connect_with" />
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>