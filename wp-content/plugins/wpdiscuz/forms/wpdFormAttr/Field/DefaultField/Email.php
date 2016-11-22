<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Email extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD;
    protected $isDefault = true;
    private $isAnonymous;

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo $this->display; ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo $this->type; ?>" name="<?php echo $this->fieldInputName; ?>[type]" />
                <label><?php _e('Name', 'wpdiscuz'); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo $this->fieldData['name']; ?>" name="<?php echo $this->fieldInputName; ?>[name]" required />
                <p class="wpd-info"><?php _e('Also used for field placeholder', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Description', 'wpdiscuz'); ?>:</label> 
                <input type="text" value="<?php echo $this->fieldData['desc']; ?>" name="<?php echo $this->fieldInputName; ?>[desc]" />
                <p class="wpd-info"><?php _e('Field specific short description or some rule related to inserted information.', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <div class="input-group">
                    <label><span class="input-group-addon"></span> <?php _e('Field icon', 'wpdiscuz'); ?>:</label>
                    <input data-placement="bottom" class="icp icp-auto" value="<?php echo $this->fieldData['icon']; ?>" type="text" name="<?php echo $this->fieldInputName; ?>[icon]"/>
                </div>
                <p class="wpd-info"><?php _e('Font-awesome icon library.', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Field is required', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['required'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[required]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId,$isMainForm) {
        if (!$currentUser->ID) {
            $hasIcon = $args['icon'] ? true : false;
            ?>
            <div class="wpdiscuz-item <?php echo $hasIcon ? 'wpd-has-icon' : '' ?>">
                <?php if ($hasIcon) { ?>
                    <div class="wpd-field-icon"><i class="fa <?php echo $args['icon']; ?>"></i></div>
                <?php } ?>
                <?php $required = $args['required'] ? 'required="required"' : ''; ?>
                <input <?php echo $required; ?> class="<?php echo $name; ?> wpd-field" type="email" name="<?php echo $name; ?>" value="" placeholder="<?php echo $args['name']; ?>">
                <?php if ($args['desc']) { ?>
                    <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                <?php } ?>
            </div>
            <?php
        }
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => __('Email', 'wpdiscuz'),
            'desc' => '',
            'icon' => 'fa-at',
            'required' => '0',
        );
    }

    public function isAnonymous() {
        return $this->isAnonymous;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        $email = isset($_POST[$fieldName]) ? trim($_POST[$fieldName]) : '';
        if (!$args['required']) {
            if (!$email) {
                $email = uniqid() . '@example.com';
                $this->isAnonymous = true;
            }
        }

        if ($email != '' && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $messageArray['code'] = 'wc_error_email_text';
            wp_die(json_encode($messageArray));
        }

        return $email;
    }

    public function frontHtml($value, $args) {
        
    }

    public function editCommentHtml($key, $value, $data,$comment) {
        
    }

}
