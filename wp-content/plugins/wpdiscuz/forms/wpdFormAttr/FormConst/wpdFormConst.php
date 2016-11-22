<?php
namespace wpdFormAttr\FormConst;

interface wpdFormConst {
    
    /* === OPTIONS NAMES === */
    const WPDISCUZ_FORMS_CONTENT_TYPE_REL             = 'wpdiscuz_form_content_type_rel';
    const WPDISCUZ_FORMS_POST_REL                     = 'wpdiscuz_form_post_rel';
    /* === CONTENT TYPES ===*/
    const WPDISCUZ_FORMS_CONTENT_TYPE                 = 'wpdiscuz_form';
    /* === FORME META === */
    const WPDISCUZ_META_FORMS_STRUCTURE               = 'wpdiscuz_form_structure';
    const WPDISCUZ_META_FORMS_POSTE_TYPES             = 'wpdiscuz_form_post_types';
    const WPDISCUZ_META_FORMS_GENERAL_OPTIONS         = 'wpdiscuz_form_general_options';
    const WPDISCUZ_META_FORMS_FIELDS                  = 'wpdiscuz_form_fields';
    const WPDISCUZ_META_FORMS_CSS                     = 'wpd_form_custom_css';
    /* === DEFAULT FIELDS NAMES ===*/
    const WPDISCUZ_FORMS_NAME_FIELD                   = 'wc_name';
    const WPDISCUZ_FORMS_EMAIL_FIELD                  = 'wc_email';
    const WPDISCUZ_FORMS_WEBSITE_FIELD                = 'wc_website';
    const WPDISCUZ_FORMS_CAPTCHA_FIELD                = 'wc_captcha';
    const WPDISCUZ_FORMS_SUBMIT_FIELD                 = 'submit';
    const CAPTCHA_LENGTH                              = 5;
}
