<?php
include_once 'autoload.php';

use wpdFormAttr\FormConst\wpdFormConst;
use wpdFormAttr\Form;

class wpDiscuzForm implements wpdFormConst {

    private $options;
    private $pluginVersion;
    public $wpdFormAdminOptions;
    private $form;
    private $formContentTypeRel;
    private $formPostRel;

    public function __construct($options, $pluginVersion) {
        global $pagenow;
        $this->options = $options;
        $this->pluginVersion = $pluginVersion;
        $this->form = new Form($this->options);
        $this->initAdminPhrazes();
        $this->formContentTypeRel = $options->formContentTypeRel;
        $this->formPostRel = $options->formPostRel;
        add_action('init', array(&$this, 'registerPostType'), 1);
        add_action('admin_init', array(&$this, 'custoFormRoleCaps'), 999);
        add_action('admin_menu', array(&$this, 'addFormToAdminMenu'), 874);
        add_action('admin_enqueue_scripts', array(&$this, 'customFormAdminScripts'), 245);
        add_action('manage_wpdiscuz_form_posts_custom_column', array(&$this, 'displayContentTypesOnList'), 10, 2);
        add_filter('manage_wpdiscuz_form_posts_columns', array(&$this, 'addContentTypeColumn'));
        add_action('edit_form_after_title', array(&$this, 'renderFormeGeneralSettings'));
        add_action('wp_ajax_wpdiscuzCustomFields', array(&$this, 'wpdiscuzFieldsDialogContent'));
        add_action('wp_ajax_adminFieldForm', array(&$this, 'adminFieldForm'));
        if (!$this->options->isCaptchaInSession) {
            add_action('wp_ajax_generateCaptcha', array(&$this->form, 'generateCaptcha'));
            add_action('wp_ajax_nopriv_generateCaptcha', array(&$this->form, 'generateCaptcha'));
        }
        add_filter('wpdiscuz_js_options', array($this, 'transferJSData'), 10);
        add_action('save_post', array(&$this, 'saveFormeData'), 10, 3);
        add_action('wp_trash_post', array(&$this, 'deleteOrTrashForm'));
        add_action('add_meta_boxes', array($this, 'formCustomCssMetabox'));
        add_action('add_meta_boxes_comment', array(&$this, 'renderEditCommentForm'), 10);
        add_filter('comment_save_pre', array(&$this, 'validateMetaCommentSavePre'), 10);
        add_action('edit_comment', array(&$this, 'updateCommentMeta'), 10);
        add_filter('wpdiscuz_comment_text', array(&$this, 'renderFrontCommentMetaHtml'), 10, 2);
        if (strstr('edit-comments.php', $pagenow)) {
            add_filter('get_comment_text', array(&$this, 'renderAdminCommentMetaHtml'), 10, 2);
        }
        add_filter('post_row_actions', array(&$this, 'addCloneFormAction'), 10, 2);
        add_filter('admin_post_cloneWpdiscuzForm', array(&$this, 'cloneForm'));
        add_filter('the_content', array(&$this->form, 'displayRatingMeta'), 10);
        add_action('admin_notices', array(&$this, 'formExists'));
    }

    public function validateMetaCommentSavePre($commentContent) {
        if (filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) == 'editedcomment') {
            $postID = filter_input(INPUT_POST, 'comment_post_ID', FILTER_SANITIZE_NUMBER_INT);
            $this->getForm($postID);
            if ($this->form) {
                $currentUser = wp_get_current_user();
                $this->form->initFormFields();
                $this->form->validateFields($currentUser);
            }
        }
        return $commentContent;
    }

    public function updateCommentMeta($commentID) {
        if (filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) == 'editedcomment') {
            $postID = filter_input(INPUT_POST, 'comment_post_ID', FILTER_SANITIZE_NUMBER_INT);
            $this->getForm($postID);
            if ($this->form) {
                $this->form->saveCommentMeta($commentID);
            }
        }
    }

    public function adminFieldForm() {
        $this->canManageOptions();
        $field = filter_input(INPUT_POST, 'fieldType', FILTER_SANITIZE_STRING);
        $isDefault = filter_input(INPUT_POST, 'defaultField', FILTER_SANITIZE_NUMBER_INT);
        $row = filter_input(INPUT_POST, 'row', FILTER_SANITIZE_STRING);
        $col = filter_input(INPUT_POST, 'col', FILTER_SANITIZE_STRING);
        if ($field && $row && $col) {
            if ($isDefault) {
                $field = 'wpdFormAttr\Field\\' . $field;
            }
            $fieldClass = call_user_func($field . '::getInstance');
            $fieldClass->dashboardFormDialogHtml($row, $col);
        } else {
            _e('Invalid Data !!!');
        }
        wp_die();
    }

    public function registerPostType() {
        register_post_type(self::WPDISCUZ_FORMS_CONTENT_TYPE, array(
            'labels' => array(
                'name' => __('Forms', 'wpdiscuz'),
                'singular_name' => __('Form', 'wpdiscuz'),
                'add_new' => __('Add New', 'wpdiscuz'),
                'add_new_item' => __('Add New Form', 'wpdiscuz'),
                'edit_item' => __('Edit Form', 'wpdiscuz'),
                'not_found' => __('You did not create any forms yet', 'wpdiscuz'),
                'not_found_in_trash' => __('Nothing found in Trash', 'wpdiscuz'),
                'search_items' => __('Search Forms', 'wpdiscuz')
            ),
            'show_ui' => true,
            'show_in_menu' => false,
            'public' => false,
            'supports' => array('title'),
            'capability_type' => self::WPDISCUZ_FORMS_CONTENT_TYPE,
            'map_meta_cap' => true,
                )
        );
    }

    public function custoFormRoleCaps() {
        $role = get_role('administrator');
        $role->add_cap('read');
        $role->add_cap('read_' . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap('read_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('edit_' . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap('edit_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('edit_others_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('edit_published_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('publish_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('delete_' . self::WPDISCUZ_FORMS_CONTENT_TYPE);
        $role->add_cap('delete_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('delete_others_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('delete_private_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
        $role->add_cap('delete_published_' . self::WPDISCUZ_FORMS_CONTENT_TYPE . 's');
    }

    public function saveFormeData($postId, $post, $update) {
        if ($post->post_type != self::WPDISCUZ_FORMS_CONTENT_TYPE || (isset($_REQUEST['action']) && $_REQUEST['action'] == 'inline-save')) {
            return;
        }
        $this->canManageOptions();
        $this->form->saveFormData($postId);
        $css = filter_input(INPUT_POST, self::WPDISCUZ_META_FORMS_CSS, FILTER_SANITIZE_STRING);
        update_post_meta($postId, self::WPDISCUZ_META_FORMS_CSS, $css);
    }

    public function addFormToAdminMenu() {
        global $submenu;
        $submenu['edit-comments.php'][] = array('<div id="wpd-form-menu-item"></div>&raquo; ' . __('Forms', 'wpdiscuz'), 'manage_options', 'edit.php?post_type=' . self::WPDISCUZ_FORMS_CONTENT_TYPE);
    }

    /* Display custom column */

    public function displayContentTypesOnList($column, $post_id) {
        $this->form->theFormListData($column, $post_id);
    }

    /* Add custom column to post list */

    public function addContentTypeColumn($columns) {
        return array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', 'default'),
            'form_post_types' => __('Post Types', 'wpdiscuz'),
            'form_post_ids' => __('Post IDs', 'wpdiscuz'),
            'form_lang' => __('Language', 'wpdiscuz'),
            'date' => __('Date', 'default'),
        );
    }

    public function customFormAdminScripts() {
        global $current_screen;
        if ($current_screen->id == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            wp_register_style('fontawesome-iconpicker-css', plugins_url(WPDISCUZ_DIR_NAME . '/assets/third-party/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css'), array(), '1.0.0');
            wp_enqueue_style('fontawesome-iconpicker-css');
            wp_register_script('fontawesome-iconpicker-js', plugins_url(WPDISCUZ_DIR_NAME . '/assets/third-party/fontawesome-iconpicker/js/fontawesome-iconpicker.min.js'), array('jquery'), '1.0.0', true);
            wp_enqueue_script('fontawesome-iconpicker-js');
            wp_register_style('wpdiscuz-custom-form-css', plugins_url(WPDISCUZ_DIR_NAME . '/assets/css/wpdiscuz-custom-form.css'), array(), $this->pluginVersion);
            wp_enqueue_style('wpdiscuz-custom-form-css');
            wp_register_script('wpdiscuz-custom-form', plugins_url(WPDISCUZ_DIR_NAME . '/assets/js/wpdiscuz-custom-form.js'), array('jquery'), $this->pluginVersion, true);
            wp_enqueue_script('wpdiscuz-custom-form');
            wp_localize_script('wpdiscuz-custom-form', 'wpdFormAdminOptions', $this->wpdFormAdminOptions);
            wp_register_script('wpdiscuz-form-menu-item', plugins_url(WPDISCUZ_DIR_NAME . '/assets/js/wpdiscuz-admin-menu-item.js'), array('jquery'), $this->pluginVersion, true);
            wp_enqueue_script('wpdiscuz-form-menu-item');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('jquery-ui-sortable');
        }
        if ($current_screen->id == 'edit-' . self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            wp_register_script('wpdiscuz-form-menu-item', plugins_url(WPDISCUZ_DIR_NAME . '/assets/js/wpdiscuz-admin-menu-item.js'), array('jquery'), $this->pluginVersion, true);
            wp_enqueue_script('wpdiscuz-form-menu-item');
        }
    }

    public function renderFormeGeneralSettings($post) {
        global $current_screen;
        if ($current_screen->id == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
            $this->form->setFormID($post->ID);
            $this->form->renderFormStructure();
        }
    }

    public function wpdiscuzFieldsDialogContent() {
        $this->canManageOptions();
        include_once 'wpdFormAttr/html/admin-form-fields-list.php';
        wp_die();
    }

    private function initAdminPhrazes() {
        $this->wpdFormAdminOptions = array(
            'wpdiscuz_form_structure' => wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE,
            'wpd_form_fields' => __('Field Types', 'wpdiscuz'),
            'two_column' => __('Two column', 'wpdiscuz'),
            'delete' => __('Delete', 'wpdiscuz'),
            'move' => __('Move', 'wpdiscuz'),
            'add_field' => __('Add Field', 'wpdiscuz'),
            'edit_field' => __('Edit', 'wpdiscuz'),
            'can_not_delete_field' => __('You can not delete default field.', 'wpdiscuz'),
            'confirm_delete_message' => __('You really want to delete this item ?', 'wpdiscuz'),
            'loaderImg' => plugins_url(WPDISCUZ_DIR_NAME . '/assets/img/form-loading.gif'),
        );
    }

    private function canManageOptions() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Permission Denied !!!', 'wpdiscuz'));
        }
    }

    public function renderFrontForm($commentsCount, $currentUser) {
        global $post;
        $this->getForm($post->ID);
        $this->form->initFormMeta();
        $this->form->renderFrontForm('main', '0_0', $commentsCount, $currentUser);
        ?>
        <div id = "wpdiscuz_hidden_secondary_form" style = "display: none;">
            <?php $this->form->renderFrontForm(0, 'wpdiscuzuniqueid', $commentsCount, $currentUser); ?>
        </div>
        <?php
    }

    public function renderAdminCommentMetaHtml($content, $comment) {
        $screen = get_current_screen();
        if ($screen->id != 'edit-comments') {
            return $content;
        }
        $this->form->resetData();
        $this->getForm($comment->comment_post_ID);
        if ($this->form && $this->form->customFieldsExists()) {
            $html = '<div class="wpd-comments-table">';
            $html .= '<h3>' . __('Custom Fields', 'wpdiscuz') . '</h3>';
            $html .= '<div class="wpd-comments-table-meta">';
            $htmlExists = $this->form->renderFrontCommentMetaHtml($comment->comment_ID, $tempHtml);
            if ($htmlExists) {
                $content .= $html . $tempHtml . '</div></div>';
            }
        }
        return $content;
    }

    public function renderFrontCommentMetaHtml($output, $comment) {
        $this->getForm($comment->comment_post_ID);
        if ($this->form) {
            $this->form->initFormMeta();
            $this->form->initFormFields();
            $this->form->renderFrontCommentMetaHtml($comment->comment_ID, $output);
        }
        return $output;
    }

    public function renderEditCommentForm($comment) {
        $postID = $comment->comment_post_ID;
        $this->getForm($postID);
        if ($this->form) {
            $this->form->initFormMeta();
            $this->form->initFormFields();
            $this->form->renderEditAdminCommentForm($comment);
        }
    }

    public function getForm($postID) {
        $formID = 0;
        if (!$this->form->getFormID()) {
            $postType = get_post_type($postID);
            if (key_exists($postID, $this->formPostRel)) {
                $formID = $this->formPostRel[$postID];
            } elseif (key_exists($postType, $this->formContentTypeRel)) {
                $tempContentTypeRel = $this->formContentTypeRel[$postType];
                $defaultFormID = array_shift($tempContentTypeRel);
                $lang = get_locale();
                $formID = isset($this->formContentTypeRel[$postType][$lang]) && $this->formContentTypeRel[$postType][$lang] ? $this->formContentTypeRel[$postType][$lang] : $defaultFormID;
            }
            $this->form->setFormID($formID);
        }
        return $this->form;
    }

    public function formCustomCssMetabox() {
        add_meta_box(self::WPDISCUZ_META_FORMS_CSS, __('Custom CSS', 'wpdiscuz'), array(&$this, 'formCustomCssMetaboxHtml'), self::WPDISCUZ_FORMS_CONTENT_TYPE, 'side');
    }

    public function formCustomCssMetaboxHtml() {
        global $post;
        $cssMeta = get_post_meta($post->ID, self::WPDISCUZ_META_FORMS_CSS, true);
        $css = $cssMeta ? $cssMeta : '';
        echo '<textarea style="width:100%;" name="' . self::WPDISCUZ_META_FORMS_CSS . '" class="' . self::WPDISCUZ_META_FORMS_CSS . '">' . $css . '</textarea>';
    }

    public function transferJSData($data) {
        global $post;
        $this->getForm($post->ID);
        return $this->form->transferJSData($data);
    }

    public function deleteOrTrashForm($formId) {
        if (get_post_type($formId) != wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE) {
            return;
        }
        foreach ($this->formPostRel as $postId => $value) {
            if ($formId == $value) {
                unset($this->formPostRel[$postId]);
            }
        }
        foreach ($this->formContentTypeRel as $type => $value) {
            foreach ($value as $lang => $id) {
                if ($formId == $id) {
                    unset($this->formContentTypeRel[$type][$lang]);
                }
            }
        }
        $this->form->setFormID($formId);
        $this->form->initFormMeta();
        $generalOptions = $this->form->getGeneralOptions();
        $generalOptions[wpdFormConst::WPDISCUZ_META_FORMS_POSTE_TYPES] = array();
        $generalOptions['postidsArray'] = array();
        $generalOptions['postid'] = '';
        update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
        update_option('wpdiscuz_form_content_type_rel', $this->formContentTypeRel);
        update_option('wpdiscuz_form_post_rel', $this->formPostRel);
    }

    public function createDefaultForm($version) {
        if ($version == '1.0.0' || version_compare($version, '4.0.0', '<')) {
            $oldForms = get_posts(array('posts_per_page' => 1,
                'post_type' => self::WPDISCUZ_FORMS_CONTENT_TYPE));
            if ($oldForms) {
                return;
            }
            $wpdGeneralOptions = maybe_unserialize(get_option('wc_options'));
            $phrases = array();
            if (!$this->options->isUsePoMo && $this->options->dbManager->isPhraseExists('wc_be_the_first_text')) {
                $phrases = $this->options->dbManager->getPhrases();
            }
            $form = array(
                'post_title' => __('Default Form', 'wpdiscuz'),
                'post_type' => wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE,
                'post_status' => 'publish',
                'comment_status' => 'closed',
                'ping_status' => 'closed'
            );
            $lang = get_locale();
            $formId = wp_insert_post($form);
            $defaultFields = array();
            $postTypes = array(
                'post' => 'post',
                'attachment' => 'attachment',
            );
            $this->options->initPhrasesOnLoad();
            $generalOptions = $this->getDefaultFormGeneralOptions($version, $lang, $wpdGeneralOptions, $phrases, $postTypes);
            $formStructure = $this->getDefaultFormStructure($version, $wpdGeneralOptions, $phrases, $defaultFields);
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $generalOptions);
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_STRUCTURE, add_magic_quotes($formStructure));
            update_post_meta($formId, wpdFormConst::WPDISCUZ_META_FORMS_FIELDS, add_magic_quotes($defaultFields));
            foreach ($postTypes as $key => $vale) {
                $this->formContentTypeRel[$key][$lang] = $formId;
            }
            update_option('wpdiscuz_form_content_type_rel', $this->formContentTypeRel);
        }
    }

    private function getDefaultFormGeneralOptions($version, $lang, $wpdGeneralOptions, $phrases, &$postTypes) {
        $generalOptions = array(
            'lang' => $lang,
            'guest_can_comment' => get_option('comment_registration') ? 0 : 1,
            'show_subscription_bar' => 1,
            'header_text' => __('Leave a Reply', 'wpdiscuz'),
            'wpdiscuz_form_post_types' => $postTypes,
            'postid' => '',
            'postidsArray' => array(),
        );

        if (version_compare($version, '4.0.0', '<=') && version_compare($version, '1.0.0', '!=') && is_array($wpdGeneralOptions)) {
            $generalOptions['show_subscription_bar'] = $wpdGeneralOptions['show_subscription_bar'];
            $generalOptions['header_text'] = isset($phrases['wc_leave_a_reply_text']) ? $phrases['wc_leave_a_reply_text'] : __('Leave a Reply', 'wpdiscuz');
            $optionPostTypes = $wpdGeneralOptions['wc_post_types'];
            $generalOptions['wpdiscuz_form_post_types'] = array();
            foreach ($optionPostTypes as $optionPostType) {
                $generalOptions['wpdiscuz_form_post_types'][$optionPostType] = $optionPostType;
            }
            $postTypes = $generalOptions['wpdiscuz_form_post_types'];
        }
        return $generalOptions;
    }

    private function getDefaultFormStructure($version, $wpdGeneralOptions, $phrases, &$defaultFileds) {
        $formStructure = $this->form->defaultFieldsData();
        if (version_compare($version, '4.0.0', '<=') && version_compare($version, '1.0.0', '!=')) {
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD]['required'] = $wpdGeneralOptions['wc_is_name_field_required'];
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_NAME_FIELD]['name'] = isset($phrases['wc_name_text']) ? $phrases['wc_name_text'] : __('Name', 'wpdiscuz');
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]['required'] = $wpdGeneralOptions['wc_is_email_field_required'];
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_EMAIL_FIELD]['name'] = isset($phrases['wc_email_text']) ? $phrases['wc_email_text'] : __('Email', 'wpdiscuz');
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD]['enable'] = $wpdGeneralOptions['wc_weburl_show_hide'];
            $formStructure['left'][wpdFormConst::WPDISCUZ_FORMS_WEBSITE_FIELD]['name'] = isset($phrases['wc_website_text']) ? $phrases['wc_website_text'] : __('WebSite URL', 'wpdiscuz');
            $formStructure['right'][wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]['show_for_guests'] = $wpdGeneralOptions['wc_captcha_show_hide'] ? 0 : 1;
            $formStructure['right'][wpdFormConst::WPDISCUZ_FORMS_CAPTCHA_FIELD]['show_for_users'] = $wpdGeneralOptions['wc_captcha_show_hide_for_members'];
            $formStructure['right'][wpdFormConst::WPDISCUZ_FORMS_SUBMIT_FIELD]['name'] = isset($phrases['wc_submit_text']) ? $phrases['wc_submit_text'] : __('Post Comment', 'wpdiscuz');
        }
        $defaultFileds = array_merge($formStructure['left'], $formStructure['right']);
        return array('wpd_form_row_wrap_0' => $formStructure);
    }

    public function addCloneFormAction($actions, $post) {
        if ($post->post_type == self::WPDISCUZ_FORMS_CONTENT_TYPE && $post->post_status == 'publish') {
            $url = wp_nonce_url(admin_url('admin-post.php') . '?form_id=' . $post->ID . '&action=cloneWpdiscuzForm', 'clone-form_' . $post->ID, 'clone_form_nonce');
            $actions['inline hide-if-no-js'] = '<a href="' . esc_url($url) . '">' . __('Clone Form') . '</a>';
        }
        return $actions;
    }

    public function cloneForm() {
        $formID = filter_input(INPUT_GET, 'form_id', FILTER_SANITIZE_NUMBER_INT);
        $nonce = filter_input(INPUT_GET, 'clone_form_nonce', FILTER_SANITIZE_STRING);
        if ($formID && $nonce && wp_verify_nonce($nonce, 'clone-form_' . $formID)) {
            $form = get_post($formID);
            if ($form && $form->post_type == self::WPDISCUZ_FORMS_CONTENT_TYPE) {
                $cform = array(
                    'post_title' => $form->post_title . ' ( ' . __('Clone', 'wpdiscuz') . ' )',
                    'post_type' => wpdFormConst::WPDISCUZ_FORMS_CONTENT_TYPE,
                    'post_status' => 'publish',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed'
                );
                $cfGeneralOptions = get_post_meta($formID, self::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, true);
                $cfGeneralOptions['wpdiscuz_form_post_types'] = array();
                $cfGeneralOptions['postid'] = '';
                $cfGeneralOptions['postidsArray'] = array();
                $cfFormFields = get_post_meta($formID, self::WPDISCUZ_META_FORMS_FIELDS, true);
                $cfFormStructure = get_post_meta($formID, self::WPDISCUZ_META_FORMS_STRUCTURE, true);
                $cfFormID = wp_insert_post($cform);
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_GENERAL_OPTIONS, $cfGeneralOptions);
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_FIELDS, add_magic_quotes($cfFormFields));
                update_post_meta($cfFormID, self::WPDISCUZ_META_FORMS_STRUCTURE, add_magic_quotes($cfFormStructure));
            }
        } else {
            wp_die('Permission denied !');
        }
        wp_redirect(admin_url('edit.php?post_type=' . self::WPDISCUZ_FORMS_CONTENT_TYPE));
        exit();
    }

    public function removeAllFiles() {
        $captcha = $this->form->getCaptchaFied();
        $captcha->removeOldFiles();
    }

    public function formExists() {
        if (current_user_can('manage_options')) {
            $forms = get_posts(array('posts_per_page' => 1,
                'post_type' => self::WPDISCUZ_FORMS_CONTENT_TYPE,
                'post_status' => 'publish'));
            if (!$forms) {
                ?>
                <div class="error" style="padding-top: 5px;padding-bottom: 5px;">
                    <p>
                        <?php _e('Comment Form is not detected, please navigate to form manager page to create it. ', 'wpdiscuz'); ?> 
                        <a href="<?php echo admin_url('post-new.php?post_type=' . self::WPDISCUZ_FORMS_CONTENT_TYPE); ?>" class="button button-primary"><?php _e('Add Comment Form', 'wpdiscuz'); ?></a>
                    </p>
                </div>
                <?php
            }
        }
    }

}
