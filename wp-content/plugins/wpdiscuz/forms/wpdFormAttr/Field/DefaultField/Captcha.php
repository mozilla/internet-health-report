<?php

namespace wpdFormAttr\Field\DefaultField;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\Field;

class Captcha extends Field {

    private $captchaDir;
    private $filesPath;
    private $msgImgCreateError;
    private $msgPermsDeniedError;
    private $msgGDLibraryDisabled;
    private $msgPNGCreationDisabled;
    private $captchaString;
    protected $name = wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD;
    protected $isDefault = true;

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
            <div class="wpd-field-option">
                <label><?php _e('Show for guests', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['show_for_guests'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[show_for_guests]" />
            </div>
            <div class="wpd-field-option">
                <label><?php _e('Show for logged in users', 'wpdiscuz'); ?>:</label> 
                <input type="checkbox" value="1" <?php checked($this->fieldData['show_for_users'], 1, true); ?> name="<?php echo $this->fieldInputName; ?>[show_for_users]" />
            </div>
            <div style="clear:both;"></div>
        </div>
        <?php
    }

    public function frontFormHtml($name, $args, $options, $currentUser, $uniqueId,$isMainForm) {
        if ($options->isGoodbyeCaptchaActive) {
            echo $options->goodbyeCaptchaTocken;
        } else {
            if ($this->isShowCaptcha($currentUser->ID, $args)) {
                if (class_exists("wpDiscuzReCaptcha")) {
                    global $wpDiscuzReCaptcha;
                    $wpDiscuzReCaptcha->recaptchaHtml($uniqueId);
                } else {
                    $this->generateCaptchaHtml($args,$options);
                }
            }
        }
    }

    public function sanitizeFieldData($data) {
        $cleanData = array();
        $cleanData['type'] = $data['type'];
        if (isset($data['name'])) {
            $name = trim(strip_tags($data['name']));
            $cleanData['name'] = $name ? $name : $this->fieldDefaultData['name'];
        }
        if (isset($data['desc'])) {
            $cleanData['desc'] = trim(strip_tags($data['desc']));
        }
        if (isset($data['show_for_guests'])) {
            $cleanData['show_for_guests'] = intval($data['show_for_guests']);
        }
        if (isset($data['show_for_users'])) {
            $cleanData['show_for_users'] = intval($data['show_for_users']);
        }
        return wp_parse_args($cleanData, $this->fieldDefaultData);
    }

    public function validateFieldData($fieldName,$args, $options, $currentUser) {
        if ($currentUser && $this->isShowCaptcha($currentUser->ID,$args) && !class_exists("wpDiscuzReCaptcha") && !$options->isGoodbyeCaptchaActive) {
            $captcha = isset($_POST[$fieldName]) ? trim($_POST[$fieldName]) : '';
            if ($options->isCaptchaInSession) {
                if (!session_id()) {
                    session_start();
                }
                $cnonce = isset($_POST['cnonce']) ? trim($_POST['cnonce']) : '';
                $sCaptcha = isset($_SESSION['wpdiscuzc'][$cnonce]) ? $_SESSION['wpdiscuzc'][$cnonce] : false;
                if (!$sCaptcha || md5(strtolower($captcha)) !== $sCaptcha) {
                    $messageArray['code'] = 'wc_invalid_captcha';
                    wp_die(json_encode($messageArray));
                }
            } else {
                $key = isset($_POST['cnonce']) ? substr(trim($_POST['cnonce']), wpdFormConst::CAPTCHA_LENGTH) : '';
                $fileName = isset($_POST['fileName']) ? substr(trim($_POST['fileName']), 0, strlen(trim($_POST['fileName'])) - 4) : '';
                if (!($this->checkCaptchaFile($key, $fileName, $captcha))) {
                    $messageArray['code'] = 'wc_invalid_captcha';
                    wp_die(json_encode($messageArray));
                }
            }
        }
    }

    protected function initDefaultData() {
        $this->fieldDefaultData = array(
            'name' => __('Code', 'wpdiscuz'),
            'desc' => '',
            'show_for_guests' => '0',
            'show_for_users' => '0'
        );
        $this->captchaDir = WPDISCUZ_DIR_PATH . WPDISCUZ_DS . 'utils' . WPDISCUZ_DS . 'temp';
        $this->filesPath = WPDISCUZ_DIR_PATH . WPDISCUZ_DS . 'utils' . WPDISCUZ_DS . 'captcha' . WPDISCUZ_DS;
        $this->msgImgCreateError = __('Cannot create image file', 'wpdiscuz');
        $this->msgPermsDeniedError = __('Permission denied for file creation', 'wpdiscuz');
        $this->msgGDLibraryDisabled = __('PHP GD2 library is disabled', 'wpdiscuz');
        $this->msgPNGCreationDisabled = __('PNG image creation disabled', 'wpdiscuz');
    }

    private function generateCaptchaHtml($args,$options) {
        ?>
        <div class="wc-field-captcha wpdiscuz-item">
            <div class="wc-captcha-input">
                <input type="text" maxlength="5" value="" autocomplete="off" required="required" name="wc_captcha"  class="wpd-field wc_field_captcha" placeholder="<?php echo $args['name']; ?>" title="Insert the CAPTCHA code">
            </div>
            <div class="wc-label wc-captcha-label">
                <?php
                if ($options->isCaptchaInSession) {
                    $key = uniqid('c');
                    $message = 'src="' . plugins_url(WPDISCUZ_DIR_NAME . WPDISCUZ_DS . 'utils' . WPDISCUZ_DS . 'captcha' . WPDISCUZ_DS . 'captcha.php?key=' . $key) . '"';
                } else {
                    $cData = $this->createCaptchaImage();
                    $key = $cData['key'];
                    $message = $cData['code'] ? 'src="' . plugins_url(WPDISCUZ_DIR_NAME . WPDISCUZ_DS . 'utils' . WPDISCUZ_DS . 'temp' . WPDISCUZ_DS . $cData['message']) . '"' : 'alt="' . $cData['message'] . '"';
                }
                ?>
                <a class="wpdiscuz-nofollow" href="#" rel="nofollow"><img alt="wpdiscuz_captcha" class="wc_captcha_img" <?php echo $message; ?>  width="80" height="26"/></a><a class="wpdiscuz-nofollow wc_captcha_refresh_img" href="#" rel="nofollow"><img  alt="refresh" class="" src="<?php echo plugins_url(WPDISCUZ_DIR_NAME . WPDISCUZ_DS . 'assets' . WPDISCUZ_DS . 'img' . WPDISCUZ_DS . 'captcha-loading.png'); ?>" width="16" height="16"/></a>
                <input type="hidden" id="<?php echo $key; ?>" class="wpdiscuz-cnonce" name="cnonce" value="<?php echo $key; ?>" />
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
    }

    private function createCaptchaImage() {
        $dirExists = $this->removeOldFiles();
        $captchaData = array();

        if (!$dirExists) {
            $captchaData['code'] = 0;
            $captchaData['key'] = '';
            $captchaData['message'] = $this->msgImgCreateError;
            return $captchaData;
        }

        if (!(@is_writable($this->captchaDir))) {
            $captchaData['code'] = 0;
            $captchaData['key'] = '';
            $captchaData['message'] = $this->msgPermsDeniedError;
            return $captchaData;
        }

        if (!function_exists('imagecreatefrompng')) {
            $captchaData['code'] = 0;
            $captchaData['key'] = '';
            $captchaData['message'] = $this->msgGDLibraryDisabled;
            return $captchaData;
        }

        if (($im = @imagecreatefrompng($this->filesPath . 'captcha_bg_easy.png')) === false) {
            $captchaData['code'] = 0;
            $captchaData['key'] = '';
            $captchaData['message'] = $this->msgImgCreateError;
            return $captchaData;
        }
        $t = str_replace('.', '', $this->getmicrotime());
        $chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        $randomString = '';
        $prefix = '';
        for ($i = 0; $i < wpdFormConst::CAPTCHA_LENGTH; $i++) {
            $randomString .= $chars[rand(0, strlen($chars) - 1)];
            $prefix .= $chars[rand(0, strlen($chars) - 1)];
        }
        $this->captchaString = $randomString;

        $size = 16;
        $angle = 0;
        $x = 5;
        $y = 20;
        $font = $this->filesPath . 'consolai.ttf';
        for ($i = 0; $i < strlen($randomString); $i++) {
            $color = imagecolorallocate($im, rand(0, 255), 0, rand(0, 255));
            $letter = substr($randomString, $i, 1);
            imagettftext($im, $size, $angle, $x, $y, $color, $font, $letter);
            $x += 13;
        }

        for ($i = 0; $i < 5; $i++) {
            $color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
            imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
        }
        $fileName = $prefix . '-' . $t . '.png';
        $filePath = $this->captchaDir . WPDISCUZ_DS . $fileName;
        if (imagepng($im, $filePath, 5)) {
            $captchaData['code'] = 1;
            $captchaData['message'] = $fileName;
        } else {
            $captchaData['code'] = 0;
            $captchaData['key'] = '';
            $captchaData['message'] = $this->msgPNGCreationDisabled;
            return $captchaData;
        }
        imagedestroy($im);
        $key = $this->createAnswer($prefix, $t);
        $captchaData['key'] = $prefix . $key;
        return $captchaData;
    }

    public function removeOldFiles($minutes = 30, $deactivate = false) {
        $minutes = apply_filters('wpdiscuz_captcha_expired', $minutes);
        if ($this->captchaDir && file_exists($this->captchaDir) && floatval($minutes)) {
            $files = function_exists('scandir') ? scandir($this->captchaDir) : false;
            if ($files && is_array($files)) {
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..' && $file != '.htaccess') {
                        $fileName = $this->captchaDir . WPDISCUZ_DS . $file;
                        $mTime = substr($file, wpdFormConst::CAPTCHA_LENGTH + 1, 10);
                        if (file_exists($fileName) && is_file($fileName) && $mTime) {
                            $expired = $mTime + ($minutes * 60);
                            if ($expired < time() || $deactivate) {
                                @unlink($fileName);
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function removeAllFiles() {
        $this->removeOldFiles(true);
    }

    public function getmicrotime() {
        list($pfx_usec, $pfx_sec) = explode(" ", microtime());
        return ((float) $pfx_usec + (float) $pfx_sec);
    }

    public function generateCaptcha() {
        $messageArray = array();
        if (isset($_POST['wpdiscuz_unique_id'])) {
            $cData = $this->createCaptchaImage();
            $messageArray['code'] = $cData['code'];
            $messageArray['key'] = $cData['key'];
            $messageArray['message'] = $cData['message'];
            wp_die(json_encode($messageArray));
        }
    }

    private function createAnswer($prefix, $t) {
        $key = '';
        $dir = trailingslashit($this->captchaDir);
        $answerFileName = $prefix . '-' . $t . '.jpg';
        $answerFile = $dir . WPDISCUZ_DS . $answerFileName;

        if ($out = @fopen($answerFile, 'w')) {
            $loweredString = strtolower($this->captchaString);
            $key = hash_hmac('sha256', $loweredString, time() . '');
            $hash = hash_hmac('sha256', $loweredString, $key);
            fwrite($out, $key . '=' . $hash);
            fclose($out);
        }
        return $key;
    }

    public function checkCaptchaFile($key, $fileName, $captcha) {
        if (!$key || !$fileName || !$captcha) {
            return false;
        }
        $captchaLower = strtolower($captcha);
        $file = $fileName . '.jpg';
        $filePath = $this->captchaDir . WPDISCUZ_DS . $file;
        $parts = explode('=', file_get_contents($filePath));
        $tKey = $parts[0];
        $tAnswer = $parts[1];
        return is_readable($filePath) && $tKey == $key && $tAnswer == hash_hmac('sha256', $captchaLower, $key);
    }

    /**
     * check if the captcha field show or not
     * @return type boolean 
     */
    public function isShowCaptcha($isUserLoggedIn, $args) {
        return ($isUserLoggedIn && $args['show_for_users']) || (!$isUserLoggedIn && $args['show_for_guests']);
    }
    
    public function editCommentHtml($key, $value ,$data,$comment) {}
    
    public function frontHtml($value,$args) {}

}
