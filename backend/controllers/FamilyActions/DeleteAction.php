<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 16:48
 */

namespace backend\controllers\FamilyActions;


use common\models\Family;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($family_id)
    {
        $rst=['code'=>'0','msg'=>''];
        if(empty($family_id))
        {
            $rst['msg']='家族长id不能为空';
            echo json_encode($rst);
            exit;
        }
        $Family = Family::findOne(['family_id'=>$family_id]);
        if(!isset($Family))
        {
            $rst['msg']='家族长账号信息不存在';
            echo json_encode($rst);
            exit;
        }

        if($Family->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($Family->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        echo json_encode($rst);
        exit;
    }
} 