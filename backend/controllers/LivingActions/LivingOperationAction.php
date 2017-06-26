<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/18
 * Time: 11:53
 */

namespace backend\controllers\LivingActions;

use backend\business\CloseLivingLogUtil;
use common\components\ClearCacheHelper;
use common\models\CloseLivingLog;
use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use yii\base\Action;
use yii\db\Query;
use yii\log\Logger;

class LivingOperationAction extends Action
{

    public function run($living_id,$type,$seal_reason)
    {
        $rst = ['code'=>'1','msg'=>''];
        if(!isset($type))
        {
            $rst['msg'] = '无直播间操作类型';
            return $rst;
        }

        //类型为2 则是禁播 如果有禁播原因，则向用户发送im消息
        if($type == 2)
        {
            //关闭直播
            self::closeLive($living_id,$type);

            //这里封播用户
            LivingUtil::SealLiving($living_id,2);
        }

        //类型为1 则是禁号 如果有禁号原因，则向用户发送im消息
        if($type == 1)
        {
            //关闭直播
            self::closeLive($living_id,$type);

            $living = LivingUtil::GetLivingById($living_id);
            //这里禁用用户
            $client = ClientUtil::GetClientById($living->living_master_id);
            if(!isset($client))
            {
                $rst['message'] = '用户记录不存在';
                echo json_encode($rst);
                exit;
            }
            $client->status = 0;


            if(!ClientUtil::SetBanUser($client,$seal_reason,$error))
            {
                $rst['message'] = $error;
                echo json_encode($rst);
                exit;
            }


        }
    }

    public function closeLive($living_id,$type)
    {
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
            if($type == 1)
            {
                echo(\yii\helpers\Json::encode(array('code'=>0)));
            }

        }else{
            \Yii::getLogger()->log('关闭直播是发生了错误',Logger::LEVEL_ERROR);
            echo(\yii\helpers\Json::encode(array('code'=>1)));
        }
    }
}