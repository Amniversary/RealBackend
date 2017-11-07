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
use yii\base\Exception;
use yii\web\HttpException;

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
        $MethodName = !empty($header['MethodName']) ? $header['MethodName'] : '';
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
        $count = count($Server[$ServerName]);
        //TODO: 从配置列表中随机获取
        $request = $Server[$ServerName]['default'][mt_rand(0, $count-1)];
        if(array_key_exists($MethodName, $Server[$ServerName])) {
            $request = $Server[$ServerName][$MethodName]['default'][mt_rand(0, $count-1)];
        }
        $resources = file_get_contents('php://input');
        $result = '';
        $headers = [];
        $headerConfig = ['servername', 'methodname', 'x-wx-code', 'x-wx-encrypted-data', 'x-wx-iv', 'x-wx-id', 'x-wx-skey', 'appid', 'openid', 'userid'];
        foreach($header as $item => $v) {
            if(!in_array($item, $headerConfig)) continue;
            $headers[] = "$item:$v[0]";
        }
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':                                       //TODO: 将header参数带上
                $result = UsualFunForNetWorkHelper::HttpGet($request, $headers);
                break;
            case 'POST':
                $result = UsualFunForNetWorkHelper::HttpsPost($request, $resources, $headers);
                break;
            case 'OPTIONS':
                \Yii::$app->response->statusCode = 200;
                break;
        }
        $end = round(microtime(TRUE) * 1000);
        if(empty($result)) {
            $rst['code'] = 10001;
            $rst['msg'] = '系统错误';
            echo json_encode($rst);
            exit;
        }
//        \Yii::error(sprintf('socket 时间:%sms, url : %s', $end - $begin, $request));
        return $result;
    }


    //TODO: 验证协议参数
    private function CheckParams(&$error)
    {
        $header = \Yii::$app->request->headers;
        $paramsName = ['ServerName'];
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