<?php
namespace app\models;

use app\_interface\Blog;

class  Post implements Blog
{
    private $id;
    private $text;
    private $time;
    private $comments = array();

    function __construct()
    {
        $this->id = null;
        $this->text = null;
        $this->time = null;

    }

    function init($post_id, $post_text, $post_time)
    {
        $this->id = $post_id;
        $this->text = $post_text;
        $this->time = $post_time;
        return $this;
    }

    function push_comment($comment_id, $comment_text, $comment_time, array $replies = array())
    {
        array_push($this->comments, [COMMENT => array(C_ID => $comment_id, C_TEXT => $comment_text, C_TIME => $comment_time,
            REPLIES => $replies)]);
    }

    static function get_array_all_posts()
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);
        if (!Checker::check_template(COMMENTS))
            Checker::create_template(COMMENTS);
        if (!Checker::check_template(REPLIES))
            Checker::create_template(REPLIES);
        if (!Checker::check_template(CHANGES))
            Checker::create_template(CHANGES);


        $connect = Connect::get_connect();
        $count_row = "SELECT COUNT(*)
                       FROM posts
                       LEFT JOIN comments ON posts.post_id = comments.to_post_id
                       LEFT JOIN replies ON comments.comment_id = replies.to_comment_id";

        if (!$connect->multi_query($count_row)) {
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
        $count_row = (int)$result['COUNT(*)'];

        $connect = Connect::get_connect();
        $select = "SELECT posts.post_id,posts.post_text,posts.post_time,
                          comments.comment_id,comments.comment_text,comments.comment_time,
                          replies.reply_text,replies.reply_time
                    FROM posts
                    LEFT JOIN comments ON posts.post_id = comments.to_post_id
                    LEFT JOIN replies ON comments.comment_id = replies.to_comment_id
                    ORDER BY posts.post_time DESC";
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


        $post_id = null;
        $post_text = null;
        $post_time = null;
        $comment_id = null;
        $comment_text = null;
        $comment_time = null;
        $reply_text = null;
        $reply_time = null;

        $arReplies = array();
        $arPosts = array();
        $myPost = new Post();

        for ($i = 0; $i < $count_row; $i++) {
            $data = $result->fetch_assoc();
            if ($post_id != $data[P_ID]) {

                if ($myPost->text) {
                    if ($comment_text and count($arReplies))
                        $myPost->push_comment($comment_id, $comment_text, $comment_time, $arReplies);
                    else if ($comment_text)
                        $myPost->push_comment($comment_id, $comment_text, $comment_time);
                    array_push($arPosts, $myPost);
                    $myPost = null;
                    $myPost = new Post();
                    $arReplies = array();
                }

                $post_id = $data[P_ID];
                $post_text = $data[P_TEXT];
                $post_time = $data[P_TIME];
                $comment_id = $data[C_ID];
                $comment_text = $data[C_TEXT];
                $comment_time = $data[C_TIME];
                $reply_text = $data[R_TEXT];
                $reply_time = $data[R_TIME];

                $myPost->init($post_id, $post_text, $post_time);

                if ($reply_text)
                    array_push($arReplies, compact(R_TEXT, R_TIME));


            } else {

                if ($comment_id != $data[C_ID]) {

                    if ($comment_text and count($arReplies))
                        $myPost->push_comment($comment_id, $comment_text, $comment_time, $arReplies);
                    else if ($comment_text)
                        $myPost->push_comment($comment_id, $comment_text, $comment_time);

                    $comment_id = $data[C_ID];
                    $comment_text = $data[C_TEXT];
                    $comment_time = $data[C_TIME];
                    $reply_text = $data[R_TEXT];
                    $reply_time = $data[R_TIME];

                    $arReplies = array();


                    if ($reply_text)
                        array_push($arReplies, compact(R_TEXT, R_TIME));


                } else {
                    $reply_text = $data[R_TEXT];
                    $reply_time = $data[R_TIME];
                    if ($reply_text)
                        array_push($arReplies, compact(R_TEXT, R_TIME));

                }
            }


            if ($i == $count_row - 1 and $myPost->text) {
                if ($comment_text and count($arReplies))
                    $myPost->push_comment($comment_id, $comment_text, $comment_time, $arReplies);
                else if ($comment_text)
                    $myPost->push_comment($comment_id, $comment_text, $comment_time);

                array_push($arPosts, $myPost);
            }
        }


        $serialize = serialize($arPosts);
        if ($serialize)
            $_SESSION[SERIALIZED_POSTS] = $serialize;

        Changes::set_change_flag(0);
        return $arPosts;

    }

    static function get_array_posts()
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);

        $connect = Connect::get_connect();
        $data = array();
        $select = "SELECT posts.post_id,posts.post_text,posts.post_time
                    FROM posts
                    WHERE posts.post_text != ''
                    ORDER BY posts.post_time DESC";

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
        while ($row = $result->fetch_assoc())
            array_push($data, $row);
        return $data;

    }

    function get_array_comments()
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);
        if (!Checker::check_template(COMMENTS))
            Checker::create_template(COMMENTS);

        $connect = Connect::get_connect();
        $data = array();
        $select = "SELECT comments.comment_id,comments.comment_text,comments.comment_time
                    FROM comments
                    WHERE comments.comment_text  != '' AND  comments.to_post_id = " . $this->id . " 
                    ORDER BY comments.comment_time ASC";

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
        while ($row = $result->fetch_assoc())
            array_push($data, $row);
        return $data;
    }

    function get_array_replies($comment_id)
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);
        if (!Checker::check_template(COMMENTS))
            Checker::create_template(COMMENTS);
        if (!Checker::check_template(REPLIES))
            Checker::create_template(REPLIES);

        $connect = Connect::get_connect();
        $data = array();
        $select = "SELECT replies.reply_text,replies.reply_time
                    FROM replies
                    WHERE replies.reply_text != '' AND replies.to_comment_id =" . $comment_id . "
                    ORDER BY replies.reply_time ASC";

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
        while ($row = $result->fetch_assoc())
            array_push($data, $row);
        return $data;

    }

    function get_count_comment()
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);
        if (!Checker::check_template(COMMENTS))
            Checker::create_template(COMMENTS);

        $connect = Connect::get_connect();
        $select = "SELECT COUNT(*) FROM comments
                    WHERE comments.comment_text  != '' AND  comments.to_post_id =" . $this->id;
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
        $count = $result['COUNT(*)'];
        if ($count)
            return $count;
        return 0;
    }

    function get_count_replies($comment_id)
    {
        if (!Checker::check_template(POSTS))
            Checker::create_template(POSTS);
        if (!Checker::check_template(COMMENTS))
            Checker::create_template(COMMENTS);
        if (!Checker::check_template(REPLIES))
            Checker::create_template(REPLIES);

        $connect = Connect::get_connect();
        $select = "SELECT COUNT(*) FROM replies
                    WHERE replies.reply_text  != '' AND  replies.to_comment_id = " . $comment_id;
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
        $count = $result['COUNT(*)'];
        return $count;
    }

    function __get($name)
    {

        if (property_exists(get_class($this), $name)) {
            return $this->$name;

        }
        $trace = debug_backtrace();
        trigger_error(
            "this class hasn't property in __get(): " . $name .
            ' in file ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;

    }

    static function insert($which, $text, $id = 0)
    {
        switch ($which) {
            case POST:
                if (!Checker::check_template(POSTS))
                    Checker::create_template(POSTS);
                if (!Checker::check_template(COMMENTS))
                    Checker::create_template(COMMENTS);
                if (!Checker::check_template(REPLIES))
                    Checker::create_template(REPLIES);
                if (!Checker::check_template(CHANGES))
                    Checker::create_template(CHANGES);

                $connect = Connect::get_connect();
                $select = "INSERT INTO posts(posts . post_text) values('" . addslashes($text) . "')";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                unset($_SESSION[SERIALIZED_POSTS]);
                Changes::set_change_flag();
                break;

            case COMMENT:
                if (!Checker::check_template(POSTS))
                    Checker::create_template(POSTS);
                if (!Checker::check_template(COMMENTS))
                    Checker::create_template(COMMENTS);
                if (!Checker::check_template(REPLIES))
                    Checker::create_template(REPLIES);
                if (!Checker::check_template(CHANGES))
                    Checker::create_template(CHANGES);

                $connect = Connect::get_connect();
                $select = "INSERT INTO comments(to_post_id,comment_text) values(" . $id . ",'" . addslashes($text) . "')";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                unset($_SESSION[SERIALIZED_POSTS]);
                Changes::set_change_flag();
                break;

            case REPLY:
                if (!Checker::check_template(POSTS))
                    Checker::create_template(POSTS);
                if (!Checker::check_template(COMMENTS))
                    Checker::create_template(COMMENTS);
                if (!Checker::check_template(REPLIES))
                    Checker::create_template(REPLIES);
                if (!Checker::check_template(CHANGES))
                    Checker::create_template(CHANGES);

                $connect = Connect::get_connect();
                $select = "INSERT INTO replies(to_comment_id,reply_text) VALUES (" . $id . ",'" . addslashes($text) . "')";
                if (!$connect->multi_query($select)) {
                    $trace = debug_backtrace();
                    trigger_error(
                        "bad select : " . mysqli_error($connect) .
                        ' in file ' . $trace[0]['file'] .
                        ' on line ' . $trace[0]['line'],
                        E_USER_NOTICE);
                    return null;
                }
                unset($_SESSION[SERIALIZED_POSTS]);
                Changes::set_change_flag();
                break;
        }

    }

}

?>
