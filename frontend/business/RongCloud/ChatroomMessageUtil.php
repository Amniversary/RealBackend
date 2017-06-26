<?php
/**
 * 发送系统消息
 */

namespace frontend\business\RongCloud;

use common\models\ChatRoom;
use common\models\ChatRoomMember;
use common\models\Living;
use yii\log\Logger;

class ChatroomMessageUtil
{
    use MessageTrait;

    const OBJECT_NAME_OTHER = 'MB:other';
    const OBJECT_NAME_SUPER = 'MB:super';
    // 系统发IM消息的id
    const SYSTEM_ID = -1;

    const MSG_SEND_ATTENTION_MSG = '我关注了主播，再也不怕迷路了';
    const MSG_SEND_SHOW_MSG = '我分享了直播';
    const MSG_SEND_JOIN_MSG = '进入直播间';
    const MSG_SEND_LEAVE_MSG = '离开直播间';
    const MSG_SEND_ATTENTION_TAG = 301;
    const MSG_SEND_SHOW_TAG = 304;
    const MSG_SEND_LEAVE_TAG = 305;
    const MSG_SEND_JOIN_TAG = 306;
    const MSG_SEND_PROPLE_COUNT_TAG = 307;
    const MSG_SEND_SYSTEM_TAG = 308;
    const MSG_NOSPEAKING_TAG = 101;
    const MSG_BANCLIENT_TAG = 104;
    const MSG_PEOPLE_COUNT_TAG = 307;
    const MSG_MANAGER_TAG = 107;

    /**
     * todo: 聊天室禁言
     * @param int $userId 用户的client_id
     * @param int $adminId 操作人的client_id
     * @param int $livingId 直播间的living_id
     * @param int $minute 禁言时间，单位分钟，0表示取消禁言，最大值为43200分钟
     * @param bool $isSystemAdministrator 是否管理员操作
     * @throws \Exception
     */
    public function setNospeakingInChatroom($userId, $adminId, $livingId, $minute = 300, $isSystemAdministrator = false)
    {
        /**
         * @var ChatRoom $chatRoomModel
         */
        $chatRoomModel = ChatRoom::findOne(
            ['living_id' => $livingId]
        );
        if (false === $chatRoomModel) {
            throw new \Exception('群组信息不存在');
        }
        // IM对应的聊天室id为room_id
        $chatroomId = $chatRoomModel->getAttribute('room_id');

        /**
         * @var ChatRoomMember $chatRoomMemberModel
         */
        $chatRoomMemberModel = ChatRoomMember::findOne([
            'group_id' => $chatroomId,
            'user_id'  => $adminId,
        ]);

        // 判断是否有禁言权限
        if (!$isSystemAdministrator) {
            if (empty($chatRoomMemberModel) || $chatRoomMemberModel->getAttribute('ower') == 3) {
                throw new \Exception('操作人不是管理人员');
            }
        }

        // 调用IM接口禁言
        $chatroomManager = \Yii::$app->im->Chatroom();
        if (!$chatroomManager->addGagUser($userId, $livingId, $minute)) {
            throw new \Exception($chatroomManager->getErrorMessage());
        }
    }

    /**
     * todo: 发送直播间人数/欢迎
     * @param $chatroomId
     * @param $extra
     * @param null $tag
     * @return bool|string
     */
    public function sendChatroomOtherMsg($chatroomId, $extra, $tag = null)
    {
        // 已系统消息发送
        $userId = self::SYSTEM_ID;
        empty($tag) && $tag = self::MSG_PEOPLE_COUNT_TAG;
        $messageManager = $this->getMessageManager();
        if (!$messageManager->publishChatroom(
            $userId, $chatroomId,
            self::OBJECT_NAME_OTHER,
            $this->filteContent('', $extra, $tag)
        )) {
            return $messageManager->getErrorMessage();
        };
        return true;
    }

    /**
     * todo: 发送直播间系统消息
     * @param $chatroomId
     * @param $extra
     * @param $tag
     * @param string $content
     * @return bool|string
     */
    public function sendChatroomSuperMsg($chatroomId, $extra, $tag, $user = [], $message = '')
    {
        // 已系统消息发送
        $userId = self::SYSTEM_ID;
        $messageManager = $this->getMessageManager();
        if (!$messageManager->publishChatroom(
            $userId, $chatroomId,
            self::OBJECT_NAME_SUPER,
            $this->filteContent($message, $extra, $tag, $user)
        )) {
            return $messageManager->getErrorMessage();
        };
        return true;
    }

    /**
     * todo: 禁用发送的im消息，让用户停止直播
     * @param int $userId
     * @param int $chatroomId living_id
     * @param array $extra
     * @return string|true
     */
    public function sendBanClientMessage($chatroomId, $extra, $user = [], $message = '')
    {
        return $this->sendChatroomSuperMsg(
            $chatroomId, $extra,
            self::MSG_BANCLIENT_TAG,
            $user, $message
        );
    }
}
