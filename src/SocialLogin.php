<?php
namespace Social;

class SocialLogin
{
    private $type = '';
    private $classMap = array(
        'qq' => 'Qq',
        'weibo' => 'Weibo',
        'wx' => 'Wx',
    );

    public function __construct(array $config = array(), $type = '')
    {
        $this->type = strtolower(empty($type) ? $_GET['type'] : $type);
        $this->configs = $config;
    }

    private function getInstance()
    {
        if (empty($this->classMap[$this->type])) {
            throw new Exception('', 1);
        }
        if (empty($this->configs[$this->type])) {
            throw new Exception('', 1);
        }
        $class = "Social\\SocialLogin\\Sdk\Channels\\{$this->classMap[$this->type]}";
        $this->obj = new $class($this->configs[$this->type]);
        return $this->obj;
    }

    public function authAndGetUserInfo($code)
    {
        $this->getInstance();
        $result = $this->obj->authAndGetUserInfo($code);
        return $result;
    }

    public function getOriginalUserInfo()
    {
        if (empty($this->obj)) {
            return array();
        }
        return $this->obj->getOriginalUserInfo();
    }
}
