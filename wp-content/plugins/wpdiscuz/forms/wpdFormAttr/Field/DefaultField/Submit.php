<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Submit extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD;
    protected $isDefault = true;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo $this->display; ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo $this->type; ?>" name="<?php echo $this->fieldInputName; ?>[type]" />
                <label><?php _e('Name', 'wpdiscuz'); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo $this->fieldData['name']; ?>" name="<?php echo $this->fieldInputName; ?>[name]" required />
                <p class="wpd-info"><?php _e('Button Text', 'wpdiscuz'); ?></p>
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }


    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId,$isMainForm) {
        global $wpdiscuz,$post;
        ?>
        <div class="wc-field-submit">
            <?php if ($options->wordpressThreadComments || class_exists('Prompt_Comment_Form_Handling')) { ?>
                <?php
                $isShowSubscribeWrapper = false;
                if ($options->showHideReplyCheckbox) {
                    if ($currentUser->ID) {
                        $subscriptionData = $wpdiscuz->dbManager->hasSubscription($post->ID, $currentUser->user_email);
                        $isShowSubscribeWrapper = !$subscriptionData || ($subscriptionData && $subscriptionData['type'] == $wpdiscuz::SUBSCRIPTION_COMMENT) ? true : false;
                    } else {
                        $isShowSubscribeWrapper = true;
                    }
                }
                if ($isShowSubscribeWrapper) {
                    $isReplyDefaultChecked = $options->isReplyDefaultChecked ? 'checked="checked"' : '';
                    ?>
                    <div class="wc_notification_checkboxes" style="display:block">
                        <?php
                        if (class_exists('Prompt_Comment_Form_Handling') && $options->usePostmaticForCommentNotification) {
                            ?>
                            <input id="wc_notification_new_comment-<?php echo $uniqueId; ?>" class="wc_notification_new_comment-<?php echo $uniqueId; ?>" value="post"  type="checkbox" name="wpdiscuz_notification_type"/> <label class="wc-label-comment-notify" for="wc_notification_new_comment-<?php echo $uniqueId; ?>"><?php echo $options->phrases['wc_postmatic_subscription_label']; ?></label><br />
                            <?php
                        } else {
                            ?>
                            <input id="wc_notification_new_comment-<?php echo $uniqueId; ?>" class="wc_notification_new_comment-<?php echo $uniqueId; ?>" value="comment"  type="checkbox" name="wpdiscuz_notification_type" <?php echo $isReplyDefaultChecked; ?>/> <label class="wc-label-comment-notify" for="wc_notification_new_comment-<?php echo $uniqueId; ?>"><?php echo $options->phrases['wc_notify_on_new_reply']; ?></label><br />
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <input class="wc_comm_submit button alt" type="submit" name="<?php echo $name; ?>" value="<?php echo $args['name']; ?>">
        </div>
        <?php
    }

    public function sanitizeFieldData($data) {
        $cleanData = array();
        $cleanData['type'] = $data['type'];
        if (isset($data['name'])) {
            $name = trim(strip_tags($data['name']));
            $cleanData['name'] = $name ? $name : $this->fieldDefaultData['name'];
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => __('Post Comment', 'wpdiscuz'),
        );
    }

    public function frontHtml($value,$args) {}
    
    public function validateFieldData($fieldName,$args, $options, $currentUser) {}
    
    public function editCommentHtml($key, $value ,$data,$comment) {}

}
