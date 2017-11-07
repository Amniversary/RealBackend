<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/20
 * Time: 下午8:33
 */

namespace frontend\api\version\Notice;


use frontend\api\IApiExecute;
use frontend\components\ChatTunnelHandler;
use QCloud_WeApp_SDK\Tunnel\TunnelService;

class SendBroadcast implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if(!$this->check_params($dataProtocol, $error)) return false;
        $tunnelIds = $dataProtocol['data']['tunnelIds'];
        $type = $dataProtocol['data']['type'];
        $content = $dataProtocol['data']['content'];
        $result = TunnelService::broadcast($tunnelIds, $type, $content);
        \Yii::error('result:'.var_export($result,true));
        if($result['code'] !== 0 && !empty($result['data']['invalidTunnelIds'])) {
            $invalidTunnelIds = $result['data']['invalidTunnelIds'];
            $cache = \Yii::$app->cache;
            foreach($invalidTunnelIds as $tunnelId) {
                $index = array_search($tunnelId, $tunnelIds);
                if($index !== false) {
                    $cache->delete('UserTunnel_'.$index);
                    $cache->delete($tunnelId);
                }
            }
        }
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['tunnelIds', 'type', 'content'];
        $filesLabel = ['信道id', '消息类型', '消息内容'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error = $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}