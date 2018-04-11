<?php

namespace Social\SocialLogin\Sdk;

use Exception;

class Base
{
    const HTTP_ERROR = 2;
    const TOKEN_ERROR = 5;
    const INFO_ERROR = 1307;
    const HTTP_CONNECT_TIMEOUT = 2;
    const HTTP_TIMEOUT = 2;
    protected $appid;
    protected $appsecret;

    protected $token;
    protected $openId;
    protected $userInfo;

    public function __construct(array $config = array())
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function authAndGetUserInfo($code)
    {
        if (!is_array($code)) {
            $this->getAccessToken($code);
            $result = $this->getUserInfo();
        } else {
            $result = $this->getUserInfo($code);
        }

        return $result;
    }

    public function getOriginalUserInfo()
    {
        return $this->userInfo;
    }

    public function getTokenInfo()
    {
        return $this->token;
    }

    protected function httpGet($url, array $headers = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'from sociallogin sdk');
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        if (empty($info['http_code']) || $info['http_code'] != 200) {
            throw new Exception('', self::HTTP_ERROR);
        }

        return $result;
    }

    protected function httpPost($url, $params)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::HTTP_CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::HTTP_TIMEOUT);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (empty($info['http_code']) || $info['http_code'] != 200) {
            throw new Exception('', self::HTTP_ERROR);
        }

        return $result;
    }

}
