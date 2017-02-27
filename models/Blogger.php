<?php

namespace app\models;

use app\_interface\User;

class Blogger implements User
{
    static function _init($code)
    {
        global $client_id, $client_secret, $redirect_uri;
        $token = json_decode(file_get_contents('https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query(compact('client_id', 'client_secret', 'redirect_uri', 'code')))), true);
        $uids = $token['user_id'];
        $fields = 'uid,first_name,last_name,screen_name,sex,bdate,photo_big';
        $access_token = $token['access_token'];

        $userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get' . '?' . urldecode(http_build_query(compact('uids', 'fields', 'access_token')))), true);
        if (isset($userInfo['response'][0]['uid'])) {
            $userInfo = $userInfo['response'][0];
            $_SESSION[IMG] = $userInfo['photo_big'];
        }

    }


}
