<?php

namespace Social\SocialLogin\Sdk\Channels;

use Exception;
use Social\SocialLogin\Sdk\Base;
use Social\SocialLogin\Sdk\SocialInterface;

class Wx extends Base implements SocialInterface
{

    protected $tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token';
    protected $infoUrl = 'https://api.weixin.qq.com/sns/userinfo';

    public function getAccessToken($code)
    {
        $params = array(
            'grant_type' => 'authorization_code',
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'code' => $code,
        );
        $token = array();
        // $result = $this->httpGet($this->tokenUrl . '?' . http_build_query($params));
        // if (!$result) {
        //     throw new Exception('', parent::HTTP_ERROR);
        // }
        //  $token = json_decode($result, true);
        $token = [
            "access_token" => "8_DKoMdEFKRoUDI-SbwZe-3xiuRjfWkoCdpaLjg7KIAMtfxwrv9kE5V28tdG9QxVqxlx552bQhb1QyFFE7l81e1zSDZUUDs9o2bh1t3_Ya41Q",
            "openid" => "oSUR-05z-c_BWc3TewZ-RRLqA484",
            "unionid" => "oaJJlwqJEqETGgm9Zve7bUktr8Yg",
        ];
        if (!isset($token['access_token']) || empty($token['access_token'])) {
            if (isset($token['errmsg'])) {
                throw new Exception($token['errmsg'], $token['errcode']);
            } else {
                throw new Exception('', parent::TOKEN_ERROR);
            }
        }

        $token['timeout'] = empty($token['expires_in']) ? 0 : time() + $token['expires_in'];
        $this->token = $token;
        return $token;
    }

    public function getUserInfo($token = [])
    {
        if ($token) {
            $this->token = $token;
        }
        if (!$this->token) {
            throw new Exception('', parent::TOKEN_ERROR);
        }
        $userInfoUrl = $this->infoUrl . '?'
        . 'access_token=' . $this->token['access_token']
        . '&openid=' . $this->token['openid'];

        $result = $this->httpPost($userInfoUrl, []);
        if (!$result) {
            throw new Exception('', parent::HTTP_ERROR);
        }
        $info = json_decode($result, true);

        if (!isset($info['openid'])) {
            throw new Exception('', parent::INFO_ERROR);
        }

        $this->userInfo = $info;
        $userInfo = [
            'openid' => $this->userInfo['unionid'],
            'nick' => $this->userInfo['nickname'],
            "sex" => $this->userInfo['sex'],
            'face' => $this->userInfo['headimgurl'],
        ];

        return $userInfo;
    }
}
