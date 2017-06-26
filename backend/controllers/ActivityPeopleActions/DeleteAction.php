<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/27
 * Time: 11:35
 */

namespace backend\controllers\ActivityPeopleActions;


use common\models\ActivityPeople;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='预设主播id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Activity = ActivityPeople::findOne(['record_id'=>$record_id]);
        if(!isset($Activity))
        {
            $rst['msg']='预设主播信息不存在';
            echo json_encode($rst);
            exit;
        }

        if($Activity->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($Activity->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        echo json_encode($rst);
        exit;
    }
} 