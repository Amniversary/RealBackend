<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/9
 * Time: 下午6:09
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use Qiniu\Auth;

class UploadToken implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        $params = \Yii::$app->params['QiNiuOss'];
        $auth = new Auth($params['ak'],$params['sk']);
        $bucket = $params['bucket'];
        $token = $auth->uploadToken($bucket);
        $rstData['data'] = ['token'=> $token];
        return true;
    }
}