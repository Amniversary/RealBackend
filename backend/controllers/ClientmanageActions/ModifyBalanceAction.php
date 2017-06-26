<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/27
 * Time: 15:10
 */

namespace backend\controllers\ClientmanageActions;



use backend\business\UserUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\UserAccountInfoUtil;
use yii\base\Action;

class ModifyBalanceAction extends Action
{
    public function run($account_id)
    {
        $rst =['code'=>'1', 'msg'=>''];
        $unique_id = \Yii::$app->session['unique_id'];
        if(!isset($unique_id))
        {
            $rst['msg'] = '系统错误';
            echo json_encode($rst);
            exit;
        }
        if(empty($account_id))
        {
            $rst['msg'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
            //ExitUtil::ExitWithmsg('用户id不能为空');
        }
        $client = PersonalUserUtil::GetAccontInfoById($account_id);// ReportUtil::GetReportById($my_report_id);
        if(!isset($client))
        {
            //ExitUtil::ExitWithmsg('用户不存在');
            $rst['msg'] = '用户记录不存在';
            echo json_encode($rst);
            exit;
        }
        $operate_type = \Yii::$app->request->post('operate_type');
        $op_money = \Yii::$app->request->post('op_money');
        if(empty($operate_type) || empty($op_money))
        {
            $rst['msg'] = '参数异常';
            echo json_encode($rst);
            exit;
        }
        $user = UserUtil::GetUserByUserId(\Yii::$app->user->id);
        if(!isset($user))
        {
            $rst['msg'] = '系统用户不存在';
            echo json_encode($rst);
            exit;
        }
        if(!UserAccountInfoUtil::ModifyBalance($account_id,$operate_type,$op_money,$user,$error,$unique_id))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 