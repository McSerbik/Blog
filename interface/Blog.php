<?php
namespace app\_interface;

interface Blog
{
    function __construct();

    function init($post_id, $post_text, $post_time);

    function push_comment($comment_id, $comment_text, $comment_time, array $replies = array());

    static function get_array_all_posts();

    static function get_array_posts();

    function get_array_comments();

    function get_array_replies($comment_id);

    function get_count_comment();

    function get_count_replies($comment_id);

    static function insert($which, $text, $id = 0);

    function __get($name);
}

?>