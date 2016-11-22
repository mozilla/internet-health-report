<?php
require_once 'default-fields.php';

$wpdiscuzCustomFields = apply_filters('wpdiscuz_form_custom_fields', array());
?>
<div class="wpdiscz-default-fields">
    <h3 class="wpdiscuz-tb-title"><?php _e('Comment Form Fields', 'wpdiscuz'); ?></h3>
    <?php
    foreach ($wpdiscuzDefaultFields['html'] as $class => $title) {
        ?>
        <button id="<?php echo $class; ?>" class="wpd-field-button button wpdDefaultField"><?php echo $title; ?></button>
        <?php
    }
    ?>
</div>
<div class="wpdiscz-custom-fields">
    <?php if ($wpdiscuzCustomFields) { ?>
        <h3 class="wpdiscuz-tb-title"><?php _e('Custom Fields', 'wpdiscuz'); ?></h3>
        <?php
        foreach ($wpdiscuzCustomFields as $wpdiscuzCustomField) {
            ?>
            <a href="<?php echo admin_url('admin-ajax.php?action=getCustomFieldHtml&fieldType=' . $wpdiscuzCustomField['type'] . '&width=700&height=400'); ?>"  class="button thickbox" title="<?php echo $wpdiscuzCustomField['title']; ?>"><?php echo $wpdiscuzCustomField['title']; ?></a>
            <?php
        }
    }
    ?>
</div>