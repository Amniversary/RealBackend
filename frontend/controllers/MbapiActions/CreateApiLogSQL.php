<?php
namespace frontend\controllers\MbapiActions;

use frontend\business\ApiLogUtil;
use frontend\business\JobUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 *   向api_log表插入数据
 * Class CreateApiLogSQL
 * @package frontend\controllers\MbapiActions
 */
class CreateApiLogSQL extends Action
{
    public function run()
    {
//        $cache = \Yii::$app->cache->get('cache_ranstr');
        $rand_str = \Yii::$app->request->get('rand_str');
//        if(empty($cache) || $cache != $rand_str)
//        {
//            $error_msg = '系统错误';
//            echo $error_msg;
//            exit;
//        }
//        $del_cache = \Yii::$app->cache->delete('cache_ranstr');

        $ip=$_SERVER["REMOTE_ADDR"];
        $p_sign = \Yii::$app->request->get('p_sign');
        $time = \Yii::$app->request->get('time');
        if(empty($p_sign)  || empty($rand_str)  || empty($time))
        {
            $error_msg = '参数错误';
            echo $error_msg;
            exit;
        }
        $sign_param = [
            'rand_str' => $rand_str,
            'time' => $time,
        ];
        $sign = ApiLogUtil::GetApiLogSign($sign_param);
        $error_msg = 'ok';
        if($p_sign !== $sign)
        {
            $error_msg = '签名错误';
            echo $error_msg;
            exit;
        }
        if(!JobUtil::AddApiLogJob('create_api_log',['filename' => $time,'ip'=>$ip],$error)){
            $error_msg = 'Job添加失败';
        }
        echo $error_msg;
        exit;
    }
}