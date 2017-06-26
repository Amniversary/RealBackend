<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 13:55
 */
namespace backend\controllers\AdvertiseActions;


use common\models\Advertise;
use yii\base\Action;

class DeleteAction extends Action
{
    public function run($id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($id))
        {
            $rst['msg']='广告id不能为空';
            echo json_encode($rst);
            exit;
        }
        $advertise =  Advertise::findOne(['id'=>$id]);
        if(!isset($advertise))
        {
            $rst['msg']='广告不存在';
            echo json_encode($rst);
            exit;
        }

        if($advertise->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($advertise->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        echo json_encode($rst);
        exit;
    }

}