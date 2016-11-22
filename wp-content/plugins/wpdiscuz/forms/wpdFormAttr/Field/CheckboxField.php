<?php

namespace wpdFormAttr\Field;

class CheckboxField extends Field {

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
                <p class="wpd-info"><?php _e('Please insert one value per line', 'wpdiscuz'); ?></p>
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
        if($comment->comment_parent && !$data['is_show_sform']){
            return '';
        }
        $valuesMeta = maybe_unserialize($value);
        $values = is_array($valuesMeta) ? $valuesMeta : array();
        $html = '<tr><td class="first">';
        $html .= '<label for = "' . $key . '">' . $data['name'] . ': </label>';
        $html .= '</td><td>';
        $required = $data['required'] ? ' wpd-required-group ' : '';
        $html .= '<div class="wpdiscuz-item ' . $required . ' wpd-field-group">';
        foreach ($data['values'] as $index => $val) {
            $uniqueId = uniqid();
            $checked = in_array($val, $values) ? ' checked="checked" ' : '';
            $index = $index + 1;
            $html .= '<input ' . $checked . '  id="' . $key . '-' . $index . '_' . $uniqueId . '" type="checkbox" name="' . $key . '[]" value="' . $index . '" class="' . $key . ' wpd-field wpd-field-checkbox" > <label class="wpd-field-label wpd-cursor-pointer" for="' . $key . '-' . $index . '_' . $uniqueId . '">' . $val . '</label>';
        }
        $html .= '</div>';
        $html .= '</td></tr >';
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (empty($args['values']) || (!$isMainForm && !$args['is_show_sform']))
            return;
        $hasDesc = $args['desc'] ? true : false;
        $required = $args['required'] ? ' wpd-required-group ' : '';
        
        if (count($args['values']) == 1): ?>
            <div class="wpdiscuz-item wpd-field-group wpd-field-checkbox wpd-field-single  <?php echo $required; ?>  <?php echo $hasDesc ? 'wpd-has-desc' : '' ?>">
                <div class="wpd-field-group-title">
                    <div class="wpd-item">
                        <input id="<?php echo $name . '-1_' . $uniqueId; ?>" type="checkbox" name="<?php echo $name; ?>[]" value="1" class="<?php echo $name; ?> wpd-field"  <?php echo $args['required'] ? 'required' : ''; ?>>
                        <label class="wpd-field-label wpd-cursor-pointer" for="<?php echo $name . '-1_' . $uniqueId; ?>"><?php echo $args['values'][0]; ?></label>
                    </div>
                </div>
            <?php if ($args['desc']) { ?>
                    <div class="wpd-field-desc">
                        <i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span>
                    </div>
            <?php } ?>
            </div>
            <?php else: ?>
            <div class="wpdiscuz-item wpd-field-group wpd-field-checkbox <?php echo $required; ?> <?php echo $hasDesc ? 'wpd-has-desc' : '' ?>">
                <div class="wpd-field-group-title"><?php _e($args['name'], 'wpdiscuz'); ?></div>
                <?php if ($args['desc']) { ?>
                    <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                    <?php } ?>
                <div class="wpd-item-wrap">
                    <?php
                    foreach ($args['values'] as $index => $val) {
                        ?>
                        <div class="wpd-item">
                            <input id="<?php echo $name . '-' . ($index + 1) . '_' . $uniqueId; ?>" type="checkbox" name="<?php echo $name; ?>[]" value="<?php echo $index + 1; ?>" class="<?php echo $name; ?> wpd-field" >
                            <label class="wpd-field-label wpd-cursor-pointer" for="<?php echo $name . '-' . ($index + 1) . '_' . $uniqueId; ?>"><?php echo $val; ?></label>
                        </div>
            <?php }
            ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
    }

    public function frontHtml($value, $args) {
        if(!$args['is_show_on_comment']){
            return '';
        }
        $html = '<div class="wpd-custom-field wpd-cf-text">';
        $html .= '<div class="wpd-cf-label">' . $args['name'] . '</div> <div class="wpd-cf-value"> ' . implode(' , ', $value) . '</div>';
        $html .= '</div>';
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        if(!$this->isCommentParentZero() && !$args['is_show_sform']){
            return array();
        }
        $values = filter_input(INPUT_POST, $fieldName, FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);
        $tempValues = is_array($values) ? array_filter($values) : array();
        $values = array();
        foreach ($tempValues as $val) {
            if ($val < 1 || !key_exists($val - 1, $args['values'])) {
                continue;
            }
            $values[] = $args['values'][$val - 1];
        }

        if (!$values && $args['required']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('field is required!', 'wpdiscuz'));
        }
        return $values;
    }

}
