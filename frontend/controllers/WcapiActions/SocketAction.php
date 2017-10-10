<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/23
 * Time: 下午4:22
 */

namespace frontend\controllers\WcapiActions;


use common\components\UsualFunForNetWorkHelper;
use yii\base\Action;

class SocketAction extends Action
{
    public function run()
    {
        $begin = round(microtime(TRUE) * 1000);
        $rst = ['code' => 1, 'msg' => '', 'data' => ''];
        if (!$this->CheckParams($error)) {   //TODO: 检测请求参数
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $header = \Yii::$app->request->headers;
        $ServerName = $header['ServerName'];
        $OpenId = $header['OpenId'];
        $AppId = $header['AppId'];
        $MethodName = !empty($header['MethodName']) ? $header['MethodName'] : 'url';
        $ConfigFile = \Yii::$app->getBasePath() . '/config/ServersConfig.php';
        if (!file_exists($ConfigFile)) {   //TODO : 检测配置文件是否存在
            $rst['code'] = 10001;
            $rst['msg'] = '找不到对应的服务器配置文件';
            echo json_encode($rst);
            exit;
        }
        $Server = require($ConfigFile);
        if(!isset($Server[$ServerName])) {  //TODO: 请求中的服务名是否存在
            $rst['code'] = 10001;
            $rst['msg'] = '配置文件错误, 找不到对应服务名';
            \Yii::error($rst['msg'] .  ' ServerName :'. $ServerName);
            echo json_encode($rst);
            exit;
        }
        if(!isset($Server[$ServerName][$MethodName])) {
            $rst['code'] = 10001;
            $rst['msg'] = '配置文件错误, 找不到对应扩展服务名';
            \Yii::error($rst['msg'] . ' MethodName :' . $MethodName);
            echo json_encode($rst);
            exit;
        }
        $request = $Server[$ServerName][$MethodName];
        $count = count($request);
        $resources = file_get_contents('php://input');
        $result = '';
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':                        //TODO: 从配置列表中随机获取( 暂时处理 )      将Header头中的OpenId 和AppId 带过去
                $result = UsualFunForNetWorkHelper::HttpGet($request[mt_rand(0,$count-1)], ["OpenId:$OpenId", "AppId:$AppId"]);
                break;
            case 'POST':
                $result = UsualFunForNetWorkHelper::HttpsPost($request[mt_rand(0,$count-1)], $resources, ["OpenId:$OpenId", "AppId:$AppId"]);
                break;
            case 'OPTIONS':
                \Yii::$app->response->statusCode = 200;
                break;
        }
        $end = round(microtime(TRUE) * 1000);
        \Yii::error(sprintf('socket 时间:%sms', $end - $begin));
        return $result;
    }


    //TODO: 验证协议参数
    private function CheckParams(&$error)
    {
        $header = \Yii::$app->request->headers;
        $paramsName = ['ServerName', 'OpenId', 'AppId'];
        $count = count($paramsName);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($header[$paramsName[$i]]) || empty($header[$paramsName[$i]])) {
                $error .= $paramsName[$i] . ' params cannot be empty.';
                return false;
            }
        }
        return true;
    }
}