<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/21
 * Time: 13:04
 */

namespace backend\controllers\LivingActions;

use backend\business\CloseLivingLogUtil;
use common\models\CloseLivingLog;
use frontend\business\LivingUtil;
use common\models\Living;
use yii\base\Action;
use yii\log\Logger;
use yii\db\Query;
use common\components\ClearCacheHelper;
class CloseLiveAction extends Action
{

    public function run($living_id)
    {
        //$living =  Living::findOne(['living_id'=>$living_id]);


        $qurey = new Query();
        $qurey->from('mb_living li')->select(['li.living_before_id','li.living_id','living_master_id','cr.other_id','finish_time'])
            ->innerJoin('mb_chat_room cr','li.living_id = cr.living_id')
            ->where(['li.living_id'=>$living_id]);

        $living = $qurey->one();

        //禁用用户，直接结束直播
        $finishInfo = null;
        $finishInfo = null;
        if(LivingUtil::SetBanClientFinishLiving($living_id,$finishInfo,$living['living_master_id'],$living['other_id'],$outInfo,$error))
        {
            ClearCacheHelper::ClearHotLivingDataCache();
            $finish_time = $living['finish_time'];
            $now_time = date('Y-m-d H:i:s');
            \Yii::getLogger()->log('关闭直播：'.' living_id:'.$living['living_id'].$now_time.' finish_time:'.$finish_time,Logger::LEVEL_ERROR);

            //关闭直播日志表写入记录
            $close_living_model = new CloseLivingLog();
            $close_living_model->living_id = $living['living_id'];
            $close_living_model->living_before_id = $living['living_before_id'];
            $close_living_model->close_time = date('Y-m-d H:i:s');
            $close_living_model->backend_user_id = \Yii::$app->user->identity->id;
            $close_living_model->backend_user_name = \Yii::$app->user->identity->username;
            if(!CloseLivingLogUtil::CloseLivingLogSave($close_living_model,$error))
            {
                echo(\yii\helpers\Json::encode(array('code'=>1,'msg' => $error)));
                exit;
            }
            echo(\yii\helpers\Json::encode(array('code'=>0)));

        }else{
            \Yii::getLogger()->log('关闭直播是发生了错误',Logger::LEVEL_ERROR);
            echo(\yii\helpers\Json::encode(array('code'=>1)));
        }
    }
}