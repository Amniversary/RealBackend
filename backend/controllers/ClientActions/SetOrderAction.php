<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/17
 * Time: 16:10
 */

namespace backend\controllers\ClientActions;


use frontend\business\LivingHotUtil;
use frontend\business\LivingUtil;
use yii\base\Action;

class SetOrderAction extends Action
{
    public function run($living_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($living_id))
        {
            $rst['message'] = '参数id不能为空';
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
        $order_no = \Yii::$app->request->post('order_no');

        $living->order_no = $order_no;
        if(!LivingUtil::SaveHotLiving($living,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }
        echo '0';
    }
} 