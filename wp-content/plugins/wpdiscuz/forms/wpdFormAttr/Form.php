<?php

namespace wpdFormAttr;

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Field\DefaultField\Captcha;

class Form {

    private $wpdOptions;
    private $generalOptions;
    private $formeStructure;
    private $formPostTypes;
    private $formFields;
    private $formCustomFields;
    private $defaultsFieldsNames;
    private $formID;
    private $row;
    private $captchaFied;
    private $fieldsBeforeSave = array();
    private $ratings;
    private $ratingsExists = false;

    public function __construct($options, $formID = 0) {
        $this->defaultsFieldsNames = array(
            wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD, wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD,
            wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD, wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD,
            wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD);
        $this->wpdOptions = $options;
        $this->setFormID($formID);
        $this->row = new Row();
        $this->captchaFied = Captcha::getInstance();
    }

    public function initFormMeta() {
        if (!$this->generalOptions) {
            $this->generalOptions = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
            $this->wpdOptions->guestCanComment = isset($this->generalOptions['guest_can_comment']) ? $this->generalOptions['guest_can_comment'] : 0;
        }
        if (!$this->formeStructure) {
            $this->formeStructure = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, true);
        }
        if (!$this->formPostTypes) {
            $this->formPostTypes = isset($this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES]) ? $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] : array();
        }
    }

    public function initFormFields() {
        if (!$this->formFields) {
            $this->formCustomFields = array();
            $this->formFields = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, true);
            if (is_array($this->formFields)) {
                foreach ($this->formFields as $key => $field) {
                    if (!in_array($key, $this->defaultsFieldsNames)) {
                        $this->formCustomFields[$key] = $field;
                        if (!$this->ratingsExists && $field['type'] == 'wpdFormAttr\Field\RatingField') {
                            $this->ratingsExists = true;
                        }
                    }
                }
            }
        }
    }

    public function setFormID($formID) {
        if ($formID == 0) {
            $this->formID = $formID;
            return;
        }
        $form = get_post($formID);
        if ($form && $form->post_status == 'publish' && $form->post_type == wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE) {
            $this->formID = $formID;
            do_action('wpdiscuz_form_init', $this);
        } else {
            $postRel = $this->wpdOptions->formPostRel;
            $contentRel = $this->wpdOptions->formContentTypeRel;
            foreach ($postRel as $pid => $fid) {
                if ($formID == $fid) {
                    unset($postRel[$pid]);
                }
            }
            foreach ($contentRel as $postType => $postTypeData) {
                foreach ($postTypeData as $lang => $fid) {
                    if ($formID == $fid) {
                        unset($contentRel[$postType][$lang]);
                    }
                }
            }
            update_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, $contentRel);
            update_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, $postRel);
            $this->formID = 0;
        }
    }

    public function getFormID() {
        return $this->formID;
    }

    public function getGeneralOptions() {
        return $this->generalOptions;
    }

    public function getHeaderText() {
        $this->initFormMeta();
        return $this->generalOptions['header_text'];
    }

    public function getCaptchaFied() {
        return $this->captchaFied;
    }

    public function isShowSubscriptionBar() {
        return $this->generalOptions['show_subscription_bar'];
    }

    public function getCustomCSS() {
        return get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_CSS, true);
    }

    public function getFormPostTypes() {
        return $this->formPostTypes;
    }

    public function getFormFields() {
        return $this->formFields;
    }

    public function theFormListData($column, $formID) {
        $this->setFormID($formID);
        $this->generalOptions = get_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
        switch ($column) {
            case 'form_post_types':
                $postTypes = isset($this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES]) ? $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] : '';
                echo $postTypes ? implode(', ', $this->generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES]) : '';
                break;
            case 'form_post_ids':
                echo isset($this->generalOptions['postid']) ? $this->generalOptions['postid'] : '';
                break;
            case 'form_lang':
                echo isset($this->generalOptions['lang']) ? $this->generalOptions['lang'] : '';
                break;
        }
    }

    public function saveFormData($formID) {
        $this->setFormID($formID);
        $this->initFormMeta();
        if (isset($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS])) {
            $generalOptions = $this->validateGeneralOptions($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS]);
            $this->saveFormContentTypeRel($generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES], $generalOptions['lang']);
            $this->saveFormPostRel($generalOptions['postidsArray']);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
        }
        if (isset($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE])) {
            $formeStructure = $this->validateFormStructure($_REQUEST[wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE]);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, $formeStructure);
            update_post_meta($this->formID, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, $this->formFields);
        }
    }

    public function saveCommentMeta($commentID) {
        foreach ($this->fieldsBeforeSave as $mettaKey => $data) {
            if ($this->ratingsExists && $this->formCustomFields[$mettaKey]['type'] == 'wpdFormAttr\Field\RatingField') {
                $oldCommentRating = get_comment_meta($commentID, $mettaKey, true);
                if ($oldCommentRating) {
                    $comment = get_comment($commentID);
                    $postID = $comment->comment_post_ID;
                    $postRatingMeta = get_post_meta($postID, 'wpdiscuz_rating_count', true);
                    $oldCommentRatingCount = $postRatingMeta[$mettaKey][$oldCommentRating] - 1;
                    if ($oldCommentRatingCount > 0) {
                        $postRatingMeta[$mettaKey][$oldCommentRating] = $oldCommentRatingCount;
                    } else {
                        unset($postRatingMeta[$mettaKey][$oldCommentRating]);
                    }

                    update_post_meta($postID, 'wpdiscuz_rating_count', $postRatingMeta);
                }
                $this->ratings[] = array('metakey' => $mettaKey, 'value' => $data);
            }
            update_comment_meta($commentID, $mettaKey, $data);
        }
        if ($this->ratingsExists && $this->ratings) {
            $ratingSum = 0;
            foreach ($this->ratings as $rating) {
                $ratingSum += $rating['value'];
            }
            $gRating = round($ratingSum / count($this->ratings));
            $this->saveProstRatingMeta($commentID, $gRating);
            update_comment_meta($commentID, 'rating', $gRating);
        }
    }

    private function saveProstRatingMeta($commentID, $rating) {
        $comment = get_comment($commentID);
        $postID = $comment->comment_post_ID;
        if (class_exists('WooCommerce') && get_post_type($postID) == 'product') {
            $ratingCount = get_post_meta($postID, '_wc_rating_count', true);
            $oldRatingMeta = get_comment_meta($commentID, 'rating', true);
            $oldRating = $oldRatingMeta ? $oldRating : 0;
            if (isset($ratingCount[$oldRating])) {
                $oldRatingCount = $ratingCount[$oldRating] - 1;
                if ($oldRatingCount > 0) {
                    $ratingCount[$oldRating] = $oldRatingCount;
                } else {
                    unset($ratingCount[$oldRating]);
                }
            }
            if (isset($ratingCount[$rating])) {
                $ratingCount[$rating] = $ratingCount[$rating] + 1;
            } else if ($rating) {
                $ratingCount[$rating] = 1;
            }
            $allRatingSum = 0;
            $allCount = 0;
            foreach ($ratingCount as $star => $count) {
                $allRatingSum += $star * $count;
                $allCount += $count;
            }
            $averageRating = round($allRatingSum / $allCount, 2);
            update_post_meta($postID, '_wc_average_rating', $averageRating);
            update_post_meta($postID, '_wc_rating_count', $ratingCount);
        } else {
            $wpdiscuzRatingCountMeta = get_post_meta($postID, 'wpdiscuz_rating_count', true);
            $wpdiscuzRatingCount = $wpdiscuzRatingCountMeta && is_array($wpdiscuzRatingCountMeta) ? $wpdiscuzRatingCountMeta : array();

            foreach ($this->ratings as $key => $value) {
                if (isset($wpdiscuzRatingCount[$value['metakey']][$value['value']])) {
                    $wpdiscuzRatingCount[$value['metakey']][$value['value']] = $wpdiscuzRatingCount[$value['metakey']][$value['value']] + 1;
                } else if ($value['value']) {
                    $wpdiscuzRatingCount[$value['metakey']][$value['value']] = 1;
                }
            }
            update_post_meta($postID, 'wpdiscuz_rating_count', $wpdiscuzRatingCount);
        }
    }

    public function displayRatingMeta($content) {
        global $post;
        if ($this->ratingsExists && is_singular() && !(class_exists('WooCommerce') && get_post_type($post) == 'product')) {
            $wpdiscuzRatingCountMeta = get_post_meta($post->ID, 'wpdiscuz_rating_count', true);
            $wpdiscuzRatingCount = $wpdiscuzRatingCountMeta && is_array($wpdiscuzRatingCountMeta) ? $wpdiscuzRatingCountMeta : array();
            $ratingList = array();
            foreach ($wpdiscuzRatingCount as $metaKey => $data) {
                $tempRating = 0;
                $tempRatingCount = 0;
                foreach ($data as $rating => $count) {
                    $tempRating += $rating * $count;
                    $tempRatingCount += $count;
                }

                $ratingList[$metaKey]['average'] = round($tempRating / $tempRatingCount, 2);
                $ratingList[$metaKey]['count'] = $tempRatingCount;
            }
            if ($ratingList) {
                $content .= '<div class="wpdiscuz-post-rating-wrap wpd-custom-field">';
                foreach ($ratingList as $key => $value) {
                    if (key_exists($key, $this->formCustomFields)) {
                        $icon = $this->formCustomFields[$key]['icon'];
                        $content .= '<div class="wpdiscuz-stars-label">' . $this->formCustomFields[$key]['name'] . ' (' . $value['average'] . ' / ' . $value['count'] . ') </div>';
                        $content .= '<div class="wpdiscuz-stars-wrapper">
										 <div class="wpdiscuz-stars-wrapper-inner">
										  <div class="wpdiscuz-pasiv-stars">
											<i class="fa ' . $icon . ' wcf-pasiv-star"></i>
											<i class="fa ' . $icon . ' wcf-pasiv-star"></i>
											<i class="fa ' . $icon . ' wcf-pasiv-star"></i>
											<i class="fa ' . $icon . ' wcf-pasiv-star"></i>
											<i class="fa ' . $icon . ' wcf-pasiv-star"></i>
										  </div>
										  <div class="wpdiscuz-activ-stars" style="width:' . $value['average'] * 100 / 5 . '%;">
											<i class="fa ' . $icon . ' wcf-activ-star"></i>
											<i class="fa ' . $icon . ' wcf-activ-star"></i>
											<i class="fa ' . $icon . ' wcf-activ-star"></i>
											<i class="fa ' . $icon . ' wcf-activ-star"></i>
											<i class="fa ' . $icon . ' wcf-activ-star"></i>
										  </div></div></div><div style="display:inline-block; position:relative;"></div>';
                    }
                }
                $content .= '</div>';
            }
        }
        return $content;
    }

    private function validateGeneralOptions($options) {
        $validData = array(
            'lang' => get_locale(),
            'guest_can_comment' => 1,
            'show_subscription_bar' => 1,
            'header_text' => '',
            wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES => array(),
            'postid' => '',
            'postidsArray' => array()
        );
        if (isset($options['guest_can_comment'])) {
            $validData['guest_can_comment'] = intval($options['guest_can_comment']);
        }
        if (isset($options['header_text'])) {
            $validData['header_text'] = $options['header_text'];
        }
        if (isset($options['lang'])) {
            $validData['lang'] = $options['lang'];
        }
        if (isset($options['show_subscription_bar'])) {
            $validData['show_subscription_bar'] = intval($options['show_subscription_bar']);
        }
        if (isset($options[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES])) {
            $validData[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] = $options[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES];
        }
        if (isset($options['postid'])) {
            $postIds = trim(strip_tags($options['postid']));
            if ($postIds) {
                $postIdsArray = array();
                $postIdsExplode = explode(',', $postIds);
                foreach ($postIdsExplode as $postId) {
                    $postId = intval($postId);
                    if ($postId) {
                        $postIdsArray[] = $postId;
                    }
                }
                $postIdsArray = array_unique($postIdsArray);
                sort($postIdsArray);
                $validData['postidsArray'] = $postIdsArray;
                $postIds = implode(', ', $postIdsArray);
            }
            $validData['postid'] = $postIds;
        }
        return $validData;
    }

    private function validateFormStructure($formStructure) {
        foreach ($formStructure as $rowID => $rowData) {
            $sanitizeData = $this->row->sanitizeRowData($rowData, $this->formFields);
            if ($sanitizeData) {
                $formStructure[$rowID] = $sanitizeData;
            }else{
                unset($formStructure[$rowID]);
            }
        }
        return $formStructure;
    }

    public function validateFields($currentUser) {
        foreach ($this->formCustomFields as $fieldName => $fieldArgs) {
            $fieldType = $fieldArgs['type'];
            $field = call_user_func($fieldType . '::getInstance');
            $this->fieldsBeforeSave[$fieldName] = $field->validateFieldData($fieldName, $fieldArgs, $this->wpdOptions, $currentUser);
        }
    }

    public function validateDefaultCaptcha($currentUser) {
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD];
        $this->captchaFied->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function validateDefaultEmail($currentUser, &$isAnonymous) {
        $emailField = Field\DefaultField\Email::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD];
        $email = $emailField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD, $args, $this->wpdOptions, $currentUser);
        $isAnonymous = $emailField->isAnonymous();
        return $email;
    }

    public function validateDefaultName($currentUser) {
        $nameField = Field\DefaultField\Name::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD];
        return $nameField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function validateDefaultWebsit($currentUser) {
        $webSiteField = Field\DefaultField\Website::getInstance();
        $args = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD];
        return $webSiteField->validateFieldData(wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD, $args, $this->wpdOptions, $currentUser);
    }

    public function renderFrontCommentMetaHtml($commentID, &$output) {
        $htmlExists = false;
        if ($this->formCustomFields) {
            $meta = get_comment_meta($commentID);
            $top = $this->_renderFrontCommentMetaHtml($meta, $this->formCustomFields, 'top');
            $bottom = $this->_renderFrontCommentMetaHtml($meta, $this->formCustomFields, 'bottom');
            if ($top || $bottom) {
                $htmlExists = true;
            }
            $output = '<div class="wpd-top-custom-fields">' . $top . '</div>' . $output . '<div class="wpd-bottom-custom-fields">' . $bottom . '</div>';
        }
        return $htmlExists;
    }

    private function _renderFrontCommentMetaHtml($meta, $formCustomFields, $loc) {
        $html = '';
        foreach ($formCustomFields as $key => $value) {
            if ($value['loc'] == $loc) {
                $fieldType = $value['type'];
                $field = call_user_func($fieldType . '::getInstance');
                $metaValuen = isset($meta[$key][0]) ? maybe_unserialize($meta[$key][0]) : '';
                if (!$metaValuen) {
                    continue;
                }
                $html .= $field->frontHtml($metaValuen, $value);
            }
        }
        return $html;
    }

    public function renderFrontForm($isMain, $uniqueId, $commentsCount, $currentUser) {
        if (!$isMain || $commentsCount) {
            $textarea_placeholder = $this->wpdOptions->phrases['wc_comment_join_text'];
        } else {
            $textarea_placeholder = $this->wpdOptions->phrases['wc_comment_start_text'];
        }

        $commentTextMinLength = intval($this->wpdOptions->commentTextMinLength);
        $commentTextMaxLength = intval($this->wpdOptions->commentTextMaxLength);
        $commentTextLengthRange = ($commentTextMinLength && $commentTextMaxLength) ? 'pattern=".{' . $commentTextMinLength . ',' . $commentTextMaxLength . '}"' : '';
        $textareaMaxLength = $commentTextMaxLength ? "maxlength=$commentTextMaxLength" : '';
        ?>
        <div class="wc-form-wrapper <?php echo!$isMain ? 'wc-secondary-form-wrapper' : 'wc-main-form-wrapper'; ?>"  <?php echo!$isMain ? "id='wc-secondary-form-wrapper-$uniqueId'  style='display: none;'" : "id='wc-main-form-wrapper-$uniqueId'"; ?> >
            <div class="wpdiscuz-comment-message" style="display: block;"></div>
            <?php if (!$isMain) { ?>
                <div class="wc-secondary-forms-social-content"></div>
            <?php } ?>
            <?php
            if ($this->isGuestCanComment($currentUser->ID)) {
                ?>
                <form class="wc_comm_form <?php echo!$isMain ? 'wc-secondary-form-wrapper' : 'wc_main_comm_form'; ?>" method="post" action="" enctype="multipart/form-data">
                    <div class="wc-field-comment">
                        <?php if ($this->wpdOptions->wordpressShowAvatars) { ?>
                            <?php $authorName = $currentUser->ID ? $currentUser->display_name : 'avatar'; ?>
                            <div class="wc-field-avatararea">
                                <?php echo get_avatar($currentUser->ID, 48, '', $authorName); ?>
                            </div>
                        <?php } ?>
                        <div class="wpdiscuz-item wc-field-textarea" <?php
                        if (!$this->wpdOptions->wordpressShowAvatars) {
                            echo ' style="margin-left: 0;"';
                        }
                        ?>>
                            <div class="wpdiscuz-textarea-wrap <?php if ($this->wpdOptions->isQuickTagsEnabled) echo 'wpdiscuz-quicktags-enabled'; ?>">
                                <textarea id="wc-textarea-<?php echo $uniqueId; ?>" <?php echo $commentTextLengthRange . ' ' . $textareaMaxLength; ?> placeholder="<?php echo $textarea_placeholder; ?>" required name="wc_comment" class="wc_comment wpd-field"></textarea>
                                <?php if (intval($this->wpdOptions->commentTextMaxLength)) { ?>
                                    <div class="commentTextMaxLength"><?php echo $this->wpdOptions->commentTextMaxLength; ?></div>
                                <?php } ?>
                                <?php if (defined('WPDISCUZ_BOTTOM_TOOLBAR')): ?>
                                    <div class="wpdiscuz-textarea-foot">
                                        <?php do_action('wpdiscuz_button', $uniqueId, $currentUser); ?>
                                        <div class="wpdiscuz-button-actions"><?php do_action('wpdiscuz_button_actions', $uniqueId, $currentUser); ?></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="wc-form-footer"  style="display: none;"> 
                        <?php
                        foreach ($this->formeStructure as $row) {
                            $this->row->renderFrontFormRow($row, $this->wpdOptions, $currentUser, $uniqueId, $isMain);
                        }
                        ?>
                    </div>
                    <div class="clearfix"></div>
                    <input type="hidden" class="wpdiscuz_unique_id" value="<?php echo $uniqueId; ?>" name="wpdiscuz_unique_id">
                </form>
            <?php } else { ?>
                <p class="wc-must-login">
                    <?php
                    echo $this->wpdOptions->phrases['wc_you_must_be_text'];
                    $login = wp_loginout(get_permalink(), false);
                    $login = preg_replace('!>([^<]+)!is', '>' . $this->wpdOptions->phrases['wc_logged_in_text'], $login);
                    echo ' ' . $login . ' ' . $this->wpdOptions->phrases['wc_to_post_comment_text'];
                    ?>
                </p>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public function renderEditFrontCommentForm($comment) {
        $html = '<div class="wpdiscuz-edit-form-wrap"><form id="wpdiscuz-edit-form">';
        $html .= '<div class="wpdiscuz-item wpdiscuz-textarea-wrap"><textarea required="required" name="wc_comment" class="wc_comment wpd-field wc_edit_comment" style="min-height: 2em;">' . str_replace(array('<code>', '</code>'), array('`', '`'), $comment->comment_content) . '</textarea></div>';
        if ($this->formCustomFields) {
            $html .= '<table class="form-table editcomment wpd-form-row"><tbody>';
            foreach ($this->formCustomFields as $key => $data) {
                $fieldType = $data['type'];
                $field = call_user_func($fieldType . '::getInstance');
                $value = get_comment_meta($comment->comment_ID, $key, true);
                $html .= $field->editCommentHtml($key, $value, $data, $comment);
            }
            $html .= '</tbody></table>';
        }
        $html .= '<input  type="hidden" name="wpdiscuz_unique_id" value="' . $comment->comment_ID . '_' . $comment->comment_parent . '">';
        $html .= '<div class="wc_save_wrap"><input class="wc_save_edited_comment" type="submit" value="' . $this->wpdOptions->phrases['wc_comment_edit_save_button'] . '"></div>';
        $html .= '</form></div>';
        return $html;
    }

    public function renderEditAdminCommentForm($comment) {
        if ($this->formCustomFields) {
            ?>
            <div  class="stuffbox">
                <div class="inside">
                    <fieldset>
                        <legend class="edit-comment-author"><?php _e('Custom Fields', 'wpdiscuz'); ?></legend>
                        <table class="form-table editcomment">
                            <tbody>
                                <?php
                                foreach ($this->formCustomFields as $key => $data) {
                                    $fieldType = $data['type'];
                                    $field = call_user_func($fieldType . '::getInstance');
                                    $value = get_comment_meta($comment->comment_ID, $key, true);
                                    echo $field->editCommentHtml($key, $value, $data, $comment);
                                }
                                ?>
                            </tbody>
                        </table>
                        <input type="hidden" name="wpdiscuz_unique_id" value="<?php echo $comment->comment_ID . '_' . $comment->comment_parent; ?>">
                    </fieldset>
                </div>
            </div>
            <?php
        }
    }

    public function renderFormStructure() {
        $this->initFormMeta();
        ?>
        <div class="wpdiscuz-wrapper">
            <div class="wpd-form-options" style="width:100%;">
                <table class="wpd-form-table" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin:10px 0px 20px 0px;">
                    <tbody>
                        <tr>
                            <th>
                                <?php _e('Language', 'wpdiscuz'); ?>
                            </th>
                            <td>
                                <?php $lang = isset($this->generalOptions['lang']) ? $this->generalOptions['lang'] : get_locale(); ?>
                                <input required="" type="text" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[lang]" value="<?php echo $lang; ?>" >
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Allow guests to comment', 'wpdiscuz'); ?>
                            </th>
                            <td>
                                <?php $guestCanComment = isset($this->generalOptions['guest_can_comment']) ? $this->generalOptions['guest_can_comment'] : 1; ?>
                                <input <?php checked($guestCanComment, 1, true); ?> type="radio" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[guest_can_comment]" value="1" id="wpd_cf_guest_yes" > <label for="wpd_cf_guest_yes"><?php _e('Yes', 'wpdiscuz'); ?></label>
                                &nbsp; 
                                <input <?php checked($guestCanComment, 0, true); ?> type="radio" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[guest_can_comment]" value="0" id="wpd_cf_guest_no"> <label for="wpd_cf_guest_no"><?php _e('No', 'wpdiscuz'); ?></label> 
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Enable subscription bar', 'wpdiscuz'); ?>
                            </th>
                            <td>
                                <?php $showSubscriptionBar = isset($this->generalOptions['show_subscription_bar']) ? $this->generalOptions['show_subscription_bar'] : 1; ?>
                                <input <?php checked($showSubscriptionBar, 1, true); ?> type="radio" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[show_subscription_bar]" value="1" id="wpd_cf_sbbar_yes" > <label for="wpd_cf_sbbar_yes"><?php _e('Yes', 'wpdiscuz'); ?></label>
                                &nbsp; 
                                <input <?php checked($showSubscriptionBar, 0, true); ?> type="radio" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[show_subscription_bar]" value="0" id="wpd_cf_sbbar_no"> <label for="wpd_cf_sbbar_no"><?php _e('No', 'wpdiscuz'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Comment form header text', 'wpdiscuz'); ?>
                            </th>
                            <td >
                                <div>
                                    <input  type="text" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[header_text]" placeholder="<?php _e('Leave a Reply', 'wpdiscuz'); ?>" value="<?php echo isset($this->generalOptions['header_text']) ? $this->generalOptions['header_text'] : __('Leave a Reply', 'wpdiscuz'); ?>" style="width:80%;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th> <?php _e('Display comment form for post types', 'wpdiscuz'); ?></th>
                            <td class="wpd-ct"> 
                                <?php
                                $this->formPostTypes = $this->formPostTypes ? $this->formPostTypes : array();
                                $registeredPostTypes = get_post_types(array('public' => true));
                                $formContentTypeRel = $this->wpdOptions->formContentTypeRel;
                                $hasForm = false;
                                $formRelExistsInfo = '<p class="wpd-info" style="padding-top:3px;">' . __('The red marked post types are already attached to other comment form. If you set this form too, the old forms will not be used for them.', 'wpdiscuz') . '</p>';
                                foreach ($registeredPostTypes as $typeKey => $typeValue) {
                                    $checked = array_key_exists($typeKey, $this->formPostTypes) ? 'checked' : '';
                                    $formRelExistsClass = '';
                                    if (!$checked && isset($formContentTypeRel[$typeKey][$lang])) {
                                        $formRelExistsClass = 'wpd-form-rel-exixts';
                                        $hasForm = true;
                                    }
                                    ?>
                                    <label class="<?php echo $formRelExistsClass; ?>" for="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES . '-' . $typeKey; ?>">
                                        <input  value="<?php echo $typeKey; ?>" id="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES . '-' . $typeKey; ?>" type="checkbox" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[<?php echo wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES . '][' . $typeKey . ']'; ?>" <?php echo $checked; ?>/>
                                        <span><?php echo $typeValue; ?></span>
                                    </label>
                                <?php } ?>
                                <?php if ($hasForm) echo $formRelExistsInfo; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <?php _e('Display comment form for post IDs', 'wpdiscuz'); ?>
                                <p class="wpd-info"> <?php _e('You can use this form for certain posts/pages specified by comma separated IDs.', 'wpdiscuz'); ?></p>
                            </th>
                            <td><input type="text" name="<?php echo wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS; ?>[postid]" placeholder="5,26,30..." value="<?php echo isset($this->generalOptions['postid']) ? $this->generalOptions['postid'] : ''; ?>" style="width:80%;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="wpdiscuz-wrapper">
                <div class="wpd-form">
                    <div class="wpd-col-wrap">
                        <div class="wpd-field">
                            <div class="wpd-field-head-textarea"><?php _e('Comment Text Field', 'wpdiscuz'); ?></div>
                        </div>
                    </div>
                    <div id="wpd-form-sortable-rows">
                        <?php
                        if ($this->formeStructure) {
                            foreach ($this->formeStructure as $id => $rowData) {
                                $this->row->dashboardForm($id, $rowData);
                            }
                        } else {
                            $this->row->dashboardForm('wpd_form_row_wrap_0', $this->defaultFieldsData());
                        }
                        ?>
                    </div>
                    <div id="wpdiscuz_form_add_row" class="wpd-field wpd-field-add" style="width:100%; padding:20px; margin:20px 0px; cursor:pointer;" title="Add new custom field">
                        <div class="wpd-field-head-new"><i class="fa fa-plus-circle"></i> <?php _e('ADD ROW', 'wpdiscuz'); ?></div>
                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <?php
    }

    private function isGuestCanComment($isUserLoggedIn) {
        $user_can_comment = TRUE;
        if (!$this->generalOptions['guest_can_comment']) {
            if (!$isUserLoggedIn) {
                $user_can_comment = FALSE;
            }
        }
        return $user_can_comment;
    }

    public function generateCaptcha() {
        $this->captchaFied->generateCaptcha();
    }

    public function defaultFieldsData() {
        return array(
            'column_type' => 'two',
            'row_order' => 0,
            'default' => 1,
            'left' => array(
                wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD => array(
                    'type' => 'wpdFormAttr\Field\DefaultField\Name',
                    'name' => __('Name', 'wpdiscuz'),
                    'desc' => '',
                    'icon' => 'fa-user',
                    'required' => '1'
                ),
                wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD => array(
                    'type' => 'wpdFormAttr\Field\DefaultField\Email',
                    'name' => __('Email', 'wpdiscuz'),
                    'desc' => '',
                    'icon' => 'fa-at',
                    'required' => '1'
                ),
                wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD => array(
                    'type' => 'wpdFormAttr\Field\DefaultField\Website',
                    'name' => __('Website', 'wpdiscuz'),
                    'desc' => '',
                    'icon' => 'fa-link',
                    'enable' => '1'
                ),
            ),
            'right' => array(
                wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD => array(
                    'type' => 'wpdFormAttr\Field\DefaultField\Captcha',
                    'name' => __('Code', 'wpdiscuz'),
                    'desc' => '',
                    'show_for_guests' => '1',
                    'show_for_users' => '0'
                ),
                wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD => array(
                    'type' => 'wpdFormAttr\Field\DefaultField\Submit',
                    'name' => __('Post Comment', 'wpdiscuz')
                )
            ),
        );
    }

    private function saveFormContentTypeRel($data, $lang) {
        $contentType = get_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, array());
        foreach ($this->formPostTypes as $formPostType) {
            if (!in_array($formPostType, $data)) {
                unset($contentType[$formPostType][$lang]);
            }
        }
        foreach ($data as $type => $lable) {
            if (isset($contentType[$type][$lang]) && $contentType[$type][$lang]) {
                $existsFormID = $contentType[$type][$lang];
                $generalOptions = get_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                unset($generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES][$type]);
                update_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            }
            $contentType[$type][$lang] = $this->formID;
        }
        update_option(wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE_REL, $contentType);
    }

    private function saveFormPostRel($data) {
        $formPostIds = isset($this->generalOptions['postidsArray']) ? $this->generalOptions['postidsArray'] : array();
        $ids = get_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, array());
        foreach ($formPostIds as $formPostId) {
            if (!in_array($formPostId, $data)) {
                unset($ids[$formPostId]);
            }
        }
        foreach ($data as $id) {
            if (isset($ids[$id]) && $ids[$id]) {
                $existsFormID = $ids[$id];
                $generalOptions = get_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                foreach ($generalOptions['postidsArray'] as $key => $pid) {
                    if ($pid == $id) {
                        unset($generalOptions['postidsArray'][$key]);
                    }
                }
                $generalOptions['postid'] = implode(', ', $generalOptions['postidsArray']);
                update_post_meta($existsFormID, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            }
            $ids[$id] = $this->formID;
        }
        update_option(wpdFormConst::WPDISCUZ_FORMS_POST_REL, $ids);
    }

    public function transferJSData($data) {
        $this->initFormFields();
        $data['wc_captcha_show_for_guest'] = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]['show_for_guests'];
        $data['wc_captcha_show_for_members'] = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]['show_for_users'];
        $data['is_email_field_required'] = $this->formFields[wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]['required'];
        return $data;
    }

    public function customFieldsExists() {
        $this->initFormFields();
        $exists = $this->formCustomFields ? true : false;
        return $exists;
    }

    public function resetData() {
        $this->formID = 0;
        $this->generalOptions = array();
        $this->formCustomFields = array();
        $this->formFields = array();
    }

}
