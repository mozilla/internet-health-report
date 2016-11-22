<?php

class WpdiscuzEmailHelper {

    private $optionsSerialized;
    private $dbManager;

    public function __construct($optionsSerialized, $dbManager) {
        $this->optionsSerialized = $optionsSerialized;
        $this->dbManager = $dbManager;
    }

    public function addSubscription() {
        global $wp_rewrite;
        $current_user = wp_get_current_user();
        $subscribeFormNonce = filter_input(INPUT_POST, 'wpdiscuz_subscribe_form_nonce');
        $httpReferer = filter_input(INPUT_POST, '_wp_http_referer');
        $subscriptionType = filter_input(INPUT_POST, 'wpdiscuzSubscriptionType');
        $postId = filter_input(INPUT_POST, 'wpdiscuzSubscriptionPostId');
        if ($current_user && $current_user->ID) {
            $email = $current_user->user_email;
        } else {
            $email = filter_input(INPUT_POST, 'wpdiscuzSubscriptionEmail');
        }

        $success = 0;
        if (wp_verify_nonce($subscribeFormNonce, 'wpdiscuz_subscribe_form_nonce_action') && $email && filter_var($email, FILTER_VALIDATE_EMAIL) !== false && in_array($subscriptionType, array(WpdiscuzCore::SUBSCRIPTION_POST, WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT)) && $postId) {
            $noNeedMemberConfirm = ($current_user->ID && $this->optionsSerialized->disableMemberConfirm);
            $noNeedGuestsConfirm = (!$current_user->ID && $this->optionsSerialized->disableGuestsConfirm && $this->dbManager->hasConfirmedSubscription($email));
            if ($noNeedMemberConfirm || $noNeedGuestsConfirm) {
                $confirmData = $this->dbManager->addEmailNotification($postId, $postId, $email, $subscriptionType, 1);
                $success = 1;
            } else {
                $confirmData = $this->dbManager->addEmailNotification($postId, $postId, $email, $subscriptionType, 0);
                $success = $this->confirmEmailSender($postId, $email) ? 1 : -1;
                if ($success < 0) {
                    $this->dbManager->unsubscribe($confirmData['id'], $confirmData['activation_key']);
                }
            }
        }
        $httpReferer .= $wp_rewrite->using_permalinks() ? "?subscribeAnchor&subscriptionSuccess=$success&subscriptionID=".$confirmData['id']."#wc_unsubscribe_message" : "&subscribeAnchor&subscriptionSuccess=$success#wc_unsubscribe_message";
        wp_redirect($httpReferer);
        exit();
    }

    public function confirmEmailSender($postId, $email) {
        $subject = isset($this->optionsSerialized->phrases['wc_confirm_email_subject']) ? $this->optionsSerialized->phrases['wc_confirm_email_subject'] : __('Subscribe Confirmation', 'wpdiscuz');
        $message = isset($this->optionsSerialized->phrases['wc_confirm_email_message']) ? $this->optionsSerialized->phrases['wc_confirm_email_message'] : __('Hi, <br/> You just subscribed for new comments on our website. This means you will receive an email when new comments are posted according to subscription option you\'ve chosen. <br/> To activate, click confirm below. If you believe this is an error, ignore this message and we\'ll never bother you again.', 'wpdiscuz');
        $confirm_url = $this->dbManager->confirmLink($postId, $email);
        $unsubscribe_url = $this->dbManager->unsubscribeLink($postId, $email);
        $post_permalink = get_permalink($postId);
        $message .= "<br/><br/><a href='$post_permalink'>$post_permalink</a>";
        $message .= "<br/><br/><a href='$confirm_url'>" . $this->optionsSerialized->phrases['wc_confirm_email'] . "</a>";
        $message .= "<br/><br/><a href='$unsubscribe_url'>" . $this->optionsSerialized->phrases['wc_ignore_subscription'] . "</a>";
        $headers = array();
        $content_type = apply_filters('wp_mail_content_type', 'text/html');
        $from_name = apply_filters('wp_mail_from_name', get_option('blogname'));
        $from_email = apply_filters('wp_mail_from', get_option('admin_email'));
        $headers[] = "Content-Type:  $content_type; charset=UTF-8";
        $headers[] = "From: " . $from_name . " <" . $from_email . "> \r\n";
        return wp_mail($email, $subject, $message, $headers);
    }

    /**
     * send email
     */
    public function emailSender($email_data, $wc_new_comment_id, $subject, $message) {
        global $wp_rewrite;
        $comment = get_comment($wc_new_comment_id);
        $curr_post = get_post($comment->comment_post_ID);
        $curr_post_author = get_userdata($curr_post->post_author);

        if ($email_data['email'] == $curr_post_author->user_email) {
            if (get_option('moderation_notify') && !$comment->comment_approved) {
                return;
            } else if (get_option('comments_notify') && $comment->comment_approved) {
                return;
            }
        }

        $wc_new_comment_content = $comment->comment_content;
        $permalink = get_comment_link($wc_new_comment_id);
        $unsubscribe_url = !$wp_rewrite->using_permalinks() ? get_permalink($comment->comment_post_ID) . "&" : get_permalink($comment->comment_post_ID) . "?";
        $unsubscribe_url .= "subscribeAnchor&wpdiscuzSubscribeID=" . $email_data['id'] . "&key=" . $email_data['activation_key'] . '&#wc_unsubscribe_message';
        $message .= "<br/><br/><a href='$permalink'>$permalink</a>";
        $message .= "<br/><br/>$wc_new_comment_content";
        $message .= "<br/><br/><a href='$unsubscribe_url'>" . $this->optionsSerialized->phrases['wc_unsubscribe'] . "</a>";
        $headers = array();
        $content_type = apply_filters('wp_mail_content_type', 'text/html');
        $from_name = apply_filters('wp_mail_from_name', get_option('blogname'));
        $from_email = apply_filters('wp_mail_from', get_option('admin_email'));
        $headers[] = "Content-Type:  $content_type; charset=UTF-8";
        $headers[] = "From: " . $from_name . " <" . $from_email . "> \r\n";
        wp_mail($email_data['email'], $subject, $message, $headers);
    }

    /**
     * Check notification type and send email to post new comments subscribers
     */
    public function checkNotificationType() {
        $postId = isset($_POST['postId']) ? intval($_POST['postId']) : 0;
        $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $isParent = isset($_POST['isParent']) ? intval($_POST['isParent']) : '';
        $current_user = wp_get_current_user();
        if ($current_user && $current_user->user_email) {
            $email = $current_user->user_email;
        }
        if ($comment_id && $email && $postId) {
            $this->notifyPostSubscribers($postId, $comment_id, $email);
            if (!$isParent) {
                $comment = get_comment($comment_id);
                $parent_comment_id = $comment->comment_parent;
                $parent_comment = get_comment($parent_comment_id);
                $parent_comment_email = $parent_comment->comment_author_email;
                if ($parent_comment_email != $email) {
                    $this->notifyAllCommentSubscribers($postId, $comment_id, $email);
                    $this->notifyCommentSubscribers($parent_comment_id, $comment->comment_ID, $email);
                }
            }
        }
        wp_die();
    }

    /**
     * notify on new comments
     */
    public function notifyPostSubscribers($post_id, $comment_id, $email) {
        $emails_array = $this->dbManager->getPostNewCommentNotification($post_id, $email);
        $subject = ($this->optionsSerialized->phrases['wc_email_subject']) ? $this->optionsSerialized->phrases['wc_email_subject'] : 'New Comment';
        $message = ($this->optionsSerialized->phrases['wc_email_message']) ? $this->optionsSerialized->phrases['wc_email_message'] : 'New comment on the discussion section you\'ve been interested in';
        foreach ($emails_array as $e_row) {
            $this->emailSender($e_row, $comment_id, $subject, $message);
        }
    }

    /**
     * notify on comment new replies
     */
    public function notifyAllCommentSubscribers($post_id, $new_comment_id, $email) {
        $emails_array = $this->dbManager->getAllNewCommentNotification($post_id, $email);
        $subject = ($this->optionsSerialized->phrases['wc_new_reply_email_subject']) ? $this->optionsSerialized->phrases['wc_new_reply_email_subject'] : 'New Reply';
        $message = ($this->optionsSerialized->phrases['wc_new_reply_email_message']) ? $this->optionsSerialized->phrases['wc_new_reply_email_message'] : 'New reply on the discussion section you\'ve been interested in';
        foreach ($emails_array as $e_row) {
            $this->emailSender($e_row, $new_comment_id, $subject, $message);
        }
    }

    /**
     * notify on comment new replies
     */
    public function notifyCommentSubscribers($parent_comment_id, $new_comment_id, $email) {
        $emails_array = $this->dbManager->getNewReplyNotification($parent_comment_id, $email);
        $subject = ($this->optionsSerialized->phrases['wc_new_reply_email_subject']) ? $this->optionsSerialized->phrases['wc_new_reply_email_subject'] : __('New Reply', 'wpdiscuz');
        $message = ($this->optionsSerialized->phrases['wc_new_reply_email_message']) ? $this->optionsSerialized->phrases['wc_new_reply_email_message'] : __('New reply on the discussion section you\'ve been interested in', 'wpdiscuz');
        foreach ($emails_array as $e_row) {
            $this->emailSender($e_row, $new_comment_id, $subject, $message);
        }
    }

    public function wc_notify_to_subscriber($new_status, $old_status, $comment) {
        if ($old_status != $new_status) {
            if ($new_status == 'approved') {
                $post_id = $comment->comment_post_ID;
                $comment_id = $comment->comment_ID;
                $email = $comment->comment_author_email;
                $parent_comment = get_comment($comment->comment_parent);
                $this->wc_notify_on_new_comments($post_id, $comment_id, $email);
                if ($parent_comment) {
                    $this->wc_notify_on_new_reply($parent_comment->comment_ID, $comment_id, $email);
                    $parent_comment_email = $parent_comment->comment_author_email;
                    if ($parent_comment_email != $email) {
                        $this->wc_notify_on_all_new_reply($post_id, $comment_id, $parent_comment_email);
                    }
                }
            }
        }
    }

}
