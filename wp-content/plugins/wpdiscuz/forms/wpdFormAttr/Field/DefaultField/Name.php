<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Name extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD;
    protected $isDefault = true;

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
            $nameLengthRange = (intval($options->commenterNameMinLength) >= 3 && (intval($options->commenterNameMaxLength) >= 3 && intval($options->commenterNameMaxLength) <= 50)) ? 'pattern=".{' . $options->commenterNameMinLength . ',' . $options->commenterNameMaxLength . '}"' : ''; ?>
            <div class="wpdiscuz-item <?php echo $hasIcon ? 'wpd-has-icon' : ''?>">
                <?php if ($args['icon']) { ?>
                <div class="wpd-field-icon"><i class="fa <?php echo $args['icon']; ?>"></i></div>
                <?php } ?>
                <?php $required = $args['required'] ? 'required="required"' : ''; ?>
                <input <?php echo $required; ?> class="<?php echo $name; ?> wpd-field" type="text" name="<?php echo $name; ?>" value="" placeholder="<?php _e($args['name'],'wpdiscuz')//echo $args['name']; ?>" maxlength="<?php echo $options->commenterNameMaxLength; ?>" <?php echo $nameLengthRange; ?> title="">
                <?php if ($args['desc']) { ?>
                    <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                <?php } ?>
            </div>
            <?php
        }
    }


    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => __('Name', 'wpdiscuz'),
            'desc' => '',
            'icon' => 'fa-user',
            'required' => '0'
        );
    }

    public function validateFieldData($fieldName,$args, $options, $currentUser) {
        $name = isset($_POST[$fieldName]) ? filter_var($_POST[$fieldName]) : '';
        if (!$args['required']) {
            $name = !($name) ? $options->phrases['wc_anonymous'] : $name;
        }
        return $name;
    }
    
    public function frontHtml($value,$args) {}
    
    public function editCommentHtml($key, $value ,$data,$comment) {}

}
