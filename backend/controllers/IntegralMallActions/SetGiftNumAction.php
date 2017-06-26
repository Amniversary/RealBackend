<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/22
 * Time: 9:35
 */
namespace backend\controllers\IntegralMallActions;


use frontend\business\IntegralUtil;
use yii\base\Action;
use yii\log\Logger;

class SetGiftNumAction extends Action
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
        $gift_num = \Yii::$app->request->post('gift_num');

        $gift->gift_num = $gift_num;
        if(!IntegralUtil::SaveGiftMoney($gift,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }

        echo '0';
    }
}