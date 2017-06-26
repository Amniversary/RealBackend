<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/1
 * Time: 10:35
 */

namespace backend\controllers\LivingActions;


use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use yii\base\Action;

class LivingHotNumAction extends Action
{
    public function run($living_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($living_id))
        {
            $rst['message'] = '直播参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $living = LivingHotUtil::GetLivingHotByLivingId($living_id);
        if(!isset($living))
        {
            $rst['message'] = '直播记录不存在';
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
        $living_num = \Yii::$app->request->post('living_num');

        $living->living_num = $living_num;
        if(!LivingUtil::SaveHotLiving($living,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 