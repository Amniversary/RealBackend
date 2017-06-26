<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/6
 * Time: 19:16
 */

namespace frontend\controllers\MblivingActions;


use backend\components\ExitUtil;
use common\components\wxpay\JsApiPay;
use common\components\Yii2ValidateCode;
use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class PCRechargeLoginAction extends Action
{
    public function run()
    {
        $getVPic = \Yii::$app->session['pc_recharge'];
        if(!isset($getVPic) || $getVPic !== '1')
        {
            $rst['msg'] = '系统信息错误';
            echo json_encode($rst);
            exit;
        }
        $rst = ['code'=>'1','msg'=>''];
        $clientNo = \Yii::$app->request->post('client_no');
        $user = ClientInfoUtil::GetClientNo($clientNo);
        if(!isset($clientNo))
        {
            \Yii::getLogger()->log('Client: '.$clientNo,Logger::LEVEL_ERROR);
            $rst['msg'] = '蜜播号不能为空';
            echo json_encode($rst);
            exit;
        }
        if(!isset($user))
        {
            \Yii::getLogger()->log('user: '.var_export($user,true),Logger::LEVEL_ERROR);
            $rst['msg'] = '该蜜播账号用户信息不存在';
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->session['user_id'] =  $user->client_id;
        $vCode = \Yii::$app->request->post('v_code');
        if(empty($vCode))
        {
            $rst['msg'] = '验证码不能为空';
            echo json_encode($rst);
            exit;
        }
        if(!Yii2ValidateCode::ValidatePicCode($vCode))
        {
            $rst['msg'] = '验证码不正确';
            echo json_encode($rst);
            exit;
        }
        $rst['code'] = '0';
        echo json_encode($rst);
        exit;
    }
} 