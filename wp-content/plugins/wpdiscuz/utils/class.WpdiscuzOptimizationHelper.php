<?php

class WpdiscuzOptimizationHelper {

    private $isCommentInMeta;
    private $subComments;
    private $optionsSerialized;
    private $dbManager;
    private $emailHelper;
    private $wpdiscuzForm;

    public function __construct($optionsSerialized, $dbManager, $emailHelper , $wpdiscuzForm) {
        $this->optionsSerialized = $optionsSerialized;
        $this->dbManager = $dbManager;
        $this->emailHelper = $emailHelper;
        $this->wpdiscuzForm = $wpdiscuzForm;
    }

    public function setSubComment($commentId, $update = 1) {
        $childCommentIds = array();
        $comments = $this->getTreeByParentId($commentId, $childCommentIds);
        $childCommentIdsString = implode(',', $comments);
        if ($childCommentIdsString) {
            $childCommentIdsString .=',';
        }
        if ($update) {
            update_comment_meta($commentId, WpdiscuzCore::META_KEY_CHILDREN, $childCommentIdsString);
        }
        return $comments;
    }

    /**
     * recursively get new comments tree
     * return array of comments' ids
     */
    public function getTreeByParentId($commentId, &$tree) {
        $children = $this->dbManager->getCommentsByParentId($commentId);
        if ($children && is_array($children)) {
            foreach ($children as $child) {
                if (!in_array($child, $tree)) {
                    $tree[] = $child;
                    $this->getTreeByParentId($child, $tree);
                }
            }
        }
        return $tree;
    }

    public function isReplyInAuthorTree($commentId, $authorComments) {
        $comment = get_comment($commentId);
        if (in_array($comment->comment_parent, $authorComments)) {
            return true;
        }
        if ($comment->comment_parent) {
            return $this->isReplyInAuthorTree($comment->comment_parent, $authorComments);
        } else {
            return false;
        }
    }

    /**
     * get list of comments by parent ids
     * @param type $commentIds the parent comment ids
     * @return type list of comments
     */
    public function getCommentListByParentIds($commentIds, $postId) {
        $update = 1;
        $commentTree = array();
        $comments = array();
        $idsInMeta = $this->dbManager->getOptimizedCommentIds($postId);
        $isIdsInMeta = count($commentIds) == count(array_intersect($commentIds, $idsInMeta));
        if ($isIdsInMeta) {
            $ids = implode(',', $commentIds);
            $children = $this->dbManager->getIdsInMeta($ids);
            if ($children) {
                $comments = explode(',', trim($children[0] . "", ','));
            }
        } else {
            foreach ($commentIds as $commentId) {
                $children = $this->dbManager->getCommentMeta($commentId, WpdiscuzCore::META_KEY_CHILDREN);
                if (!$children) {
                    $update = 0;
                    $children = $this->setSubComment($commentId, $update);
                    $commentTree[$commentId] = $children;
                    $comments = array_merge($comments, $children);
                } elseif ($children && $children->meta_value) {
                    $update = 1;
                    $children = explode(',', trim($children->meta_value, ','));
                    $comments = array_merge($comments, $children);
                }
            }
            if (!$update) {
                $this->dbManager->addTrees($commentTree);
            }
        }
        return $comments;
    }

    /**
     * add new insertd commnt id in _commentmeta
     * @param type $id the current comment id
     * @param type $comment the current comment object
     */
    public function addCommentToTree($id, $comment) {
        $form = $this->wpdiscuzForm->getForm($comment->comment_post_ID);
        if ($form->getFormID()) {
            if ($comment->comment_approved == '1' && $comment->comment_parent) {
                $this->updateCommentTree($comment);
            }
            if (!$this->optionsSerialized->votingButtonsShowHide) {
                update_comment_meta($id, WpdiscuzCore::META_KEY_VOTES, 0);
            }
            if (!$comment->comment_parent) {
                update_comment_meta($id, WpdiscuzCore::META_KEY_CHILDREN, '');
            }
        }
    }

    /**
     * add new comment id in comment meta if status is approved
     * @param type $newStatus the comment new status
     * @param type $oldStatus the comment old status
     * @param type $comment current comment object
     */
    public function statusEventHandler($newStatus, $oldStatus, $comment) {
        if ($newStatus != $oldStatus) {
            if ($newStatus == 'approved') {
                $this->updateCommentTree($comment);
                $this->notifyOnApprove($comment);
            }
        }
    }

    private function updateCommentTree($comment) {
        $id = $comment->comment_ID;
        $rootComment = $this->getCommentRoot($id);
        $rootId = $rootComment->comment_ID;
        if ($rootId != $id) {
            $this->_updateCommentTree($rootId, $id);
        }
    }

    private function _updateCommentTree($parentId, $commentId) {
        $children = get_comment_meta($parentId, WpdiscuzCore::META_KEY_CHILDREN, TRUE);
        $childrenArray = explode(',', $children);
        if (($key = array_search($commentId, $childrenArray)) === false) {
            $childrenString = $children ? $children . $commentId . ',' : $commentId . ',';
            update_comment_meta($parentId, WpdiscuzCore::META_KEY_CHILDREN, $childrenString);
        }
    }

    public function initSubComments($commentId) {
        $this->isCommentInMeta = $this->dbManager->isCommentInMeta($commentId);
        if ($this->isCommentInMeta) {
            $subParentIds = $this->dbManager->getCommentsByParentId($commentId);
            foreach ($subParentIds as $subParentId) {
                $this->subComments[] = $subParentId;
            }
        }
    }

    public function deleteCommentFromTree($commentId) {
        if ($this->subComments && is_array($this->subComments)) {
            foreach ($this->subComments as $subCommentId) {
                $this->setSubComment($subCommentId);
            }
        }
        $this->_deleteCommentFromTree($commentId);
    }

    private function _deleteCommentFromTree($commentId) {
        $rows = $this->dbManager->getRowsContainingCommentId($commentId);
        $this->deleteImmediately($rows, $commentId);
    }

    private function deleteImmediately($rows, $commentId) {
        $pattern = "#(,|^)$commentId,#is";
        foreach ($rows as $row) {
            $replaced = preg_replace($pattern, '${1}', $row['meta_value']);
            update_comment_meta($row['comment_id'], WpdiscuzCore::META_KEY_CHILDREN, $replaced);
        }
    }

    /**
     * get the current comment root comment
     * @param type $commentId the current comment id
     * @return type comment
     */
    public function getCommentRoot($commentId) {
        $comment = get_comment($commentId);
        if ($comment && $comment->comment_parent) {
            return $this->getCommentRoot($comment->comment_parent);
        } else {
            return $comment;
        }
    }

    public function getCommentDepth($commentId, &$depth = 1) {
        $comment = get_comment($commentId);
        if ($comment->comment_parent && ($depth < $this->optionsSerialized->wordpressThreadCommentsDepth)) {
            $depth++;
            return $this->getCommentDepth($comment->comment_parent, $depth);
        } else {
            return $depth;
        }
    }

    private function notifyOnApprove($comment) {
        $postId = $comment->comment_post_ID;
        $commentId = $comment->comment_ID;
        $email = $comment->comment_author_email;
        $parentComment = get_comment($comment->comment_parent);
        $this->emailHelper->notifyPostSubscribers($postId, $commentId, $email);
        if ($parentComment) {
            $parentCommentEmail = $parentComment->comment_author_email;
            if ($parentCommentEmail != $email) {
                $this->emailHelper->notifyAllCommentSubscribers($postId, $commentId, $email);
                $this->emailHelper->notifyCommentSubscribers($parentComment->comment_ID, $commentId, $email);
            }
        }
    }

    public function clearChildrenData() {
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'clear_children_data') && isset($_GET['clear']) && trim($_GET['clear']) && current_user_can('manage_options')) {
            $this->dbManager->clearChildrenDataFromMeta();
        }
        wp_redirect(admin_url('edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS));
    }

    public function removeVoteData() {
        if (isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'remove_vote_data') && isset($_GET['remove']) && intval($_GET['remove']) == 1 && current_user_can('manage_options')) {
            $res = $this->dbManager->removeVotes();
        }
        if ($res) {
            wp_redirect(admin_url('edit-comments.php?page=' . WpdiscuzCore::PAGE_SETTINGS));
        }
    }

}
