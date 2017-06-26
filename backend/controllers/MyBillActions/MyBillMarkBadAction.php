<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\MyBillActions;


use backend\business\UserUtil;
use frontend\business\BillUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * Class MyBillMarkBadAction 设置坏账
 * @package backend\controllers\GetCashActions
 */
class MyBillMarkBadAction extends Action
{
    public function run($my_bill_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($my_bill_id))
        {
           $rst['msg'] = '借款记录id不能为空';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $billInfo = BillUtil::GetBillRecordById($my_bill_id);
        if(!isset($billInfo))
        {
            $rst['msg'] = '账单记录不存在';
            \Yii::getLogger()->log($rst['msg']. ' my_bill_id:'.$my_bill_id,Logger::LEVEL_ERROR );
            echo json_encode($rst);
            exit;
        }
        $user_id = \Yii::$app->user->id;
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            $rst['msg']= '后台用户信息不存在';
            echo json_encode($rst);
            exit;
        }
        $billInfo->bad_bill_remark = $remark;
        $billInfo->bad_mark_user_id = $user->backend_user_id;
        $billInfo->bad_mark_user_name = $user->username;
        $error = '';
        if(!BillUtil::SetBadRemark($billInfo,$user,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 