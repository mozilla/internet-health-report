<?php

namespace wpdFormAttr;

use wpdFormAttr\FormConst\wpdFormConst;

class Row {

    public function dashboardForm($id, $args) {
        $defaultArgs = array(
            'column_type' => 'full',
            'row_order' => 0
        );
        $data = wp_parse_args($args, $defaultArgs);
        $columnType = $data['column_type'];
        $rowOrder = $data['row_order'];
        ?>
        <div class="wpd-form-row-wrap" id="<?php echo $id; ?>">
            <input type="hidden" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE; ?>[<?php echo $id; ?>][column_type]" class="column_type" value="<?php echo $columnType; ?>"  />
            <input type="hidden" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE; ?>[<?php echo $id; ?>][row_order]" class="row_order" value="<?php echo $rowOrder; ?>" />
            <div class="wpd-form-row-head">
                <div class="wpd-form-row-actions">
                    <i title="<?php _e('Two column', 'wpdiscuz'); ?>" class="fa fa-columns wpd-form-columns-<?php echo $columnType; ?>"></i>
                    |<i class="fa fa-trash" title="<?php _e('Delete', 'wpdiscuz'); ?>"></i>
                    |<i class="fa fa-arrows" aria-hidden="true" title="<?php _e('Move', 'wpdiscuz'); ?>"></i>
                </div>
            </div>
            <div class="wpd-form-row">
                <?php $this->renderRow($id, $data); ?>
            </div>
        </div>
        <?php
    }

    private function renderRow($id, $args) {
        $isTwoCol = $args['column_type'] == 'two' ? true : false;
        ?>
        <div class="wpd-form-row-body <?php echo $isTwoCol ? 'two-col' : ''; ?>">
            <?php
            if ($isTwoCol) {
                $leftData = isset($args['left']) ? $args['left'] : array();
                $rightData = isset($args['right']) ? $args['right'] : array();
                $this->renderCol($id, 'left', $leftData);
                $this->renderCol($id, 'right', $rightData);
            } else {
                $fullData = $args['full'];
                $this->renderCol($id, 'full', $fullData);
            }
            ?>
        </div>
        <?php
    }

    private function renderCol($id, $colName, $fields) {
        ?>
        <div class="wpd-form-col <?php echo $colName; ?>-col">
            <div class="col-body">
                <?php
                if ($fields) {
                    foreach ($fields as $name => $fieldData) {
                        $fieldType = $fieldData['type'];
                        $field = call_user_func($fieldType . '::getInstance');
                        $field->dashboardFormHtml($id, $colName, $name, $fieldData);
                    }
                }
                ?>
            </div>
            <div class="wpd-form-add-filed">
                <i title="<?php _e('Add Field', 'wpdiscuz'); ?>" class="fa fa-plus"></i>
            </div>
        </div>
        <?php
    }

    public function renderFrontFormRow($args,$options,$currentUser,$uniqueId,$isMainForm) {
        ?>
        <div class="wpd-form-row">
            <?php
            if ($args['column_type'] == 'two') {
                $left = $args['left'];
                $right = $args['right'];
                $this->renderFrontFormCol('left', $left,$options,$currentUser,$uniqueId,$isMainForm);
                $this->renderFrontFormCol('right', $right, $options,$currentUser,$uniqueId,$isMainForm);
            } else {
                $full = $args['full'];
                $this->renderFrontFormCol('full', $full,$options,$currentUser,$uniqueId,$isMainForm);
            }
            ?>
            <div class="clearfix"></div>
        </div>
        <?php
    }
    
    private function renderFrontFormCol($colName, $fields,$options,$currentUser,$uniqueId,$isMainForm) {
        ?>
        <div class="wpd-form-col-<?php echo $colName; ?>">
            <?php
            foreach ($fields as $fieldName => $fieldData) {
                $fieldType = $fieldData['type'];
                $field = call_user_func($fieldType . '::getInstance');
                $field->frontFormHtml($fieldName,$fieldData,$options,$currentUser,$uniqueId,$isMainForm);
            }
            ?>
        </div>
        <?php
    }

    public function sanitizeRowData($data, &$fields) {
        if (isset($data['full'])) {
            $data['full'] = is_array($data['full']) ? $data['full'] : array();
            $data['full'] = $this->callFieldSanitize($data['full'], $fields);
            $data['column_type'] = 'full';
        } else if(isset($data['left']) || isset($data['right'])){
            $data['left'] = is_array($data['left']) ? $data['left'] : array();
            $data['right'] = is_array($data['right']) ? $data['right'] : array();
            $data['left'] = $this->callFieldSanitize($data['left'], $fields);
            $data['right'] = $this->callFieldSanitize($data['right'], $fields);
            $data['column_type'] = 'two';
        }else{
            return null;
        }
        if (isset($data['row_order'])) {
            $data['row_order'] = intval($data['row_order']);
        } else {
            $data['row_order'] = '0';
        }
        return $data;
    }

    private function callFieldSanitize($args, &$fields) {
        foreach ($args as $fieldName => $fieldData) {
            if (!isset($fieldData['type']) && !$fieldData['type']) {
                continue;
            }
            $callableClass = str_replace('\\\\','\\',$fieldData['type']);
            if (is_callable($callableClass . '::getInstance')) {
                $field = call_user_func($callableClass . '::getInstance');
                $args[$fieldName] = $field->sanitizeFieldData($fieldData);
                $fields[$fieldName] = $field->sanitizeFieldData($fieldData);
            }
        }
        return $args;
    }
}
