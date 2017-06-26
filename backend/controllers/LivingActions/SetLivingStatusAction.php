<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 13:22
 */

namespace backend\controllers\LivingActions;


use frontend\business\LivingUtil;
use yii\base\Action;

class SetLivingStatusAction extends Action
{
    public function run($living_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($living_id))
        {
            $rst['message'] = '直播id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living = LivingUtil::GetLivingById($living_id);
        if(!isset($living))
        {
            $rst['message'] = '直播间不存在';
            echo json_encode($rst);
            exit;
        }

        $hasEdit = \Yii::$app->request->post('hasEditable');
        if(!isset($hasEdit))
        {
            $rst['message'] = 'hasEditable参数为空';
            echo json_encode($rst);
            exit;
        }
        if(empty($hasEdit))
        {
            $rst['message'] = '';
            echo json_encode($rst);
            exit;
        }
        $editIndex = \Yii::$app->request->post('editableIndex');
        if(!isset($editIndex))
        {
            $rst['message'] = 'editableIndex参数为空';
            echo json_encode($rst);
            exit;
        }

        $status = \Yii::$app->request->post('status');
        if(!isset($status))
        {
            $rst['message'] = '直播状态参数为空';
            echo json_encode($rst);
            exit;
        }

        $living->status = $status;
        if(!LivingUtil::SaveLiving($living,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 