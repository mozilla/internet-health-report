<?php

interface WpDiscuzConstants {
    /* === OPTIONS SLUGS === */
    const OPTION_SLUG_OPTIONS                         = 'wc_options';
    const OPTION_SLUG_VERSION                         = 'wc_plugin_version';
    /* === OPTIONS SLUGS === */
    const PAGE_SETTINGS                               = 'wpdiscuz_options_page';
    const PAGE_PHRASES                                = 'wpdiscuz_phrases_page';
    const PAGE_TOOLS                                  = 'wpdiscuz_tools_page';
    const PAGE_ADDONS                                 = 'wpdiscuz_addons_page';
    /* === META KEYS === */
    const META_KEY_CHILDREN                           = 'wpdiscuz_child_ids';
    const META_KEY_VOTES                              = 'wpdiscuz_votes';
    /* === SUBSCRIPTION TYPES === */
    const SUBSCRIPTION_POST                           = 'post';
    const SUBSCRIPTION_ALL_COMMENT                    = 'all_comment';
    const SUBSCRIPTION_COMMENT                        = 'comment';
    /* === POST ACTIONS === */
    const ACTION_FORM_NONCE                           = 'wpdiscuz_form_nonce_action';
    const ACTION_CAPTCHA_NONCE                        = 'wpdiscuz_captcha_nonce_action';
}
