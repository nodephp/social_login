<?php

namespace Social\SocialLogin\Sdk;

interface SocialInterface
{
    // 获取授权token
    public function getAccessToken($code);

    // 获取用户信息
    public function getUserInfo($token);

    // 获取原始的用户信息
    public function getOriginalUserInfo();
}
