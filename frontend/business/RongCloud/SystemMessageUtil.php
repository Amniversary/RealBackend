<?php
/**
 * 发送系统消息
 */

namespace frontend\business\RongCloud;

class SystemMessageUtil
{
    use MessageTrait;

    // 消息类型
    const OBJECT_NAME = 'MB:super';

    // 系统消息
    const MSG_SEND_SYSTEM_ID = -1;

    // 通知消息
    const MSG_SEND_GENERAL_ID = -2;

    // 群系统通知消息
    const MSG_SEND_GROUP_ID = -3;

    // 系统消息 TAG
    const MSG_SEND_SYSTEM_TAG = 106;

    // 通知消息 TAG
    const MSG_SEND_GENERAL_TAG = 108;

    // 群系统通知消息 TAG
    const MSG_SEND_GROUP_TAG = 109;

    /**
     * todo: 发送通知消息
     */
    public function sendGeneralMessage($toUserId, $content, $extra = null)
    {
        $content = $this->filteContent($content, $extra, self::MSG_SEND_GENERAL_TAG);
        $this->send(self::MSG_SEND_GENERAL_ID, $toUserId, $content);
    }

    /**
     * todo: 发送系统消息
     */
    public function sendSystemMessage($toUserId, $content, $extra = null, $tag = null)
    {
        $tag = empty($tag) ? self::MSG_SEND_SYSTEM_TAG : $tag;
        $content = $this->filteContent($content, $extra, $tag);
        $this->send(self::MSG_SEND_SYSTEM_ID, $toUserId, $content);
    }

    /**
     * todo: 群系统通知消息
     */
    public function sendGroupMessage($toUserId, $content, $extra = null)
    {
        $content = $this->filteContent($content, $extra, self::MSG_SEND_GROUP_TAG);
        $this->send(self::MSG_SEND_GROUP_ID, $toUserId, $content);
    }

    /**
     * todo: 发送广播消息
     * @param $content
     * @param null|array $extra
     */
    public function sendBroadcastMessage($content, $pushContent, $extra = null)
    {
        $messageManager = \Yii::$app->im->Message();
        $content = $this->filteContent($content, $extra, self::MSG_SEND_SYSTEM_TAG);
        $rst = $messageManager->broadcast(self::MSG_SEND_SYSTEM_ID, self::OBJECT_NAME, $content, $pushContent);
        if (!$rst) {
            throw new \Exception($messageManager->getErrorMessage());
        }
    }

    private function send($fromUserId, $toUserId, $content)
    {
        $messageManager = \Yii::$app->im->Message();
        $rst = $messageManager->publishPrivate($fromUserId, $toUserId, self::OBJECT_NAME, $content);
        if (!$rst) {
            throw new \Exception($messageManager->getErrorMessage());
        }
    }
}
