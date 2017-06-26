<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 11:21
 */

namespace backend\controllers\LevelActions;


use common\models\Goods;
use common\models\LevelStage;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($level_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($level_id))
        {
            $rst['msg']='等级id不能为空';
            echo json_encode($rst);
            exit;
        }
        $levels = LevelStage::findOne(['level_no'=>$level_id]);
        if(!isset($levels))
        {
            $rst['msg']='等级信息不存在';
            echo json_encode($rst);
            exit;
        }

        if($levels->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($levels->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('/level/index');
    }
}