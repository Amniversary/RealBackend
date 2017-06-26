<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/24
 * Time: 15:46
 */

namespace frontend\business\RongCloud;


class ChatRoomUtil
{

    private $chatRoom = null;

    /**
     * @return \common\components\rongcloudsdk\methods\Chatroom
     */
    public function newRoomClass()
    {
        if(empty($this->chatRoom)){
            $this->chatRoom = \Yii::$app->im->Chatroom();
        }
        return $this->chatRoom;
    }

    /**
     * TODO: 创建聊天室
     * @param $livingId
     * @param $error
     * @return bool
     */
    public function createRoom($livingId,&$error)
    {
        if(empty($livingId)){
            $error = 'livingId params is Empty';
            return false;
        }
        $roomClass = $this->newRoomClass();
        if(!$roomClass->create($livingId)){
            $error = 'code:'.$roomClass->getErrorCode().' msg:'.$roomClass->getErrorMessage();
            return false;
        }
        return true;
    }
} 