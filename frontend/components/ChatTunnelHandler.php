<?php
namespace frontend\components;

use common\components\UsualFunForNetWorkHelper;
use \QCloud_WeApp_SDK\Tunnel\ITunnelHandler as ITunnelHandler;
use \QCloud_WeApp_SDK\Tunnel\TunnelService as TunnelService;

/**
 * 实现 WebSocket 信道处理器
 */
class ChatTunnelHandler implements ITunnelHandler
{
    /**
     * 实现 onRequest 方法
     * 在客户端请求 WebSocket 信道连接之后，
     * 会调用 onRequest 方法，此时可以把信道 ID 和用户信息关联起来
     */
    public function onRequest($tunnelId, $userInfo)
    {
        if (is_array($userInfo)) {
            $userInfo['tunnelId'] = $tunnelId;
            $data = $userInfo;
            self::saveData($data);
        }
    }

    /**
     * 实现 onConnect 方法
     * 在客户端成功连接 WebSocket 信道服务之后会调用该方法，
     */
    public function onConnect($tunnelId)
    {
        $data = self::loadTunnelIds($tunnelId);
        if (!empty($data)) {

        } else {
            \Yii::error("Unknown tunnelId({$data['tunnelId']}) was connectd, close it");
            self::closeTunnel($data);
        }
    }

    /**
     * 实现 onMessage 方法
     * 客户端推送消息到 WebSocket 信道服务器上后，会调用该方法，此时可以处理信道的消息.
     * 此方法处理客户端推送到 WebSocket 上的消息.
     */
    public function onMessage($tunnelId, $type, $content)
    {
        $data = self::loadTunnelIds($tunnelId);
        if (!empty($data)) {
            switch ($type) {
                case 'socket':
                    $rst = self::socket(json_decode($content, true));
                    $result = TunnelService::emit($data['tunnelId'], $type, $rst);
                    \Yii::error(' onMessage回调 : ' . var_export($result, true));
                    break;
                case 'broadcast':
                    break;
            }
        } else {
            self::closeTunnel($data);
        }
    }

    /**
     * 实现 onClose 方法
     * 客户端关闭 WebSocket 信道或者被信道服务器判断为已断开后，
     * 会调用该方法，此时可以进行清理及通知操作
     */
    public function onClose($tunnelId)
    {
        $data = self::loadTunnelIds($tunnelId);
        $tunnelId = $data['tunnelId'];
        if (empty($data)) {
            \Yii::error('[onClose] 无效的信道 ID =>' . $data['tunnelId'] . ' userId : ' . $data['id']);
            self::closeTunnel($data);
            return;
        }
        \Yii::$app->cache->delete('UserTunnel_' . $data['id']);
        \Yii::$app->cache->delete($tunnelId);
    }

    /**
     * 调用 TunnelService::broadcast() 进行广播
     */
    private static function broadcast($type, $content)
    {
        $data = self::loadTunnelIds($content['who']['data']['tunnelId']);
        $result = TunnelService::emit($data['tunnelId'], $type, $content);
        \Yii::error('广播回调:' . var_export($result, true));
        if ($result['code'] === 0 && !empty($result['data']['invalidTunnelIds'])) {
            $invalidTunnelIds = $result['data']['invalidTunnelIds'];
            //debug('检测到无效的信道 IDs =>', $invalidTunnelIds);

            // 从`userMap`和`connectedTunnelIds`将无效的信道记录移除
//            foreach ($invalidTunnelIds as $tunnelId) {
////                \Yii::$app->redis->del('UserTunnel_'.);//$data['userMap'][$tunnelId]
//
//                $index = array_search($tunnelId, $tunnelIds);
//                if ($index !== FALSE) {
//                    array_splice($tunnelIds, $index, 1);
//                }
//            }
//            self::SaveTunnelIds($tunnelIds);
////            self::saveData($data);
        }
    }

    /**
     * 调用 TunnelService::closeTunnel() 关闭信道
     * @param  String $tunnelId 信道ID
     */
    private static function closeTunnel($data)
    {
        TunnelService::closeTunnel($data['tunnelId']);
        \Yii::$app->cache->delete('UserTunnel_' . $data['id']);
        \Yii::$app->cache->delete($data['tunnelId']);
    }

    /**
     * 加载 WebSocket 信道对应的用户 => userMap
     * 加载 当前已连接的 WebSocket 信道列表 => connectedTunnelIds
     * 在实际的业务中，应该使用数据库进行存储跟踪，这里作为示例只是演示其作用
     */
    private static function loadData($userId)
    {
        $cache = \Yii::$app->cache;
        $defaultData = [];
        $cache->get('UserTunnel_' . $userId);
        if (!$cache->get('UserTunnel_' . $userId)) {
            return $defaultData;
        }
        $data = $cache->get('UserTunnel_' . $userId);
        $data = json_decode($data, true);
        return (is_array($data) ? $data : $defaultData);
    }

    /**
     * 加载 WebSocket 已经连接的信道列表
     */
    private static function loadTunnelIds($tunnelId)
    {
        $cache = \Yii::$app->cache;
        $defaultData = [];
        if (!$cache->get($tunnelId)) {
            return $defaultData;
        }
        $data = $cache->get($tunnelId);
        $data = json_decode($data, true);
        return (is_array($data) ? $data : []);
    }

    /**
     * 保存 WebSocket 信道对应的用户 => userMap
     * 保存 当前已连接的 WebSocket 信道ID列表 => connectedTunnelIds
     * 在实际的业务中，应该使用数据库进行存储跟踪，这里作为示例只是演示其作用
     */
    private static function saveData($data)
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $content = json_encode($data);
        }
        \Yii::$app->cache->set($data['tunnelId'], $content, 60 * 60 * 24);
        \Yii::$app->cache->set('UserTunnel_' . $data['id'], $content, 60 * 60 * 24);
    }


    private static function socket($data)
    {
        $rst = ['code' => 1, 'msg' => '', 'data' => ''];
        if (!self::CheckParams($data, $error)) { //TODO: 检测请求参数
            $rst['msg'] = $error;
            return json_encode($rst, JSON_UNESCAPED_UNICODE);
        }
        $ServerName = $data['servername'];
        $MethodName = !empty($data['methodname']) ? $data['methodname'] : '';
        $ConfigFile = \Yii::$app->getBasePath() . '/config/ServersConfig.php';
        if (!file_exists($ConfigFile)) {   //TODO : 检测配置文件是否存在
            $rst['code'] = 10001;
            $rst['msg'] = '找不到对应的服务器配置文件';
            return json_encode($rst, JSON_UNESCAPED_UNICODE);
        }
        $Server = require($ConfigFile);
        if (!isset($Server[$ServerName])) {  //TODO: 请求中的服务名是否存在
            $rst['code'] = 10001;
            $rst['msg'] = '配置文件错误, 找不到对应服务名';
            \Yii::error($rst['msg'] . ' ServerName :' . $ServerName);
            return json_encode($rst, JSON_UNESCAPED_UNICODE);
        }
        $count = count($Server[$ServerName]);
        $request = $Server[$ServerName]['default'][mt_rand(0, $count - 1)]; //TODO: 从配置列表中随机获取
        if (array_key_exists($MethodName, $Server[$ServerName])) {
            $request = $Server[$ServerName][$MethodName]['default'][mt_rand(0, $count - 1)];
        }
        $resources = json_encode($data['data'], JSON_UNESCAPED_UNICODE);
        unset($data['data']);
        $headers = [];
        $headerConfig = ['servername', 'methodname', 'x-wx-code', 'x-wx-encrypted-data', 'x-wx-iv', 'x-wx-id', 'x-wx-skey', 'appid', 'openid', 'userid'];
        foreach ($data as $item => $v) {
            if (!in_array($item, $headerConfig)) continue;
            $headers[] = "$item:$v";
        }
        $result = UsualFunForNetWorkHelper::HttpsPost($request, $resources, $headers);
        \Yii::error(' Socket请求 : ' . $result);
        \Yii::error(' Socket内容 : ' . var_export($data, true));
        if (empty($result)) {
            $rst['code'] = 10001;
            $rst['msg'] = '系统错误';
            return json_encode($rst, JSON_UNESCAPED_UNICODE);
        }
        $data['data'] = $result;
        return $data;
    }


    //TODO: 验证协议参数
    private static function CheckParams($dataProtocol, &$error)
    {
        $paramsName = ['servername', 'methodname', 'userid', 'data'];
        $count = count($paramsName);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($dataProtocol[$paramsName[$i]]) || empty($dataProtocol[$paramsName[$i]])) {
                $error .= $paramsName[$i] . ' params cannot be empty.';
                return false;
            }
        }
        return true;
    }
}
