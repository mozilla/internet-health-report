<?php 
if (!defined('ABSPATH')) {
    exit();
}
?>
<div>
    <h2 style="padding:5px 10px 10px 10px; margin:0px;"><?php _e('Date/Time Phrases', 'wpdiscuz'); ?></h2>
    <table class="wp-list-table widefat plugins"  style="margin-top:10px; border:none;">
        <tbody>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Year', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_year_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_year_text']['datetime'][0]; ?>" name="wc_year_text" id="wc_year_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Years (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_year_text_plural">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_year_text_plural']['datetime'][0]) ? $this->optionsSerialized->phrases['wc_year_text_plural']['datetime'][0] : __('Years', 'wpdiscuz'); ?>" name="wc_year_text_plural" id="wc_year_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Month', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_month_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_month_text']['datetime'][0]; ?>" name="wc_month_text" id="wc_month_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Months (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_month_text_plural">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_month_text_plural']['datetime'][0]; ?>" name="wc_month_text_plural" id="wc_month_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Day', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_day_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_day_text']['datetime'][0]; ?>" name="wc_day_text" id="wc_day_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Days (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_day_text_plural">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_day_text_plural']['datetime'][0]; ?>" name="wc_day_text_plural" id="wc_day_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Hour', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_hour_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_hour_text']['datetime'][0]; ?>" name="wc_hour_text" id="wc_hour_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Hours (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_hour_text_plural">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_hour_text_plural']['datetime'][0]; ?>" name="wc_hour_text_plural" id="wc_hour_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Minute', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_minute_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_minute_text']['datetime'][0]; ?>" name="wc_minute_text" id="wc_minute_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Minutes (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_minute_text_plural">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_minute_text_plural']['datetime'][0]; ?>" name="wc_minute_text_plural" id="wc_minute_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Second', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_second_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_second_text']['datetime'][0]; ?>" name="wc_second_text" id="wc_second_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Seconds (Plural Form)', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_second_text_plural">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_second_text_plural']['datetime'][0]; ?>" name="wc_second_text_plural" id="wc_second_text_plural" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Commented "right now" text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_right_now_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_right_now_text']; ?>" name="wc_right_now_text" id="wc_right_now_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Ago text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_ago_text">
                        <input type="text" value="<?php echo $this->optionsSerialized->phrases['wc_ago_text']; ?>" name="wc_ago_text" id="wc_ago_text" />
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('"Today" text', 'wpdiscuz'); ?>
                </th>
                <td colspan="3">                                
                    <label for="wc_posted_today_text">
                        <input type="text" value="<?php echo isset($this->optionsSerialized->phrases['wc_posted_today_text']) ? $this->optionsSerialized->phrases['wc_posted_today_text'] : __('Today', 'wpdiscuz'); ?>" name="wc_posted_today_text" id="wc_posted_today_text" placeholder="<?php _e('Today', 'wpdiscuz'); ?> 9:26 PM"/>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>