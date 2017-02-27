<?php

$blog_uri = 'http://' . $_SERVER['HTTP_HOST'] . "/blog";

$client_id = '5449101';
$client_secret = 'y769RJnZBzJNcNEtP9Gv';
$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . "/user/login/";
$hrf_vk_aut = "http://oauth.vk.com/authorize?client_id=5449101&redirect_uri=" . $redirect_uri . "&response_type=code";