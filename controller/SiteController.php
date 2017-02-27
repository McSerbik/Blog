<?php
use app\models\Blogger;
use app\models\Post;
use app\models\Checker;
use app\models\Changes;

class SiteController
{

    function actionAuth()
    {
        global $hrf_vk_aut, $blog_uri;
        include_once(ROOT . "/views/auth.php");
        return true;
    }


    function actionSite()
    {
        global $hrf_vk_aut;
        $arPosts = null;
        try {

            if (Checker::check_template(CHANGES)) {
                if (!Changes::get_change_flag()) {
                    if (isset($_SESSION[SERIALIZED_POSTS]))
                        $arPosts = unserialize($_SESSION[SERIALIZED_POSTS]);
                }
            }
            if (!$arPosts) {
                $arPosts = array();
                $posts = Post::get_array_posts();
                for ($i = 0; $i < count($posts); $i++) {
                    $post = $posts[$i];
                    array_push($arPosts, (new Post())->init($post[P_ID], $post[P_TEXT], $post[P_TIME]));
                }
            }

        } catch
        (Exception $e) {
            echo $e->getMessage();
        }
        include_once(ROOT . "/views/site.php");
        return true;

    }

    function actionInsert(array $params)
    {
        session_start();
        if (!empty($_SESSION[IMG])) {
            switch ($params[0]) {
                case POST:
                    Post::insert(POST, $params[2]);
                    $this->actionSite();
                    break;
                case COMMENT:
                    Post::insert(COMMENT, $params[2], $params[1]);
                    $this->actionSite();
                    break;
                case REPLY :
                    Post::insert(REPLY, $params[2], $params[1]);
                    $this->actionSite();
                    break;
            }
        }
    }


    function actionLogin(array $params)
    {
        if (empty($_SESSION[IMG])) {
            session_start();
            Blogger::_init($params[0]);
        }
        $this->actionSite();
        return true;
    }

    function actionOut()
    {
        if (!empty($_SESSION[IMG]))
            unset($_SESSION[IMG]);
        $this->actionSite();
        return true;
    }

    function actionAll(array $params)
    {
        global $hrf_vk_aut;
        session_start();
        $arPosts = null;
        try {
            if (Checker::check_template(CHANGES)) {
                if (!Changes::get_change_flag()) {
                    if (isset($_SESSION[SERIALIZED_POSTS]))
                        $arPosts = unserialize($_SESSION[SERIALIZED_POSTS]);
                }
            }
            if (!$arPosts)
                $arPosts = Post::get_array_all_posts();

        } catch
        (Exception $e) {
            echo $e->getMessage();
        }
        include_once(ROOT . "/views/site.php");
        return true;
    }

    function actionSingle(array $params)
    {
        global $hrf_vk_aut;
        session_start();
        $arPosts = null;
        try {
            if (Checker::check_template(CHANGES)) {
                if (!Changes::get_change_flag()) {
                    if (isset($_SESSION[SERIALIZED_POSTS]))
                        $arPosts = unserialize($_SESSION[SERIALIZED_POSTS]);
                }
            }
            if (!$arPosts) {
                $arPosts = array();
                $posts = Post::get_array_posts();
                for ($i = 0; $i < count($posts); $i++) {
                    $post = $posts[$i];
                    array_push($arPosts, (new Post())->init($post[P_ID], $post[P_TEXT], $post[P_TIME]));

                    if ($arPosts[$i]->id == $params[0]) {
                        $comments = $arPosts[$i]->get_array_comments();
                        for ($j = 0; $j < count($comments); $j++) {
                            if ($comments[$j][C_ID] == $params[1]) {
                                $replies = $arPosts[$i]->get_array_replies($comments[$j][C_ID]);
                                $arPosts[$i]->push_comment($comments[$j][C_ID], $comments[$j][C_TEXT], $comments[$j][C_TIME], $replies);
                            } else $arPosts[$i]->push_comment($comments[$j][C_ID], $comments[$j][C_TEXT], $comments[$j][C_TIME]);

                        }
                    }
                }
            }
        } catch
        (Exception $e) {
            echo $e->getMessage();
        }
        include_once(ROOT . "/views/site.php");
        return true;
    }

}

