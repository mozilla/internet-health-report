<?php
if (!defined('ABSPATH')) {
    exit();
}
?>
<?php $this->refreshAddonPage(); $this->dismissAddonNoteOnPage(); ?>
<div class="wrap wpdiscuz_addons_page">
    <div style="float:left; width:50px; height:55px; margin:10px 10px 20px 0px;">
        <img src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/plugin-icon/plugin-icon-48.png'); ?>" style="border:2px solid #fff;"/>
    </div>
    <h1 style="padding-bottom:20px; padding-top:15px;"><?php _e('wpDiscuz Addons', 'wpdiscuz'); ?></h1>
    <br style="clear:both" />
    <table width="100%" border="0" cellspacing="1" class="widefat">
        <tr>
            <td valign="top" style="padding:10px 10px 0px 10px;">
                <table width="100%" border="0" cellspacing="1">
                    <thead>
                        <tr>
                            <th style="font-size:16px; padding-bottom:15px;"><strong><?php _e('wpDiscuz Add-ons','wpdiscuz');?></strong></th>
                            <th style="font-size:16px; padding-bottom:15px; width:205px; text-align:center; border-bottom:1px solid #008EC2;"><a href="http://gvectors.com/forum/" style="color:#008EC2; overflow:hidden; outline:none;" target="_blank"><?php _e('Addons Support Forum','wpdiscuz');?></a></th>
                        </tr>
                    </thead>
                    <tr valign="top">
                        <td colspan="2" style="background:#FFF; text-align:left; font-size:13px;">
                            <?php _e('All wpDiscuz addons are being developed by wpDiscuz developers at gVectors Team, those are very solid premium level extensions and come with premium support.
                            These addons help us to keep top level development of the free wpDiscuz plugin. All essential and even dozens of extra-cool features are already available for free in core wpDiscuz. 
                            There will never be any limits, any pro and paid versions for wpDiscuz. We have another dozens of awesome features in our to-do list which will also be added for free in future releases. 
                            So the free wpDiscuz development always stays on the first priority and wpDiscuz is being extended with new free functions and features even faster than before.','wpdiscuz');?>
                            <br />
                            <p style="font-size:10px; color:#B1B1B1; font-style:italic; text-align:right; line-height:12px; padding-top:6px; margin:0px;">
                               <?php _e(' Thank you!<br/> Sincerely yours,<br/> gVectors Team&nbsp;','wpdiscuz');?>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br style="clear:both" />

    <div class="wpdiscuz-addons-wrapper">
        <?php
        foreach ($this->addons as $key => $addon) {
            $installed = (class_exists($addon['class'])) ? true : false;
            ?>
            <div class="wpdiscuz-addon-block">
                <div id="wpdiscuz-addon-<?php echo $key ?>" class="addon-thumb" style="background:url(<?php echo ($installed) ? str_replace('.png', '-off.png', $addon['thumb']) : $addon['thumb']; ?>) top center no-repeat;">
                    &nbsp;
                </div>
                <div class="contenthover">
                    <div class="addon-isactive">
                        <?php if ($installed) { ?>
                            <div class="note-installed"><?php _e('Installed', 'default'); ?></div>
                        <?php } else { ?>
                            <h3 style="font-weight:normal; font-size:22px; line-height: 25px; margin-bottom:2px; text-shadow: 0 0 2px #999;"><?php echo $addon['title'] ?></h3>
                            <ul>
                                <li style="line-height:16px;"><?php _e('Version', 'default'); ?>: <?php echo $addon['version']; ?></li> 
                                <li style="line-height:16px;">wpDiscuz: <?php _e('at least', 'default'); ?> <?php echo $addon['requires']; ?></li> 
                            </ul>
                            <a class="button button-primary addon-button" href="<?php echo $addon['url']; ?>" target="_blank" style="font-size:14px;"><?php echo __('Live Preview | Buy', 'wpdiscuz'); ?></a>
                        <?php } ?>
                    </div>
                </div>
                <div style="clear:both"></div>
                <div class="addon-info" style="<?php if ($installed) echo 'background-color:#bbbbbb'; ?>">
                    <a href="<?php echo $addon['url']; ?>" target="_blank" title="<?php _e('More information about', 'default'); ?> <?php echo $addon['title'] ?> add-on &raquo;">
                        <p class="addon-title"><?php echo $addon['title']; ?></p>
                    </a>
                    <div class="addon-desc"><?php echo $addon['desc']; ?></div>
                </div>
            </div>
            <script language="javascript">jQuery(document).ready(function ($) { $('#wpdiscuz-addon-<?php echo $key ?>').contenthover({ overlay_width:230, overlay_height:<?php echo ($installed) ? '100' : '180'; ?>, effect:'slide', slide_direction:'right', overlay_x_position:'right', overlay_y_position:'center', overlay_background:'#e5e5e5', overlay_opacity:0.9}); });</script>
        <?php } ?>
    </div>
    <div style="clear:both;"></div>
    <h3>&nbsp;</h3>
    <hr />

</div>