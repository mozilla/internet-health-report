<?php

class WpdiscuzHelper {

    public static $datetime = 'datetime';
    public static $year = 'wc_year_text';
    public static $years = 'wc_year_text_plural';
    public static $month = 'wc_month_text';
    public static $months = 'wc_month_text_plural';
    public static $day = 'wc_day_text';
    public static $days = 'wc_day_text_plural';
    public static $hour = 'wc_hour_text';
    public static $hours = 'wc_hour_text_plural';
    public static $minute = 'wc_minute_text';
    public static $minutes = 'wc_minute_text_plural';
    public static $second = 'wc_second_text';
    public static $seconds = 'wc_second_text_plural';
    private $optionsSerialized;
    private $dbManager;
    private $wpdiscuzForm;
    public $wc_allowed_tags = array(
        'br' => array(),
        'a' => array('href' => array(), 'title' => array(), 'target' => array(), 'rel' => array(), 'download' => array(), 'hreflang' => array(), 'media' => array(), 'type' => array(), 'class'=>array()),
        'i' => array(),
        'b' => array(),
        'u' => array(),
        'strong' => array(),
        's' => array(),
        'p' => array('class' => array()),
        'img' => array('src' => array(), 'width' => array(), 'height' => array(), 'alt' => array() , 'title' => array()),
        'blockquote' => array('cite' => array()),
        'ul' => array(),
        'li' => array(),
        'ol' => array(),
        'code' => array(),
        'em' => array(),
        'abbr' => array('title' => array()),
        'q' => array('cite' => array()),
        'acronym' => array('title' => array()),
        'cite' => array(),
        'strike' => array(),
        'del' => array('datetime' => array()),
        'span' => array('id' => array(), 'class' => array(), 'title' => array()),
        'pre' => array(),
    );

    function __construct($optionsSerialized, $dbManager, $wpdiscuzForm) {
        $this->optionsSerialized = $optionsSerialized;
        $this->dbManager = $dbManager;
        $this->wpdiscuzForm = $wpdiscuzForm;
    }

// Set timezone
// Time format is UNIX timestamp or
// PHP strtotime compatible strings
    public function dateDiff($time1, $time2, $precision = 2) {

// If not numeric then convert texts to unix timestamps
        if (!is_int($time1)) {
            $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
            $time2 = strtotime($time2);
        }

// If time1 is bigger than time2
// Then swap time1 and time2
        if ($time1 > $time2) {
            $ttime = $time1;
            $time1 = $time2;
            $time2 = $ttime;
        }

// Set up intervals and diffs arrays
        $intervals = array(
            $this->optionsSerialized->phrases['wc_year_text']['datetime'][1],
            $this->optionsSerialized->phrases['wc_month_text']['datetime'][1],
            $this->optionsSerialized->phrases['wc_day_text']['datetime'][1],
            $this->optionsSerialized->phrases['wc_hour_text']['datetime'][1],
            $this->optionsSerialized->phrases['wc_minute_text']['datetime'][1],
            $this->optionsSerialized->phrases['wc_second_text']['datetime'][1]
        );
        $diffs = array();
// Loop thru all intervals
        foreach ($intervals as $interval) {
// Create temp time from time1 and interval
            $interval = $this->dateComparisionByIndex($interval);
            $ttime = strtotime('+1 ' . $interval, $time1);
// Set initial values
            $add = 1;
            $looped = 0;
// Loop until temp time is smaller than time2
            while ($time2 >= $ttime) {
// Create new temp time from time1 and interval
                $add++;
                $ttime = strtotime("+" . $add . " " . $interval, $time1);
                $looped++;
            }

            $time1 = strtotime("+" . $looped . " " . $interval, $time1);
            $diffs[$interval] = $looped;
        }

        $count = 0;
        $times = array();
// Loop thru all diffs
        foreach ($diffs as $interval => $value) {
            $interval = $this->dateTextByIndex($interval, $value);
// Break if we have needed precission
            if ($count >= $precision) {
                break;
            }
// Add value and interval
// if value is bigger than 0
            if ($value > 0) {
// Add value and interval to times array
                $times[] = $value . " " . $interval;
                $count++;
            }
        }

// Return string with times
        $ago = ($times) ? $this->optionsSerialized->phrases['wc_ago_text'] : $this->optionsSerialized->phrases['wc_right_now_text'];
        return implode(" ", $times) . ' ' . $ago;
    }

    public static function initPhraseKeyValue($phrase) {
        $phrase_value = stripslashes($phrase['phrase_value']);
        switch ($phrase['phrase_key']) {
            case WpdiscuzHelper::$year:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 1));
            case WpdiscuzHelper::$years:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 1));
            case WpdiscuzHelper::$month:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 2));
            case WpdiscuzHelper::$months:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 2));
            case WpdiscuzHelper::$day:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 3));
            case WpdiscuzHelper::$days:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 3));
            case WpdiscuzHelper::$hour:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 4));
            case WpdiscuzHelper::$hours:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 4));
            case WpdiscuzHelper::$minute:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 5));
            case WpdiscuzHelper::$minutes:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 5));
            case WpdiscuzHelper::$second:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 6));
            case WpdiscuzHelper::$seconds:
                return array(WpdiscuzHelper::$datetime => array($phrase_value, 6));
            default :
                return $phrase_value;
        }
    }

    private function dateComparisionByIndex($index) {
        switch ($index) {
            case 1:
                return 'year';
            case 2:
                return 'month';
            case 3:
                return 'day';
            case 4:
                return 'hour';
            case 5:
                return 'minute';
            case 6:
                return 'second';
        }
    }

    private function dateTextByIndex($index, $value) {
        switch ($index) {
            case 'year':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_year_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_year_text']['datetime'][0];
            case 'month':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_month_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_month_text']['datetime'][0];
            case 'day':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_day_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_day_text']['datetime'][0];
            case 'hour':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_hour_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_hour_text']['datetime'][0];
            case 'minute':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_minute_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_minute_text']['datetime'][0];
            case 'second':
                return ($value > 1) ? $this->optionsSerialized->phrases['wc_second_text_plural']['datetime'][0] : $this->optionsSerialized->phrases['wc_second_text']['datetime'][0];
        }
    }

    public static function getArray($array) {
        $new_array = array();
        foreach ($array as $value) {
            $new_array[] = $value[0];
        }
        return $new_array;
    }

    public function makeClickable($ret) {
        $ret = ' ' . $ret;
        $ret = preg_replace('#[^\"|\'](https?:\/\/[^\s]+(\.jpe?g|\.png|\.gif|\.bmp))#i', '<a href="$1"><img alt="comment image" src="$1" /></a>', $ret);
        // this one is not in an array because we need it to run last, for cleanup of accidental links within links
        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        $ret = trim($ret);
        return $ret;
    }

    /**
     * check if comment has been posted today or not
     * return boolean
     */
    public static function isPostedToday($comment) {
        return date('Ymd', strtotime(current_time('Ymd'))) <= date('Ymd', strtotime($comment->comment_date));
    }

    /**
     * check if comment is still editable or not
     * return boolean
     */
    public function isCommentEditable($comment) {
        $editableTimeLimit = isset($this->optionsSerialized->commentEditableTime) ? $this->optionsSerialized->commentEditableTime : 0;
        $timeDiff = (time() - strtotime($comment->comment_date_gmt));
        $editableTimeLimit = ($editableTimeLimit == 'unlimit') ? $timeDiff + 1 : intval($editableTimeLimit);
        return $editableTimeLimit && ($timeDiff < $editableTimeLimit);
    }

    /**
     * checks if the current comment content is in min/max range defined in options
     */
    public function isContentInRange($commentContent) {
        $commentMinLength = intval($this->optionsSerialized->commentTextMinLength);
        $commentMaxLength = intval($this->optionsSerialized->commentTextMaxLength);
        $commentContent = trim(strip_tags($commentContent));
        $contentLength = function_exists('mb_strlen') ? mb_strlen($commentContent) : strlen($commentContent);
        return ($commentMinLength && $contentLength >= $commentMinLength) && ($commentMaxLength == 0 || $contentLength <= $commentMaxLength);
    }

    /**
     * return client real ip
     */
    public function getRealIPAddr() {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function getUIDData($uid) {
        $id_strings = explode('_', $uid);
        return $id_strings;
    }

    public function isShowLoadMore($parentId, $args) {
        $postId = $args['post_id'];
        $postAllParent = $this->dbManager->getAllParentCommentCount($postId, $this->optionsSerialized->wordpressThreadComments);
        $showLoadeMore = false;
        if ($postAllParent) {
            if ($args['orderby'] == 'comment_date_gmt') {
                if ($args['order'] == 'desc' && $parentId) {
                    $minId = min($postAllParent);
                    $showLoadeMore = $minId < $parentId;
                } else {
                    $maxId = max($postAllParent);
                    $showLoadeMore = $maxId > $parentId;
                }
                $showLoadeMore = $showLoadeMore && $this->optionsSerialized->wordpressCommentPerPage && (count($postAllParent) > $this->optionsSerialized->wordpressCommentPerPage);
            } else {
                if ($this->optionsSerialized->commentListLoadType == 1 && $args['limit'] == 0) {
                    $showLoadeMore = false;
                } else {
                    $showLoadeMore = $args['offset'] + $this->optionsSerialized->wordpressCommentPerPage < count($postAllParent);
                }
            }
        }
        return $showLoadeMore;
    }

    public function superSocializerFix() {
        if (function_exists('the_champ_login_button')) {
            ?>
            <div id="comments" style="width: 0;height: 0;clear: both;margin: 0;padding: 0;"></div>
            <div id="respond" class="comments-area">
            <?php } else { ?>
                <div id="comments" class="comments-area">
                    <div id="respond" style="width: 0;height: 0;clear: both;margin: 0;padding: 0;"></div>
                    <?php
                }
            }

            public function getCommentExcerpt($commentContent, $uniqueId) {
                $readMoreLink = '<span id="wpdiscuz-readmore-' . $uniqueId . '"><span class="wpdiscuz-hellip">&hellip;&nbsp;</span><span class="wpdiscuz-readmore" title="' . $this->optionsSerialized->phrases['wc_read_more'] . '">' . $this->optionsSerialized->phrases['wc_read_more'] . '</span></span>';
                return wp_trim_words($commentContent, $this->optionsSerialized->commentReadMoreLimit, $readMoreLink);
            }


            public function isLoadWpdiscuz($post) {
                if(!$post  || !is_object($post)){
                    return false;
                }
                $form = $this->wpdiscuzForm->getForm($post->ID);
                return   $form->getFormID() && (comments_open($post) || $post->comment_count) && (is_singular() || is_front_page()) && post_type_supports($post->post_type, 'comments');
            }

            public function replaceCommentContentCode($content) {
                return preg_replace_callback('#`(.*?)`#is', array(&$this, 'replaceCodeContent'), stripslashes($content));
            }

            private function replaceCodeContent($matches) {
                if (count($matches) == 0)
                    return '';
                $codeContent = trim($matches[1]);
                $codeContent = str_replace(array('<', '>'), array('&lt;', '&gt;'), $codeContent);
                return '<code>' . $codeContent . '</code>';
            }
            
            public function getCurrentUserDisplayName($current_user){
                $displayName =  trim($current_user->display_name);
                if(!$displayName){
                    $displayName = trim($current_user->user_nicename) ? trim($current_user->user_nicename) : trim($current_user->user_login);
                }
                return $displayName;
            }
            
            public function registerWpDiscuzStyle($version){
                $templateDir = get_template_directory();
                $wpcTemplateStyleFile = $templateDir . DIRECTORY_SEPARATOR . 'wpdiscuz'. DIRECTORY_SEPARATOR .'wpdiscuz.css';
                $wpdiscuzStyleURL = plugins_url(WPDISCUZ_DIR_NAME . '/assets/css/wpdiscuz.css');
                if(file_exists($wpcTemplateStyleFile)){
                     $wpdiscuzStyleURL  = get_template_directory_uri() . '/wpdiscuz/wpdiscuz.css';
                }
                 wp_register_style('wpdiscuz-frontend-css', $wpdiscuzStyleURL , null, $version);
            }

        }