<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 14:44
 */

namespace backend\controllers\IntegralMallActions;


use common\models\IntegralMall;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($record_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($record_id))
        {
            $rst['msg']='商品id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Integral = IntegralMall::findOne(['record_id'=>$record_id]);
        if(!isset($Integral))
        {
            $rst['msg']='积分商品不存在';
            echo json_encode($rst);
            exit;
        }

        if($Integral->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($Integral->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
}