<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Website extends Field {

    protected $name = wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD;
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
                <label><?php _e('Enable', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['enable'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[enable]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$currentUser->ID) {
            $hasIcon = $args['icon'] ? true : false;
            if ($args['enable']) {
                ?>
                <div class="wpdiscuz-item <?php echo $hasIcon ? 'wpd-has-icon' : ''?>">
                    <?php if ($hasIcon) { ?>
                    <div class="wpd-field-icon"><i class="fa <?php echo $args['icon']; ?>"></i></div>
                    <?php } ?>
                    <input class="<?php echo $name; ?> wpd-field" type="text" name="<?php echo $name; ?>" value="" placeholder="<?php echo $args['name']; ?>">
                    <?php if ($args['desc']) { ?>
                        <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                    <?php } ?>
                </div>
                <?php
            }
        }
    }

    public function sanitizeFieldData($data) {
        $cleanData = array();
        $cleanData['type'] = $data['type'];
        if (isset($data['name'])) {
            $name = trim(strip_tags($data['name']));
            $cleanData['name'] = $name ? $name : $this->fieldDefaultData['name'];
        }
        if (isset($data['desc'])) {
            $cleanData['desc'] = trim(strip_tags($data['desc']));
        }
        if (isset($data['icon'])) {
            $cleanData['icon'] = trim(strip_tags($data['icon']));
        }
        if (isset($data['enable'])) {
            $cleanData['enable'] = intval($data['enable']);
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => __('Website', 'wpdiscuz'),
            'desc' => '',
            'icon' => 'fa-link',
            'enable' => '0',
        );
    }

    public function validateFieldData($fieldName,$args, $options, $currentUser) {
        $website_url = trim(filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_STRING));
        if ($website_url != '' && (strpos($website_url, 'http://') !== '' && strpos($website_url, 'http://') !== 0) && (strpos($website_url, 'https://') !== '' && strpos($website_url, 'https://') !== 0)) {
            $website_url = 'http://' . $website_url;
        }

        if ($website_url != '' && (filter_var($website_url, FILTER_VALIDATE_URL) === false)) {
            $messageArray['code'] = 'wc_error_url_text';
            wp_die(json_encode($messageArray));
        }
        return $website_url;
    }
    
    public function editCommentHtml($key, $value ,$data,$comment) {}
    public function frontHtml($value,$args) {}

}
