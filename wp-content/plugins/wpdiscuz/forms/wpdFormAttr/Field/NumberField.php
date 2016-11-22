<?php

namespace wpdFormAttr\Field;

class NumberField extends Field {

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
                <label><?php _e('Min Value', 'wpdiscuz'); ?>:</label> 
                <input type="number" value="<?php echo $this->fieldData['min']; ?>" name="<?php echo $this->fieldInputName; ?>[min]" />
                <p class="wpd-info"><?php _e('Field specific short description or some rule related to inserted information.', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Max Value', 'wpdiscuz'); ?>:</label> 
                <input type="number" value="<?php echo $this->fieldData['max']; ?>" name="<?php echo $this->fieldInputName; ?>[max]" />
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
            <div class="wpd-field-option">
                <label><?php _e('Display on reply form', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['is_show_sform'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[is_show_sform]" />
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Display on comment', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['is_show_on_comment'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[is_show_on_comment]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function editCommentHtml($key, $value, $data,$comment) {
        if($comment->comment_parent && !$data['is_show_sform']){
            return '';
        }
        $html = '<tr><td class="first">';
        $html .= '<label for = "' . $key . '">' . $data['name'] . ': </label>';
        $html .= '</td><td>';
        $html .= '<div class="wpdiscuz-item">';
        $required = $data['required'] ? 'required="required"' : '';
        $min = is_numeric($data['min']) ? 'min="' . $data['min'] . '"' : '';
        $max = is_numeric($data['max']) ? 'max="' . $data['max'] . '"' : '';
        $html .= '<input  ' . $required . ' class="wpd-field wpd-field-number" type="number" id="' . $key . '" value="' . $value . '"  name="' . $key . '"  ' . $min . ' ' . $max . '>';
        $html .= '</div>';
        $html .= '</td></tr >';
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId,$isMainForm) {
        if(!$isMainForm && !$args['is_show_sform']){
            return;
        }
        $hasIcon = $args['icon'] ? true : false;
        $hasDesc = $args['desc'] ? true : false;
        ?>
        <div class="wpdiscuz-item <?php echo $hasIcon ? 'wpd-has-icon' : '' ?> <?php echo $hasDesc ? 'wpd-has-desc' : '' ?>">
            <?php if ($hasIcon) { ?>
                <div class="wpd-field-icon"><i style="opacity: 0.8;" class="fa <?php echo $args['icon']; ?>"></i></div>
            <?php } ?>
            <?php
            $required = $args['required'] ? 'required="required"' : '';
            $min = is_numeric($args['min']) ? 'min="' . $args['min'] . '"' : '';
            $max = is_numeric($args['max']) ? 'max="' . $args['max'] . '"' : '';
            ?>
            <input <?php echo $required; ?> class="<?php echo $name; ?> wpd-field wpd-field-number" type="number" name="<?php echo $name; ?>" value="" placeholder="<?php _e($args['name'], 'wpdiscuz'); ?>"  <?php echo $min . ' ' . $max; ?>>
            <?php if ($args['desc']) { ?>
                <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                    <?php } ?>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        if(!$args['is_show_on_comment']){
            return '';
        }
        $html = '<div class="wpd-custom-field wpd-cf-text">';
        $html .= '<div class="wpd-cf-label">' . $args['name'] . '</div> <div class="wpd-cf-value"> ' . $value . '</div>';
        $html .= '</div>';
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if(!$this->isCommentParentZero() && !$args['is_show_sform']){
            return '';
        }
        $value = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT);
        if (!$value && $args['required']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('field is required!', 'wpdiscuz'));
        }
        $value = intval($value);
        if (is_int($args['min']) &&  $value < $args['min']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('value can not be less than', 'wpdiscuz') . ' ' . $args['min']);
        }
        if (is_int($args['max']) && $value > $args['max']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('value can not be more than', 'wpdiscuz') . ' ' . $args['max']);
        }
        
        return $value;
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

        if (isset($data['values'])) {
            $values = array_filter(explode("\n", trim(strip_tags($data['values']))));
            foreach ($values as $value) {
                $cleanData['values'][] = trim($value);
            }
        }

        if (isset($data['icon'])) {
            $cleanData['icon'] = trim(strip_tags($data['icon']));
        }
        if (isset($data['required'])) {
            $cleanData['required'] = intval($data['required']);
        }

        if (isset($data['min']) && trim($data['min']) != '') {
            $cleanData['min'] = intval($data['min']);
        } else {
            $cleanData['min'] = '';
        }

        if (isset($data['max']) && trim($data['max']) != '') {
            $cleanData['max'] = intval($data['max']);
        } else {
            $cleanData['max'] = '';
        }

        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => '',
            'desc' => '',
            'values' => array(),
            'icon' => '',
            'required' => '0',
            'loc' => 'bottom',
            'min' => '',
            'max' => '',
            'is_show_sform' => 0,
            'is_show_on_comment' => 1
        );
    }

}
