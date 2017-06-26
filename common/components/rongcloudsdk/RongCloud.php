<?php
/**
 * 融云 Server API PHP 客户端
 * create by kitName
 * create datetime : 2016-09-05 
 * 
 * v2.0.1
 */

namespace common\components\rongcloudsdk;

use common\components\rongcloudsdk\SendRequest;
use common\components\rongcloudsdk\methods\User;
use common\components\rongcloudsdk\methods\Message;
use common\components\rongcloudsdk\methods\Wordfilter;
use common\components\rongcloudsdk\methods\Group;
use common\components\rongcloudsdk\methods\Chatroom;
use common\components\rongcloudsdk\methods\Push;
use common\components\rongcloudsdk\methods\SMS;

class RongCloud
{
    public $appKey;

    public $appSecret;

    public $format = 'json';

    private $SendRequest;

    public function getSendRequest() {
        if (empty($this->SendRequest)) {
            $this->SendRequest = new SendRequest(
                $this->appKey,
                $this->appSecret,
                $this->format
            );
        }
        return $this->SendRequest;
    }
    
    public function User() {
        $User = new User($this->getSendRequest());
        return $User;
    }
    
    public function Message() {
        $Message = new Message($this->getSendRequest());
        return $Message;
    }
    
    public function Wordfilter() {
        $Wordfilter = new Wordfilter($this->getSendRequest());
        return $Wordfilter;
    }
    
    public function Group() {
        $Group = new Group($this->getSendRequest());
        return $Group;
    }
    
    public function Chatroom() {
        $Chatroom = new Chatroom($this->getSendRequest());
        return $Chatroom;
    }
    
    public function Push() {
        $Push = new Push($this->getSendRequest());
        return $Push;
    }
    
    public function SMS() {
        $SMS = new SMS($this->getSendRequest());
        return $SMS;
    }
}