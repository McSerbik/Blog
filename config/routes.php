<?php
return [
    'blog/posts/all' => 'site/all',
    'blog/post/([0-9]+)/comment/([0-9]+)' => 'site/single/$1/$2',
    'blog/post/([0-9]+)' => 'site/single/$1',
    'blog' => 'site/site',
    'insert/([a-z]{4,7})(/)?([0-9]+)?(/)([A-Za-z0-9]+)' => 'site/insert/$1/$3/$5',
    'out' => 'site/out',
    'user/login/\?code=([0-9]+)' => 'site/login/$1',
    'auth' => 'site/auth'
];
?>