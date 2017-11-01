<?php
namespace frontend\components;

use \QCloud_WeApp_SDK\Tunnel\ITunnelHandler as ITunnelHandler;
use \QCloud_WeApp_SDK\Tunnel\TunnelService as TunnelService;

/**
 * 实现 WebSocket 信道处理器
 * 本示例配合客户端 Demo 实现一个简单的聊天室功能
 */
class ChatTunnelHandler implements ITunnelHandler {
    /**
     * 实现 onRequest 方法
     * 在客户端请求 WebSocket 信道连接之后，
     * 会调用 onRequest 方法，此时可以把信道 ID 和用户信息关联起来
     */
    public function onRequest($tunnelId, $userInfo) {
        if (is_array($userInfo)) {
//            $data = self::loadData($userInfo['id']);
            // 保存 信道ID => 用户信息 的映射
            $userInfo['tunnelId'] = $tunnelId;
            $data = $userInfo;
            self::saveData($data);
        }
    }

    /**
     * 实现 onConnect 方法
     * 在客户端成功连接 WebSocket 信道服务之后会调用该方法，
     * 此时通知所有其它在线的用户当前总人数以及刚加入的用户是谁
     */
    public function onConnect($userId) {
//        $tunnelIds = self::loadTunnelIds();
        $data = self::loadData($userId);
        if (!empty($data)) {
//            $tunnelIds[] = $data['tunnelId'];
//            self::SaveTunnelIds($tunnelIds);
//            self::broadcast('people', ['total' => 1, 'enter' => $data]);
        } else {
            \Yii::error("Unknown tunnelId({$data['tunnelId']}) was connectd, close it");
            self::closeTunnel($data);
        }
    }

    /**
     * 实现 onMessage 方法
     * 客户端推送消息到 WebSocket 信道服务器上后，会调用该方法，此时可以处理信道的消息。
     * 在本示例，我们处理 `speak` 类型的消息，该消息表示有用户发言。
     * 我们把这个发言的信息广播到所有在线的 WebSocket 信道上
     */
    public function onMessage($userId, $type, $content) {
        $data = self::loadData($userId);
        if (!empty($data)) {
            self::broadcast($type, ['who' => $data, 'word' => $content['word']]);
        } else {
            self::closeTunnel($data);
        }
    }

    /**
     * 实现 onClose 方法
     * 客户端关闭 WebSocket 信道或者被信道服务器判断为已断开后，
     * 会调用该方法，此时可以进行清理及通知操作
     */
    public function onClose($userId) {
        $data = self::loadData($userId);
        $tunnelId = $data['tunnelId'];
        if (empty($data)) {
            \Yii::error('[onClose] 无效的信道 ID =>'. $userId);
            self::closeTunnel($data);
            return;
        }

        \Yii::$app->redis->del('UserTunnel_'. $userId);
        $tunnelIds = self::loadTunnelIds();
        $index = array_search($tunnelId, $tunnelIds);
        if ($index !== FALSE) {
            array_splice($data, $index, 1);
        }

        self::SaveTunnelIds($tunnelIds);

        // 聊天室没有人了（即无信道ID）不再需要广播消息
//        if (count($data['connectedTunnelIds']) > 0) {
//            self::broadcast('people', array(
//                'total' => count($data['connectedTunnelIds']),
//                'leave' => $leaveUser,
//            ));
//        }
    }

    /**
     * 调用 TunnelService::broadcast() 进行广播
     */
    private static function broadcast($type, $content) {
        $tunnelIds = self::loadTunnelIds();
//        $data = self::loadData($content['enter']['id']);
        $result = TunnelService::broadcast($tunnelIds, $type, $content);

        if ($result['code'] === 0 && !empty($result['data']['invalidTunnelIds'])) {
            $invalidTunnelIds = $result['data']['invalidTunnelIds'];
            //debug('检测到无效的信道 IDs =>', $invalidTunnelIds);

            // 从`userMap`和`connectedTunnelIds`将无效的信道记录移除
            foreach ($invalidTunnelIds as $tunnelId) {
//                \Yii::$app->redis->del('UserTunnel_'.);//$data['userMap'][$tunnelId]

                $index = array_search($tunnelId, $tunnelIds);
                if ($index !== FALSE) {
                    array_splice($tunnelIds, $index, 1);
                }
            }
            self::SaveTunnelIds($tunnelIds);
//            self::saveData($data);
        }
    }

    /**
     * 调用 TunnelService::closeTunnel() 关闭信道
     * @param  String $tunnelId 信道ID
     */
    private static function closeTunnel($data) {
        TunnelService::closeTunnel($data['tunnelId']);
        \Yii::$app->redis->del('UserTunnel_'.$data['id']);
    }

    /**
     * 加载 WebSocket 信道对应的用户 => userMap
     * 加载 当前已连接的 WebSocket 信道列表 => connectedTunnelIds
     * 在实际的业务中，应该使用数据库进行存储跟踪，这里作为示例只是演示其作用
     */
    private static function loadData($userId) {
        $redis = \Yii::$app->redis;
        $defaultData = [];
        if(!$redis->exists('UserTunnel_'.$userId)){
            return $defaultData;
        }
        $data = $redis->get('UserTunnel_'.$userId);
        $data = json_decode($data,true);
        return (is_array($data) ? $data : $defaultData);
    }

    /**
     * 加载 WebSocket 已经连接的信道列表
     */
    private static function loadTunnelIds()
    {
        $redis = \Yii::$app->redis;
        $defaultData = [];
        if(!$redis->exists('TunnelList')) {
            return $defaultData;
        }
        $data = $redis->get('TunnelList');
        $data = json_decode($data,true);
        return (is_array($data) ? $data : []);
    }

    /**
     * 保存 WebSocket 信道对应的用户 => userMap
     * 保存 当前已连接的 WebSocket 信道ID列表 => connectedTunnelIds
     * 在实际的业务中，应该使用数据库进行存储跟踪，这里作为示例只是演示其作用
     */
    private static function saveData($data) {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $content = json_encode($data);
        }
        \Yii::$app->redis->set('UserTunnel_'.$data['id'], 60 * 60 * 24, $content);
    }

    /**
     * 保存 WebSocket 已经连接的信道列表
     * @param $tunnelIds
     */
    private static function SaveTunnelIds($tunnelIds)
    {
        $saveData = json_encode($tunnelIds);
        \Yii::$app->redis->set('TunnelList', $saveData);
    }
}
