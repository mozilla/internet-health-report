<?php

class WpdiscuzDBManager {

    private $db;
    private $dbprefix;
    private $users_voted;
    private $phrases;
    private $emailNotification;

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->dbprefix = $wpdb->prefix;
        $this->users_voted = $this->dbprefix . 'wc_users_voted';
        $this->phrases = $this->dbprefix . 'wc_phrases';
        $this->emailNotification = $this->dbprefix . 'wc_comments_subscription';
    }

    /**
     * create table in db on activation if not exists
     */
    public function dbCreateTables() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        if (!$this->isTableExists($this->users_voted)) {
            $sql = "CREATE TABLE `" . $this->users_voted . "`(`id` INT(11) NOT NULL AUTO_INCREMENT,`user_id` VARCHAR(255) NOT NULL, `comment_id` INT(11) NOT NULL, `vote_type` INT(11) DEFAULT NULL, `is_guest` TINYINT(1) DEFAULT 0, PRIMARY KEY (`id`), KEY `user_id` (`user_id`), KEY `comment_id` (`comment_id`),  KEY `vote_type` (`vote_type`), KEY `is_guest` (`is_guest`)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";
            dbDelta($sql);
        }
        if (!$this->isTableExists($this->phrases)) {
            $sql = "CREATE TABLE `" . $this->phrases . "`(`id` INT(11) NOT NULL AUTO_INCREMENT, `phrase_key` VARCHAR(255) NOT NULL, `phrase_value` TEXT NOT NULL, PRIMARY KEY (`id`), KEY `phrase_key` (`phrase_key`)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";
            dbDelta($sql);
        }
        $this->createEmailNotificationTable();
    }

    /**
     * check if table exists in database
     * return true if exists false otherwise
     */
    public function isTableExists($tableName) {
        return $this->db->get_var("SHOW TABLES LIKE '$tableName'") == $tableName;
    }

    /**
     * creates subscription table if not exists 
     */
    public function createEmailNotificationTable() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $oldNotificationTableNameV200 = $this->dbprefix . 'wc_email_notfication';
        $oldNotificationTableNameV214 = $this->dbprefix . 'wc_email_notify';
        if (!$this->isTableExists($this->emailNotification)) {
            $sql = "CREATE TABLE `" . $this->emailNotification . "`(`id` INT(11) NOT NULL AUTO_INCREMENT, `email` VARCHAR(255) NOT NULL, `subscribtion_id` INT(11) NOT NULL, `post_id` INT(11) NOT NULL, `subscribtion_type` VARCHAR(255) NOT NULL, `activation_key` VARCHAR(255) NOT NULL, `confirm` TINYINT DEFAULT 0, `subscription_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`), KEY `subscribtion_id` (`subscribtion_id`), KEY `post_id` (`post_id`), KEY `confirm`(`confirm`), UNIQUE KEY `subscribe_unique_index` (`subscribtion_id`,`email`)) ENGINE=MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;";
            dbDelta($sql);
        }

        if ($this->isTableExists($oldNotificationTableNameV200)) {
            $this->saveNotificationDataV200($oldNotificationTableNameV200);
        }

        if ($this->isTableExists($oldNotificationTableNameV214)) {
            $this->saveNotificationDataV214($oldNotificationTableNameV214);
        }
    }

    /**
     * save old notification data from notification table v200 into new created table and drop old table
     */
    public function saveNotificationDataV200($oldNotificationTableName) {
        $sqlPostNotificationData = "SELECT * FROM `" . $oldNotificationTableName . "` WHERE `post_id` > 0;";
        $sqlCommentNotificationData = "SELECT * FROM `" . $oldNotificationTableName . "` WHERE `comment_id` > 0;";
        $postNotificationsData = $this->db->get_results($sqlPostNotificationData, ARRAY_A);
        $commentNotificationsData = $this->db->get_results($sqlCommentNotificationData, ARRAY_A);
        $insertedPostIds = array();
        foreach ($postNotificationsData as $pNotificationData) {
            $email = $pNotificationData['email'];
            $postId = $pNotificationData['post_id'];
            $insertedPostIds[] = $postId;
            $subscribtionType = "post";
            $activationKey = md5($email . uniqid() . time());
            $sqlAddOldPostNotification = "INSERT INTO `" . $this->emailNotification . "` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, `confirm`) VALUES('$email', $postId, $postId, '$subscribtionType', '$activationKey', '1');";
            $this->db->query($sqlAddOldPostNotification);
        }

        foreach ($commentNotificationsData as $cNotificationData) {
            $email = $cNotificationData['email'];
            $commentId = $cNotificationData['comment_id'];
            $comment = get_comment($commentId);
            if (!$this->wc_has_comment_notification($comment->comment_post_ID, $commentId, $email)) {
                $subscribtionType = "comment";
                $activationKey = md5($email . uniqid() . time());
                $sqlAddOldPostNotification = "INSERT INTO `" . $this->emailNotification . "` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, `confirm`) VALUES('$email', $commentId, $comment->comment_post_ID, '$subscribtionType', '$activationKey', '1');";
                $this->db->query($sqlAddOldPostNotification);
            }
        }

        $sqlDropOldNotificationTable = "DROP TABLE `" . $oldNotificationTableName . "`;";
        $this->db->query($sqlDropOldNotificationTable);
    }

    /**
     * save old notification data from notification table v214 into new created table and drop old table
     */
    public function saveNotificationDataV214($oldNotificationTableNameV214) {
        $sqlPostNotificationData = "INSERT INTO `" . $this->emailNotification . "` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, `confirm`) SELECT `email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`, '1' FROM " . $oldNotificationTableNameV214 . ";";
        $this->db->query($sqlPostNotificationData);
        $sqlDropOldNotificationTable = "DROP TABLE `" . $oldNotificationTableNameV214 . "`;";
        $this->db->query($sqlDropOldNotificationTable);
    }

    /**
     * add vote type
     */
    public function addVoteType($userId, $commentId, $voteType, $isUserLoggedIn) {
        $sql = $this->db->prepare("INSERT INTO `" . $this->users_voted . "`(`user_id`, `comment_id`, `vote_type`,`is_guest`)VALUES(%s,%d,%d,%d);", $userId, $commentId, $voteType, !$isUserLoggedIn);
        return $this->db->query($sql);
    }

    /**
     * update vote type
     */
    public function updateVoteType($user_id, $comment_id, $vote_type) {
        $sql = $this->db->prepare("UPDATE `" . $this->users_voted . "` SET `vote_type` = %d WHERE `user_id` = %s AND `comment_id` = %d", $vote_type, $user_id, $comment_id);
        return $this->db->query($sql);
    }

    /**
     * check if the user is already voted on comment or not by user id and comment id
     */
    public function isUserVoted($user_id, $comment_id) {
        $sql = $this->db->prepare("SELECT `vote_type` FROM `" . $this->users_voted . "` WHERE `user_id` = %s AND `comment_id` = %d;", $user_id, $comment_id);
        return $this->db->get_var($sql);
    }

    /**
     * update phrases
     */
    public function updatePhrases($phrases) {
        if ($phrases) {
            foreach ($phrases as $phrase_key => $phrase_value) {

                if (is_array($phrase_value) && array_key_exists(WpdiscuzHelper::$datetime, $phrase_value)) {
                    $phrase_value = $phrase_value[WpdiscuzHelper::$datetime][0];
                }
                if ($this->isPhraseExists($phrase_key)) {
                    $sql = $this->db->prepare("UPDATE `" . $this->phrases . "` SET `phrase_value` = %s WHERE `phrase_key` = %s;", str_replace('"', '&#34;', $phrase_value), $phrase_key);
                } else {
                    $sql = $this->db->prepare("INSERT INTO `" . $this->phrases . "`(`phrase_key`, `phrase_value`)VALUES(%s, %s);", $phrase_key, str_replace('"', '&#34;', $phrase_value));
                }
                $this->db->query($sql);
            }
        }
    }

    /**
     * checks if the phrase key exists in database
     */
    public function isPhraseExists($phrase_key) {
        $sql = $this->db->prepare("SELECT `phrase_key` FROM `" . $this->phrases . "` WHERE `phrase_key` LIKE %s", $phrase_key);
        return $this->db->get_var($sql);
    }

    /**
     * get phrases from db
     */
    public function getPhrases() {
        $sql = "SELECT `phrase_key`, `phrase_value` FROM `" . $this->phrases . "`;";
        $phrases = $this->db->get_results($sql, ARRAY_A);
        $tmp_phrases = array();
        foreach ($phrases as $phrase) {
            $tmp_phrases[$phrase['phrase_key']] = WpdiscuzHelper::initPhraseKeyValue($phrase);
        }
        return $tmp_phrases;
    }

    /**
     * get last comment id from database
     * current post last comment id if post id was passed
     */
    public function getLastCommentId($args) {
        if ($args['post_id']) {
            $sql = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND `comment_approved` = 1 ORDER BY `comment_ID` DESC LIMIT 1;", $args['post_id']);
        } else {
            $sql = "SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` ORDER BY `comment_ID` DESC LIMIT 1;";
        }
        return $this->db->get_var($sql);
    }

    /**
     * retrives new comment ids for live update (UA - Update Automatically)
     */
    public function getNewCommentIds($args, $loadLastCommentId, $email) {
        $sqlCommentIds = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND `comment_ID` > %d AND `comment_author_email` != %s AND `comment_approved` = 1 ORDER BY `comment_date_gmt` ASC;", $args['post_id'], $loadLastCommentId, $email);
        return $this->matrixToArray($this->db->get_results($sqlCommentIds, ARRAY_N));
    }

    /**
     * @param type $visibleCommentIds comment ids which is visible at the moment on front end
     * @param type $email the current user email
     * @return type array of author comment ids
     */
    public function getAuthorVisibleComments($args, $visibleCommentIds, $email) {
        $sql = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_approved` = 1 AND `comment_ID` IN($visibleCommentIds) AND `comment_author_email` = %s;", $email);
        return $this->matrixToArray($this->db->get_results($sql, ARRAY_N));
    }

    /**
     * @param type $postId the current post id
     * @return type int, all comments count for current post
     */
    public function getCommentsCount($postId) {
        $sqlCommentsCount = $this->db->prepare("SELECT count(*) FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND `comment_approved` = 1;", $postId);
        return $this->db->get_var($sqlCommentsCount);
    }

    /**
     * get current post  parent comments by wordpress settings
     */
    public function getPostParentComments($args) {
        $commentParent = $args['is_threaded'] ? 'AND `comment_parent` = 0' : '';
        $status = $this->getCommentsStatus($args['status']);
        if ($args['limit'] == 0) {
            $allParentCounts = count($this->getAllParentCommentCount($args['post_id'], $args['is_threaded']));
            $sqlComments = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND $status $commentParent ORDER BY `comment_date_gmt` {$args['order']} LIMIT %d OFFSET %d", $args['post_id'], $allParentCounts, $args['offset']);
        } else if ($args['last_parent_id']) {
            $operator = ($args['order'] == 'asc') ? '>' : '<';
            $sqlComments = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND $status $commentParent AND `comment_ID` $operator %d ORDER BY `comment_date_gmt` {$args['order']} LIMIT %d", $args['post_id'], $args['last_parent_id'], $args['limit']);
        } else {
            $sqlComments = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_post_ID` = %d AND $status $commentParent ORDER BY `comment_date_gmt` {$args['order']} LIMIT %d", $args['post_id'], $args['limit']);
        }
        $commentIds = $this->db->get_results($sqlComments, ARRAY_N);
        return $this->matrixToArray($commentIds);
    }

    /**
     * get comment list ordered by date or comments votes
     */
    public function getCommentList($args) {
        if ($args['orderby'] == 'by_vote') {
            $parentIds = $this->getPostVotedCommentIds($args);
        } else {
            $parentIds = $this->getPostParentComments($args);
        }
        return $parentIds;
    }

    /**
     * get post most voted comments
     * @param type $args['post_id'] the current post id
     * @param type $args['order'] data ordering asc / desc
     * @param type $args['limit'] how many rows select
     * @param type $args['offset'] rows offset
     * @return type array of comments
     */
    public function getPostVotedCommentIds($args) {
        $commentParent = $args['is_threaded'] ? 'AND `c`.`comment_parent` = 0' : '';
        $status = $this->getCommentsStatus($args['status'], '`c`.');
        if ($args['limit']) {
            $sqlPostVotedCommentIds = $this->db->prepare("SELECT `c`.`comment_ID` FROM `" . $this->dbprefix . "comments` AS `c` INNER JOIN `" . $this->dbprefix . "commentmeta` AS `cm` ON `c`.`comment_ID` = `cm`.`comment_id` WHERE `cm`.`meta_key` = '" . WpdiscuzCore::META_KEY_VOTES . "' AND `c`.`comment_post_ID` = %d AND $status $commentParent ORDER BY (`cm`.`meta_value`+0) desc, `c`.`comment_date_gmt` {$args['date_order']} LIMIT %d OFFSET %d", $args['post_id'], $args['limit'], $args['offset']);
        } else {
            $allParentCounts = count($this->getAllParentCommentCount($args['post_id'], $args['is_threaded']));
            $sqlPostVotedCommentIds = $this->db->prepare("SELECT `c`.`comment_ID` FROM `" . $this->dbprefix . "comments` AS `c` INNER JOIN `" . $this->dbprefix . "commentmeta` AS `cm` ON `c`.`comment_ID` = `cm`.`comment_id` WHERE `cm`.`meta_key` = '" . WpdiscuzCore::META_KEY_VOTES . "' AND `c`.`comment_post_ID` = %d AND $status $commentParent ORDER BY (`cm`.`meta_value`+0) desc, `c`.`comment_date_gmt` {$args['date_order']} LIMIT %d OFFSET %d", $args['post_id'], $allParentCounts, $args['offset']);
        }
        $postVotedCommentIds = $this->db->get_results($sqlPostVotedCommentIds, ARRAY_N);
        return $this->matrixToArray($postVotedCommentIds);
    }

    /**
     * @return type array of comment ids
     */
    public function getVotedCommentIds() {
        $sqlVotedCommentIds = "SELECT `c`.`comment_ID` FROM `" . $this->dbprefix . "comments` AS `c` INNER JOIN `" . $this->dbprefix . "commentmeta` AS `cm` ON `c`.`comment_ID` = `cm`.`comment_id` WHERE `cm`.`meta_key` = '" . WpdiscuzCore::META_KEY_VOTES . "' AND `c`.`comment_approved` = 1 AND `c`.`comment_parent` = 0;";
        $votedCommentIds = $this->db->get_results($sqlVotedCommentIds, ARRAY_N);
        return $this->matrixToArray($votedCommentIds);
    }

    /**
     * get all comments - currently unused
     */
    public function getAllComments($limit, $offset) {
        $sql_comments = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` LIMIT %d OFFSET %d", $limit, $offset);
        $comments_id = $this->db->get_results($sql_comments, ARRAY_N);
        return $this->matrixToArray($comments_id);
    }

    public function getAllParentCommentCount($postId = 0, $isThreaded = 1) {
        $commentParent = $isThreaded ? '`comment_parent` = 0' : '1';
        if ($postId) {
            $sql_comments = $this->db->prepare("SELECT `comment_ID` FROM  `" . $this->dbprefix . "comments` WHERE $commentParent AND `comment_post_ID` = %d AND `comment_approved` = 1", $postId);
        } else {
            $sql_comments = "SELECT `comment_ID` FROM  `" . $this->dbprefix . "comments` WHERE $commentParent";
        }
        $parentComments = $this->db->get_results($sql_comments, ARRAY_N);
        return $this->matrixToArray($parentComments);
    }

    /**
     * get first level comments by parent comment id
     */
    public function getCommentsByParentId($commentId) {
        $sql_comments = $this->db->prepare("SELECT `comment_ID` FROM `" . $this->dbprefix . "comments` WHERE `comment_parent` = %d AND `comment_approved` = 1;", $commentId);
        $comments_id = $this->db->get_results($sql_comments, ARRAY_N);
        return $this->matrixToArray($comments_id);
    }

    /**
     * checks if curret comment already is in meta
     * return comment id if true false otherwise
     */
    public function isCommentInMeta($commentId) {
        $query = $this->db->prepare("SELECT `comment_id` FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_key` LIKE %s AND `comment_id` = %d;", WpdiscuzCore::META_KEY_CHILDREN, $commentId);
        return $this->db->query($query);
    }

    /**
     * get meta rows containing comment id
     */
    public function getRowsContainingCommentId($commentId, $parentId = 0) {
        if ($parentId) {
            $query = $this->db->prepare("SELECT `comment_id`, `meta_value` FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_value` REGEXP '(,|^)%d,' AND `comment_id` = %d;", $commentId, $parentId);
        } else {
            $query = $this->db->prepare("SELECT `comment_id`, `meta_value` FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_value` REGEXP '(,|^)%d,';", $commentId);
        }
        $rows = $this->db->get_results($query, ARRAY_A);
        return $rows;
    }

    /**
     * get count by parent comment id
     */
    public function getCommentsCountByParentId($comment_id) {
        $sql_comments = $this->db->prepare("SELECT COUNT(`comment_ID`) FROM `" . $this->dbprefix . "comments` WHERE  `comment_approved` = 1 AND `comment_parent` = %d", $comment_id);
        return $this->db->get_var($sql_comments);
    }

    public function getCommentMeta($commentId, $metaKey) {
        $sql_meta = $this->db->prepare("SELECT `comment_id`,`meta_value` FROM `" . $this->dbprefix . "commentmeta` WHERE  `comment_id` = %d AND `meta_key` = %s", $commentId, $metaKey);
        return $this->db->get_row($sql_meta);
    }

    public function addTrees($trees) {
        $sql = "INSERT INTO `" . $this->dbprefix . "commentmeta`VALUES";
        foreach ($trees as $tKey => $tVal) {
            $tree = implode(',', $tVal);
            $tree .= $tree ? ',' : '';
            $sql .= "(NULL, $tKey, '" . WpdiscuzCore::META_KEY_CHILDREN . "', '" . $tree . "'),";
        }
        $sql = trim($sql, ',');
        $this->db->query($sql);
    }

    public function checkVoteData($postId) {
        $sql_query = $this->db->prepare("INSERT INTO `" . $this->dbprefix . "commentmeta`(`meta_id`,`comment_id`, `meta_key`, `meta_value`)(SELECT NULL, `c`.`comment_ID`,%s,'0' FROM `" . $this->dbprefix . "comments` AS `c` LEFT JOIN `" . $this->dbprefix . "commentmeta` AS `cm` ON `cm`.`comment_id` = `c`.`comment_ID` AND `cm`.`meta_key` = %s WHERE `cm`.`meta_key` IS NULL AND `c`.`comment_post_ID` = %d);", WpdiscuzCore::META_KEY_VOTES, WpdiscuzCore::META_KEY_VOTES, $postId);
        $this->db->query($sql_query);
    }

    public function clearChildrenDataFromMeta() {
        $sql_query = $this->db->prepare("DELETE FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_key` LIKE %s", WpdiscuzCore::META_KEY_CHILDREN);
        $this->db->query($sql_query);
    }

    public function addEmailNotification($subsriptionId, $postId, $email, $subscriptionType, $confirm = 0) {
        if ($subscriptionType != WpdiscuzCore::SUBSCRIPTION_COMMENT) {
            $this->deleteCommentNotifications($subsriptionId, $email);
        }
        $activationKey = md5($email . uniqid() . time());
        $sql = $this->db->prepare("INSERT INTO `" . $this->emailNotification . "` (`email`, `subscribtion_id`, `post_id`, `subscribtion_type`, `activation_key`,`confirm`) VALUES(%s, %d, %d, %s, %s, %d);", $email, $subsriptionId, $postId, $subscriptionType, $activationKey, $confirm);
        $this->db->query($sql);
        return $this->db->insert_id ? array('id' => $this->db->insert_id, 'activation_key' => $activationKey) : false;
    }

    public function getPostNewCommentNotification($post_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `" . $this->emailNotification . "` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `post_id` = %d  AND `email` != %s;", WpdiscuzCore::SUBSCRIPTION_POST, $post_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function getAllNewCommentNotification($post_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `" . $this->emailNotification . "` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `post_id` = %d  AND `email` != %s;", WpdiscuzCore::SUBSCRIPTION_ALL_COMMENT, $post_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function getNewReplyNotification($comment_id, $email) {
        $sql = $this->db->prepare("SELECT `id`, `email`, `activation_key` FROM `" . $this->emailNotification . "` WHERE `subscribtion_type` = %s AND `confirm` = 1 AND `subscribtion_id` = %d  AND `email` != %s;", WpdiscuzCore::SUBSCRIPTION_COMMENT, $comment_id, $email);
        return $this->db->get_results($sql, ARRAY_A);
    }

    public function hasSubscription($postId, $email) {
        $sql = $this->db->prepare("SELECT `subscribtion_type` as `type`, `confirm` FROM `" . $this->emailNotification . "` WHERE  `post_id` = %d AND `email` = %s;", $postId, $email);
        $result = $this->db->get_row($sql, ARRAY_A);
        return $result;
    }

    public function hasConfirmedSubscription($email) {
        $sql = "SELECT `subscribtion_type` as `type` FROM `" . $this->emailNotification . "` WHERE `email` = %s AND `confirm` = 1;";
        $sql = $this->db->prepare($sql, $email);
        return $this->db->get_var($sql);
    }
    
    public function hasConfirmedSubscriptionByID($subscribID) {
        $sql = "SELECT `subscribtion_type` as `type` FROM `" . $this->emailNotification . "` WHERE `id` = %d AND `confirm` = 1;";
        $sql = $this->db->prepare($sql, $subscribID);
        return $this->db->get_var($sql);
    }

    /**
     * delete comment thread subscribtions if new subscribtion type is post
     */
    public function deleteCommentNotifications($post_id, $email) {
        $sql_delete_comment_notifications = $this->db->prepare("DELETE FROM `" . $this->emailNotification . "` WHERE `subscribtion_type` != %s AND `post_id` = %d AND `email` LIKE %s;", WpdiscuzCore::SUBSCRIPTION_POST, $post_id, $email);
        $this->db->query($sql_delete_comment_notifications);
    }

    /**
     * create unsubscribe link
     */
    public function unsubscribeLink($postID, $email) {
        global $wp_rewrite;
        $sql_subscriber_data = $this->db->prepare("SELECT `id`, `post_id`, `activation_key` FROM `" . $this->emailNotification . "` WHERE  `post_id` = %d  AND `email` LIKE %s", $postID, $email);
        $wc_unsubscribe = $this->db->get_row($sql_subscriber_data, ARRAY_A);
        $post_id = $wc_unsubscribe['post_id'];
        $wc_unsubscribe_link = !$wp_rewrite->using_permalinks() ? get_permalink($post_id) . "&" : get_permalink($post_id) . "?";
        $wc_unsubscribe_link .= "subscribeAnchor&wpdiscuzSubscribeID=" . $wc_unsubscribe['id'] . "&key=" . $wc_unsubscribe['activation_key'] . '&#wc_unsubscribe_message';
        return $wc_unsubscribe_link;
    }

    /**
     * generate confirm link
     */
    public function confirmLink($postID, $email) {
        global $wp_rewrite;
        $sql_subscriber_data = $this->db->prepare("SELECT `id`, `activation_key` FROM `" . $this->emailNotification . "` WHERE `post_id` = %d AND `email` LIKE %s ", $postID, $email);
        $wc_confirm = $this->db->get_row($sql_subscriber_data, ARRAY_A);
        $wc_confirm_link = !$wp_rewrite->using_permalinks() ? get_permalink($postID) . "&" : get_permalink($postID) . "?";
        $wc_confirm_link .= "subscribeAnchor&wpdiscuzConfirmID=" . $wc_confirm['id'] . "&wpdiscuzConfirmKey=" . $wc_confirm['activation_key'] . '&wpDiscuzComfirm=yes&#wc_unsubscribe_message';
        return $wc_confirm_link;
    }

    /**
     * Confirm  post or comment subscribtion
     */
    public function notificationConfirm($subscribe_id, $key) {
        $sql_confirm = $this->db->prepare("UPDATE `" . $this->emailNotification . "` SET `confirm` = 1 WHERE `id` = %d AND `activation_key` LIKE %s;", $subscribe_id, $key);
        return $this->db->query($sql_confirm);
    }

    /**
     * delete subscribtion
     */
    public function unsubscribe($id, $activation_key) {
        $sql_unsubscribe = $this->db->prepare("DELETE FROM `" . $this->emailNotification . "` WHERE `id` = %d AND `activation_key` LIKE %s", $id, $activation_key);
        return $this->db->query($sql_unsubscribe);
    }

    public function alterPhrasesTable() {
        $sql_alter = "ALTER TABLE `" . $this->phrases . "` MODIFY `phrase_value` TEXT NOT NULL;";
        $this->db->query($sql_alter);
    }

    public function alterVotingTable() {
        $sql_alter = "ALTER TABLE `" . $this->users_voted . "` MODIFY `user_id` VARCHAR(255) NOT NULL, ADD COLUMN `is_guest` TINYINT(1) DEFAULT 0, ADD INDEX `is_guest` (`is_guest`);";
        $this->db->query($sql_alter);
    }

    public function alterNotificationTable() {
        $sql_alter = "ALTER TABLE `" . $this->emailNotification . "` ADD UNIQUE KEY `subscribe_unique_index` (`subscribtion_id`,`email`);";
        $this->db->query($sql_alter);
    }

    private function matrixToArray($commentIds) {
        $ids = array();
        foreach ($commentIds as $comment) {
            $ids[] = $comment[0];
        }
        return $ids;
    }

    /**
     * return users id who have published posts
     */
    public function getPostsAuthors() {
        $sql = "SELECT `post_author` FROM `" . $this->dbprefix . "posts` WHERE `post_type` = 'post' AND `post_status` IN ('publish', 'private') GROUP BY `post_author`;";
        $postsAuthors = $this->db->get_results($sql, ARRAY_N);
        return $this->matrixToArray($postsAuthors);
    }

    public function getOptimizedCommentIds($postId) {
        $sql = $this->db->prepare("SELECT `cm`.`comment_id` FROM `" . $this->dbprefix . "commentmeta` AS `cm` INNER JOIN `" . $this->dbprefix . "comments` AS `c` ON `c`.`comment_ID` = `cm`.`comment_id` WHERE `c`.`comment_post_ID` = %d AND `c`.`comment_approved` = 1 AND `cm`.`meta_key` = '" . WpdiscuzCore::META_KEY_CHILDREN . "' AND `cm`.`meta_value` != '';", $postId);
        $commentIds = $this->db->get_results($sql, ARRAY_N);
        return $this->matrixToArray($commentIds);
    }

    public function getIdsInMeta($commentIds) {
        if (!$commentIds) {
            return array();
        }
        $this->db->query("SET SESSION group_concat_max_len = 1000000;");
        $sql = "SELECT GROUP_CONCAT(TRIM(BOTH ',' FROM `meta_value`)) FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_key` = '" . WpdiscuzCore::META_KEY_CHILDREN . "' AND comment_id IN ($commentIds)";
        $comments = $this->db->get_results($sql, ARRAY_N);
        return $this->matrixToArray($comments);
    }

    public function removeVotes() {
        $sqlTruncate = "TRUNCATE `" . $this->dbprefix . "wc_users_voted`;";
        $sqlDelete = "DELETE FROM `" . $this->dbprefix . "commentmeta` WHERE `meta_key` = '" . WpdiscuzCore::META_KEY_VOTES . "';";
        return $this->db->query($sqlTruncate) && $this->db->query($sqlDelete);
    }

    private function getCommentsStatus($status, $alias = '') {
        $s = '';
        if ($status == 'all') {
            $s = "($alias`comment_approved` = 0 OR $alias`comment_approved` = 1)";
        } else if ($status == 'hold') {
            $s = "($alias`comment_approved` = 0)";
        } else {
            $s = "$alias`comment_approved` = 1";
        }
        return $s;
    }

    public function getVotes($commentId) {
        $sql = "SELECT IFNULL(SUM(`vote_type`), 0) FROM `" . $this->users_voted . "` WHERE `vote_type` = 1 AND `comment_id` = %d UNION SELECT IFNULL(SUM(`vote_type`), 0) FROM `" . $this->users_voted . "` WHERE `vote_type` = -1 AND `comment_id` = %d";
        $sql = $this->db->prepare($sql, $commentId, $commentId);
        return $this->matrixToArray($this->db->get_results($sql, ARRAY_N));
    }

    public function getLikeCount($commentId) {
        $sql = "SELECT IFNULL(SUM(`vote_type`), 0) FROM `" . $this->users_voted . "` WHERE `vote_type` = 1 AND `comment_id` = %d ";
        $sql = $this->db->prepare($sql, $commentId);
        return $this->db->get_var($sql);
    }

    public function getDislikeCount($commentId) {
        $sql = "SELECT IFNULL(SUM(`vote_type`), 0) FROM `" . $this->users_voted . "` WHERE `vote_type` = -1 AND `comment_id` = %d";
        $sql = $this->db->prepare($sql, $commentId);
        return $this->db->get_var($sql);
    }

    public function importOptions($serializedOptions) {
        if ($serializedOptions) {
            $serializedOptions = stripslashes($serializedOptions);
            $sql = "UPDATE `" . $this->dbprefix . "options` SET `option_value` = %s WHERE `option_name` = '" . WpdiscuzCore::OPTION_SLUG_OPTIONS . "'";
            $sql = $this->db->prepare($sql, $serializedOptions);
            $this->db->query($sql);
        }
    }
}
