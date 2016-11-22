<?php
if (!defined('ABSPATH')) {
    exit();
}
$options = maybe_serialize(get_option(WpdiscuzCore::OPTION_SLUG_OPTIONS));
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Export options', 'wpdiscuz'); ?></h2>
    <p style="font-size:13px; color:#999999; width:90%; padding-left:0px; margin-left:10px;">
    	<?php _e('You can transfer the saved options data between different installs by copying the text inside this textarea. To import data from another install, navigate to "Import Options" Tab and put the data in textarea with the one from another install and click "Save Changes". Make sure you use the same wpDiscuz versions.', 'wpdiscuz'); ?> 
    </p>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr>
                <td>                    
                    <textarea class="wpdiscuz_export_options" name="wpdiscuz_export_options" rows="15"><?php print_r($options); ?></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>