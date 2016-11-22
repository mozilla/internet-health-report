<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Comment Template Phrases', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Reply', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_reply_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_reply_text']; ?>" name="wc_reply_text" id="wc_submit_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Share', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_text']; ?>" name="wc_share_text" id="wc_share_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Edit', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_edit_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_edit_text']; ?>" name="wc_edit_text" id="wc_edit_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Share On Facebook', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_facebook">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_facebook']; ?>" name="wc_share_facebook" id="wc_share_facebook" />
                    </label>
                </td>
            </tr>
            <tr valign="top" >
                <th scope="row">
                    <?php _e('Share On Twitter', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_twitter">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_twitter']; ?>" name="wc_share_twitter" id="wc_share_twitter" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Share On Google', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_google">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_google']; ?>" name="wc_share_google" id="wc_share_google" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Share On VKontakte', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_vk">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_vk']; ?>" name="wc_share_vk" id="wc_share_vk" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Share On Odnoklassniki', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_share_ok">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_share_ok']; ?>" name="wc_share_ok" id="wc_share_ok" />
                    </label>
                </td>
            </tr>
            <tr valign="top" >
                <th scope="row">
                    <?php _e('Hide Replies', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_hide_replies_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_hide_replies_text']; ?>" name="wc_hide_replies_text" id="wc_hide_replies_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Show Replies', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_show_replies_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_show_replies_text']; ?>" name="wc_show_replies_text" id="wc_show_replies_text" />
                    </label>
                </td>
            </tr>
            <?php
            $roles = $this->optionsSerialized->blogRoles;
            foreach ($roles as $roleName => $color) {
                $phraseRoleLabel = ucfirst(str_replace('_', ' ', $roleName));

                if ($roleName == 'administrator') {
                    $roleTitle = isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : __('Admin', 'wpdiscuz');
                } elseif ($roleName == 'post_author') {
                    $roleTitle = isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : __('Author', 'wpdiscuz');
                } elseif ($roleName == 'editor') {
                    $roleTitle = isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : ucfirst(str_replace('_', ' ', $roleName));
                } elseif ($roleName == 'guest') {
                    $roleTitle = isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : __('Guest', 'wpdiscuz');
                } else {
                    $roleTitle = isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : __('Member', 'wpdiscuz');
                }
                ?>
                <tr valign="top">
                    <th scope="row"><?php echo $phraseRoleLabel; ?></th>
                    <td>                        
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_blog_role_' . $roleName]) ? $this->optionsSerialized->phrases['wc_blog_role_' . $roleName] : $roleTitle; ?>" id="wc_blog_role_<?php echo $roleName; ?>" name="wc_blog_role_<?php echo $roleName; ?>"/>
                    </td>
                </tr>
                <?php
            }
            ?>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Vote Up', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_vote_up">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_vote_up']; ?>" name="wc_vote_up" id="wc_vote_up" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Vote Down', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_vote_down">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_vote_down']; ?>" name="wc_vote_down" id="wc_vote_down" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Save edited comment button text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_edit_save_button">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_comment_edit_save_button']) ? $this->optionsSerialized->phrases['wc_comment_edit_save_button'] : __('Save', 'wpdisucz'); ?>" name="wc_comment_edit_save_button" id="wc_comment_edit_save_button" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Cancel comment editing button text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_comment_edit_cancel_button">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_comment_edit_cancel_button']) ? $this->optionsSerialized->phrases['wc_comment_edit_cancel_button'] : __('Cancel', 'wpdisucz'); ?>" name="wc_comment_edit_cancel_button" id="wc_comment_edit_cancel_button" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Comment read more link text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_read_more">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_read_more']) ? $this->optionsSerialized->phrases['wc_read_more'] : __('Read more &raquo;', 'wpdisucz'); ?>" name="wc_read_more" id="wc_read_more" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Anonymous commenter name', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_anonymous">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_anonymous']) ? $this->optionsSerialized->phrases['wc_anonymous'] : __('Anonymous', 'wpdisucz'); ?>" name="wc_anonymous" id="wc_anonymous" />
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>