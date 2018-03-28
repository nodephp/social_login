<?php
$classMap = [
    'Social\\SocialLogin' => 'src/SocialLogin.php',
    'Social\\SocialLogin\\Sdk\\Base' => 'src/Sdk/Base.php',
    'Social\\SocialLogin\\Sdk\\SocialInterface' => 'src/Sdk/SocialInterface.php',
    'Social\\SocialLogin\\Sdk\\Channels\Wx' => 'src/Sdk/Channels/Wx.php',
    'Social\\SocialLogin\\Sdk\\Channels\Weibo' => 'src/Sdk/Channels/Weibo.php',
    'Social\\SocialLogin\\Sdk\\Channels\Qq' => 'src/Sdk/Channels/Qq.php',
];
spl_autoload_register(function ($class) use ($classMap) {
    include $classMap[$class];
});

use Social\SocialLogin;
$config = [
    'qq' => [
        'appid' => '101422678',
        'appsecret' => '5ed732435bfbbd273f1cc93ba4ff9186',
    ],
    'wx' => [
        'appid' => 'wx649376d86fd7a29b',
        'appsecret' => '3147a5023e32656e98183f43a4f9dae3',
    ],
    'weibo' => [
        'appid' => '196697079',
        'appsecret' => '137cdc72b6b3b7864ad428605d03671f',
        'redirecturi' => 'http://www.6mars.com/thirdback/weibo',
        'objectid' => 'Mars.6Mars.com',
    ],
];

$code = [
    "access_token" => "763CDC9B5C81694722FF30943E5E858D",
    "openid" => "08AE104CD9D5B60B829EE6ED1E5893FE",
];
try {
    $obj = new SocialLogin($config, 'qq');
    $socialInfo = $obj->authAndGetUserInfo($code);
} catch (Exception $e) {
    echo $e->getMessage(), '具体原因：' . print_r($e->getCode(), true);
    exit;
}

print_r($socialInfo);
