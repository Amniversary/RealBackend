<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 17:21
 */
namespace backend\controllers\ChangeRecordActions;


use frontend\business\IntegralUtil;
use yii\base\Action;

class SetAddressAction extends Action
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
        $record = IntegralUtil::GetRecordById($record_id);
        if(!isset($record))
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
        $address = \Yii::$app->request->post('address');

        if($record->change_state == 0){
            $record->address = $address;
            if(!IntegralUtil::SaveChangeState($record,$error))
            {
                $rst['message'] = $error;
                echo json_encode($rst);
                exit;
            }
        }else{
            $rst['message'] = '已发货不能修改发货地址';
            echo json_encode($rst);
            exit;
        }



        echo '0';
    }
}