<?php

namespace wpdFormAttr\Field;

class SelectField extends Field {

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
            <div class="wpd-field-option wpdiscuz-item">
                <?php
                $values = '';
                foreach ($this->fieldData['values'] as $value) {
                    $values .= $value . "\n";
                }
                ?>
                <label><?php _e('Values', 'wpdiscuz'); ?>:</label> 
                <textarea required name="<?php echo $this->fieldInputName; ?>[values]" ><?php echo $values; ?></textarea>
                <p class="wpd-info"><?php _e('New value new line', 'wpdiscuz'); ?></p>
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

    public function editCommentHtml($key, $value, $data, $comment) {
        if ($comment->comment_parent && !$data['is_show_sform']) {
            return '';
        }
        $html = '<tr><td class="first">';
        $html .= '<label for = "' . $key . '">' . $data['name'] . ': </label>';
        $html .= '</td><td>';
        $required = $data['required'] ? ' required="required" ' : '';
        $html .= '<div class="wpdiscuz-item  wpd-field-group wpd-field-select">';
        $html .= '<select name="' . $key . '" class="' . $key . ' wpd-field wpd-field-select wpdiscuz_select" ' . $required . '>';
        $html .= '<option value="">...</option>';
        foreach ($data['values'] as $index => $val) {
            $selected = $value == $val ? ' selected="selected" ' : '';
            $index = $index + 1;
            $html .= '<option ' . $selected . ' value="' . $index . '">' . $val . '</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '</td></tr >';
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (empty($args['values']) || (!$isMainForm && !$args['is_show_sform']))
            return;
        $hasDesc = $args['desc'] ? true : false;
        ?>
        <?php $required = $args['required'] ? ' required="required" ' : ''; ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-select <?php echo $hasDesc ? 'wpd-has-desc' : '' ?>">
            <select <?php echo $required; ?> name="<?php echo $name; ?>" class="<?php echo $name; ?> wpd-field wpdiscuz_select">
                <option value=""><?php echo $args['name']; ?></option>
                <?php foreach ($args['values'] as $index => $val): ?>
                    <option value="<?php echo $index + 1; ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
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
        if (!$this->isCommentParentZero() && !$args['is_show_sform']) {
            return '';
        }
        $value = filter_input(INPUT_POST, $fieldName, FILTER_VALIDATE_INT);
        if (is_int($value) && $value > 0 && key_exists($value - 1, $args['values'])) {
            $value = $args['values'][$value - 1];
        } else {
            $value = '';
        }
        if (!$value && $args['required']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('field is required!', 'wpdiscuz'));
        }
        return $value;
    }

}
