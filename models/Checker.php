<?php
namespace app\models;

use mysqli_result;
use app\_interface\Check;

class Checker implements Check
{
    static function check_template($which)
    {
        switch ($which) {
            case POSTS:
                $connect = Connect::get_connect();
                $select = Checker::get_str_for_template_check(POSTS);
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                $result = $connect->use_result();
                $column_name = array('post_id', 'post_text', 'post_time');
                $column_type = array('int(11) unsigned', 'mediumtext', 'timestamp');
                $agree = Checker::same_template(compact('column_name'), compact('column_type'), $result);
                if ($agree)
                    return true;
                else return false;
                break;

            case COMMENTS:
                $connect = Connect::get_connect();
                $select = Checker::get_str_for_template_check(COMMENTS);
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                $result = $connect->use_result();
                $column_name = array('comment_id', 'to_post_id', 'comment_text', 'comment_time');
                $column_type = array('int(11) unsigned', 'int(11) unsigned', 'mediumtext', 'timestamp');
                $agree = Checker::same_template(compact('column_name'), compact('column_type'), $result);
                if ($agree)
                    return true;
                else return false;
                break;

            case REPLIES:
                $connect = Connect::get_connect();
                $select = Checker::get_str_for_template_check(REPLIES);
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                $result = $connect->use_result();
                $column_name = array('reply_id', 'to_comment_id', 'reply_text', 'reply_time');
                $column_type = array('int(11) unsigned', 'int(11) unsigned', 'mediumtext', 'timestamp');
                $agree = Checker::same_template(compact('column_name'), compact('column_type'), $result);
                if ($agree)
                    return true;
                else return false;
                break;

            case CHANGES:
                $connect = Connect::get_connect();
                $select = Checker::get_str_for_template_check(CHANGES);
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                $result = $connect->use_result();
                $column_name = array('did');
                $column_type = array('tinyint(1)');
                $agree = Checker::same_template(compact('column_name'), compact('column_type'), $result);
                if ($agree) {
                    $connect = Connect::get_connect();
                    $select = "SELECT COUNT(*) FROM changes";
                    if (!$connect->multi_query($select)) {
                        $trace = debug_backtrace();
                        trigger_error(
                            "bad select : " . mysqli_error($connect) .
                            ' in file ' . $trace[0]['file'] .
                            ' on line ' . $trace[0]['line'],
                            E_USER_NOTICE);
                        return null;
                    }
                    $result = $connect->use_result();
                    $result = $result->fetch_assoc();
                    $agree = $result['COUNT(*)'];
                }
                if ((int)$agree === 1)
                    return true;
                else return false;
                break;
        }
        return null;
    }

    static function create_template($which)
    {
        switch ($which) {
            case POSTS:
                $connect = Connect::get_connect();
                $select = "DROP TABLE IF EXISTS `posts`;
                          CREATE TABLE `posts` 
                          (
                            `post_id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            `post_text` MEDIUMTEXT NOT NULL,
                            `post_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            KEY `post_time`(`post_time`)
                           )COLLATE='utf8_general_ci'
                            ENGINE=InnoDB; 
                            INSERT INTO posts (post_text) VALUES ('I like this blog... ');";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;

            case COMMENTS:
                $connect = Connect::get_connect();
                $select = "DROP TABLE IF EXISTS `comments`;
                          CREATE TABLE `comments` 
                          (
                            `comment_id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            `to_post_id` INT(11) UNSIGNED NOT NULL,
                            `comment_text` MEDIUMTEXT NOT NULL,
                            `comment_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (`to_post_id`) REFERENCES `posts` (`post_id`) ON UPDATE CASCADE ON DELETE CASCADE,
                             KEY `comment_time`(`comment_time`)
                          ) COLLATE='utf8_general_ci'
                            ENGINE=InnoDB;";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;

            case REPLIES:
                $connect = Connect::get_connect();
                $select = "DROP TABLE IF EXISTS `replies`;
                          CREATE TABLE `replies`
                          (
                            `reply_id` INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            `to_comment_id` INT(11) UNSIGNED NOT NULL,
                            `reply_text` MEDIUMTEXT NOT NULL,
                            `reply_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (`to_comment_id`) REFERENCES `comments`(`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              KEY `reply_time`(`reply_time`) 
                          )COLLATE= 'utf8_general_ci'
                           ENGINE = InnoDB;";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;

            case CHANGES:
                $connect = Connect::get_connect();
                $select = "DROP TABLE IF EXISTS changes;
                          CREATE TABLE changes
                          (
                            `did` bool not null
                          )ENGINE=MyISAM;
                          INSERT INTO changes (did) VALUES (1);";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                break;
        }
        return null;
    }

    private static function get_str_for_template_check($what)
    {
        return "SELECT column_name,column_type
                            FROM information_schema.columns
                            WHERE table_schema = DATABASE()
                            AND table_name = '" . $what . "'";

    }

    private static function same_template($double_array_name, $double_array_type, mysqli_result $result)
    {
        $count = 0;
        while ($row = $result->fetch_assoc()) {
            if (
                $row['column_name'] != $double_array_name['column_name'][$count] or
                $row['column_type'] != $double_array_type['column_type'][$count]
            ) {
                return false;
            }
            ++$count;
        };
        return $count;
    }
}

?>