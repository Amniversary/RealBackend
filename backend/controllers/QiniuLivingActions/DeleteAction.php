<?php
namespace backend\controllers\QiniuLivingActions;


use backend\business\LivingParameterUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteAction extends Action
{
    public function run($quality_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($quality_id))
        {
            $rst['msg']='直播参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living_parameter = LivingParameterUtil::GetLivingParameterById($quality_id);
        if(!isset($living_parameter))
        {
            $rst['msg']='直播参数不存在';
            echo json_encode($rst);
            exit;
        }

        if($living_parameter->delete() === false)
        {
            $rst['msg']='删除失败';
            \Yii::getLogger()->log('删除失败:'.var_export($living_parameter->getErrors(),true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        return $this->controller->redirect('living_parameters');
    }
}