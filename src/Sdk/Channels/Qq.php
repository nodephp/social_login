<?php
namespace Social\SocialLogin\Sdk\Channels;

use Exception;
use Social\SocialLogin\Sdk\Base;
use Social\SocialLogin\Sdk\SocialInterface;

class Qq extends Base implements SocialInterface
{
    protected $authorizeUrls = array(
        'default' => 'https://graph.qq.com/oauth2.0/authorize',
        'mobile' => 'https://graph.z.qq.com/moc2/authorize',
    );

    protected $tokenUrls = array(
        'default' => 'https://graph.qq.com/oauth2.0/token',
        'mobile' => 'https://graph.z.qq.com/moc2/token',
    );
    protected $openIdUrl = 'https://graph.qq.com/oauth2.0/me';
    protected $infoUrl = 'https://graph.qq.com/user/get_user_info';

    public function getAccessToken($code)
    {
        $this->display = 'mobile';
        $tokenUrl = empty($this->tokenUrls[$this->display]) ? $this->tokenUrls['default'] : $this->tokenUrls[$this->display];
        $params = array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
        );

        $token = array();
        $result = $this->httpGet($tokenUrl . '?' . http_build_query($params));
        if (strpos($result, "callback") !== false) {
            $lpos = strpos($result, "(");
            $rpos = strrpos($result, ")");
            $token = json_decode(substr($result, $lpos + 1, $rpos - $lpos - 1), true);
        } else {
            parse_str($result, $token);
        }

        if (empty($token['access_token'])) {
            $this->setLastError($token['error'], $token['error_description']);
            throw new Exception('获取token失败', parent::ERROR);
        }

        $token['timeout'] = empty($token['expires_in']) ? 0 : time() + $token['expires_in'];
        $this->token = $token;
        return $token;
    }

    public function getOpenId($token = [])
    {
        if ($token) {
            $this->token = $token;
        }
        if (!$this->token) {
            throw new Exception('', parent::TOKEN_ERROR);
        }
        $user = array();
        $str = $this->httpGet($this->openIdUrl . '?access_token=' . $this->token['access_token']);
        if (strpos($str, "callback") !== false) {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
            $user = json_decode($str, true);
        } else {
            parse_str($str, $user);
        }

        if (!isset($user['openid'])) {
            throw new Exception('', parent::INFO_ERROR);
        }
        $this->openId = $user['openid'];
        return $user['openid'];
    }

    public function getUserInfo($token = [])
    {
        if ($token) {
            $this->getOpenId($token);
        }
        if (!$this->token) {
            throw new Exception('', parent::TOKEN_ERROR);
        }

        $userInfoUrl = $this->infoUrl . '?'
        . 'access_token=' . $this->token['access_token']
        . '&oauth_consumer_key=' . $this->appid
        . '&openid=' . $this->token['openid']
            . '&format=json';

        $result = $this->httpGet($userInfoUrl);
        if (!$result) {
            throw new Exception('', parent::HTTP_ERROR);
        }
        $info = json_decode($result, true);
        if (!isset($info['ret']) || $info['ret'] != 0) {
            throw new Exception('', parent::INFO_ERROR);
        }
        $this->userInfo = $info;
        $gender = array(
            '男' => 1,
            '女' => 2,
        );
        $userInfo = array(
            'nick' => $info['nickname'],
            'face' => empty($info['figureurl_qq_2']) ? $info['figureurl_qq_1'] : $info['figureurl_qq_2'],
            'sex' => isset($gender[$info['gender']]) ? $gender[$info['gender']] : 0,
            'openid' => $this->openId,
        );

        return $userInfo;
    }

}
