<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 19:18
 */

namespace backend\controllers\QiniuLivingActions;


use frontend\business\ClientQiNiuUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteClientAction extends Action
{
    public function run($relate_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($relate_id))
        {
            $rst['msg']='用户直播参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living_parameter = ClientQiNiuUtil::GetClientLivingParams($relate_id);

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
        return $this->controller->redirect('client_params');
    }
} 