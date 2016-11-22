<?php

namespace wpdFormAttr\Field;

class RatingField extends Field {

    protected function dashboardForm() {
        ?>
        <div class="wpd-field-body" style="display: <?php echo $this->display; ?>">
            <div class="wpd-field-option wpdiscuz-item">
                <input class="wpd-field-type" type="hidden" value="<?php echo $this->type; ?>" name="<?php echo $this->fieldInputName; ?>[type]" />
                <label><?php _e('Name', 'wpdiscuz'); ?>:</label> 
                <input class="wpd-field-name" type="text" value="<?php echo $this->fieldData['name']; ?>" name="<?php echo $this->fieldInputName; ?>[name]" required />
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Description', 'wpdiscuz'); ?>:</label> 
                <input type="text" value="<?php echo $this->fieldData['desc']; ?>" name="<?php echo $this->fieldInputName; ?>[desc]" />
                <p class="wpd-info"><?php _e('Field specific short description or some rule related to inserted information.', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <div class="input-group">
                    <label><span class="input-group-addon"></span> <?php _e('Field icon', 'wpdiscuz'); ?>:</label>
                    <input data-placement="bottom" class="icp icp-auto" value="<?php echo isset($this->fieldData['icon']) ? $this->fieldData['icon'] : 'fa-star' ; ?>" type="text" name="<?php echo $this->fieldInputName; ?>[icon]"/>
                </div>
                <p class="wpd-info"><?php _e('Font-awesome icon library.', 'wpdiscuz'); ?></p>
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Field is required', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['required'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[required]" />
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
        if ($comment->comment_parent) {
            return '';
        }
        $html = '<tr><td class="first">';
        $html .= '<label for = "' . $key . '">' . $data['name'] . ': </label>';
        $html .= '</td><td>';
        $uniqueId = uniqid();
        $required = $data['required'] ? ' wpd-required-group ' : '';
        $html .= '<div class="wpdiscuz-item wpd-field-group wpd-field-rating ' . $required . '">';
        $html .= '<fieldset class="wpdiscuz-rating">';
        for ($i = 5; $i >= 1; $i--) {
            $checked = ($i == $value) ? 'checked="checked"' : '';
            $html .= '<input type="radio" id="wpdiscuz-star_' . $uniqueId . '_' . $i . '" name="' . $key . '" value="' . $i . '"  ' . $checked . '/>';
            $html .= '<label class=" a full fa '.$data['icon'].'" for="wpdiscuz-star_' . $uniqueId . '_' . $i . '" title="' . $i . '" ></label>';
        }
        $html .= '</fieldset>';
        $html .= '</div>';
        $html .= '</td></tr >';
        return $html;
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId, $isMainForm) {
        if (!$isMainForm)
            return;
        $hasDesc = $args['desc'] ? true : false;
        $required = $args['required'] ? ' wpd-required-group ' : '';
        $uniqueId = uniqid($uniqueId);
        ?>
        <div class="wpdiscuz-item wpd-field-group wpd-field-rating <?php echo $required; ?> <?php echo $hasDesc ? 'wpd-has-desc' : '' ?>">
            <div class="wpd-field-group-title">
                <?php _e($args['name'], 'wpdiscuz'); ?>
                <?php if ($args['desc']) { ?>
                    <div class="wpd-field-desc"><i class="fa fa-question-circle-o" aria-hidden="true"></i><span><?php echo esc_html($args['desc']); ?></span></div>
                <?php } ?>
            </div>
            <div class="wpd-item-wrap">
                <fieldset class="wpdiscuz-rating">
                    <?php
                    for ($i = 5; $i >= 1; $i--) {
                        ?>
                        <input type="radio" id="wpdiscuz-star_<?php echo $uniqueId . '_' . $i; ?>" name="<?php echo $name; ?>" value="<?php echo $i; ?>" />
                        <label class = "fa <?php echo $args['icon'];?> full" for="wpdiscuz-star_<?php echo $uniqueId . '_' . $i; ?>" title="<?php echo $i; ?>"></label>
                    <?php }
                    ?>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
    }

    public function frontHtml($value, $args) {
        if(!$args['is_show_on_comment']){
            return '';
        }
        $html = '<div class="wpd-custom-field wpd-cf-rating">';
        $html .='<div class="wpd-cf-label">' . $args['name'] . ' : </div><div class="wpd-cf-value">';
        for ($i = 0; $i < 5; $i++) {
            $colorClass = ($i < $value) ? ' wcf-activ-star ' : ' wcf-pasiv-star ';
            $html .= '<i class="fa '.$args['icon'].' ' . $colorClass . '" aria-hidden="true"></i>&nbsp;';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function validateFieldData($fieldName, $args, $options, $currentUser) {
        $value = filter_input(INPUT_POST, $fieldName, FILTER_SANITIZE_NUMBER_INT);
        if (!$this->isCommentParentZero()) {
            return 0;
        }
        if (!$value && $args['required']) {
            wp_die(__($args['name'], 'wpdiscuz') . ' : ' . __('field is required!', 'wpdiscuz'));
        }
        return $value;
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => '',
            'desc' => '',
            'required' => '0',
            'loc' => 'top',
            'icon' => 'fa-star',
            'is_show_on_comment' => 1
        );
    }

}
