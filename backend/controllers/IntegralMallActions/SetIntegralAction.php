<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 14:15
 */
namespace backend\controllers\IntegralMallActions;


use frontend\business\IntegralUtil;
use yii\base\Action;
use yii\log\Logger;

class SetIntegralAction extends Action
{
    public function run($record_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($record_id))
        {
            $rst['message'] = '参数id不能为空';
            echo json_encode($rst);
            exit;
        }
        $gift = IntegralUtil::GetGiftMoneyById($record_id);
        if(!isset($gift))
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
        $gift_integral = \Yii::$app->request->post('gift_integral');

        $gift->gift_integral = $gift_integral;
        if(!IntegralUtil::SaveGiftMoney($gift,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }

        echo '0';
    }
}