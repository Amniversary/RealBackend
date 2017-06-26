<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 16:20
 */

namespace backend\controllers\LivingActions;


use backend\components\ExitUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\log\Logger;

class LookLivingAction extends Action
{
    public function run($living_id)
    {
        $res = ['code'=>'1','msg'=>''];
        if(!isset($living_id))
        {
            $error = '直播id不存在';
            $res['msg'] = $error;
            echo json_encode($res);
            exit;
        }

        $living = LivingUtil::GetLivingById($living_id);
        if(!isset($living))
        {
            $res['msg'] = '直播间不存在';
            echo json_encode($res);
            exit;
        }
        $data = [
            'pull_http_url'=>$living->pull_http_url,
            'pull_rtmp_url'=>$living->pull_rtmp_url,
            'pull_hls_url'=>$living->pull_hls_url,
        ];
        //\Yii::getLogger()->log(var_export($living,true),Logger::LEVEL_ERROR);
        $res['code'] = '0';
        $res['msg'] = $data;
        echo json_encode($res);
    }
} 