<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Import options', 'wpdiscuz'); ?></h2>
    <p style="font-size:13px; color:#999999; width:90%; padding-left:0px; margin-left:10px;">
    	<?php _e('You can transfer the saved options data between different installs by copying the text inside this textarea in "Export Options" Tab. To import data from another install, just put the data in textarea with the one from another install and click "Save Changes". Make sure you use the same wpDiscuz versions.', 'wpdiscuz'); ?> 
    </p>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr>
                <td>
                    <textarea class="wpdiscuz_import_options" name="wpdiscuz_import_options" rows="15"></textarea>
                </td>
            </tr>
        </tbody>
    </table>
</div>