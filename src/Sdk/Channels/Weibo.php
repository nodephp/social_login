<?php
namespace Social\SocialLogin\Sdk\Channels;

use Exception;
use Social\SocialLogin\Sdk\Base;
use Social\SocialLogin\Sdk\SocialInterface;

class Weibo extends Base implements SocialInterface
{

    protected $tokenUrl = 'https://api.weibo.com/oauth2/access_token';
    protected $infoUrl = 'https://api.weibo.com/2/users/show.json';

    public function getAccessToken($code)
    {
        $this->display = 'mobile';

        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
        );
        $result = $this->httpPost($this->tokenUrl, $params);
        if (!$result) {
            throw new Exception('', parent::HTTP_ERROR);
        }
        $token = json_decode($result, true);
        if (empty($token['access_token'])) {
            throw new Exception('获取token失败', parent::ERROR);
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
        . '&uid=' . $this->token['uid'];

        $result = $this->httpGet($userInfoUrl);
        if (!$result) {
            throw new Exception('', parent::HTTP_ERROR);
        }

        $info = json_decode($result, true);
        if (!isset($info['idstr'])) {
            throw new Exception('', parent::INFO_ERROR);
        }

        $this->userInfo = $info;
        $gender = array(
            'm' => 1,
            'f' => 2,
            'n' => 0,
        );
        $userInfo = array(
            'openid' => $info['idstr'],
            'nick' => $info['name'],
            'face' => preg_replace('/\.50\//', '.180/', $info['profile_image_url']),
            'sex' => $gender[$info['gender']],
        );

        return $userInfo;
    }

}
