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
use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MbLivingBindUserAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $clientNo = \Yii::$app->request->post('client_no');
        $user = ClientInfoUtil::GetClientNo($clientNo);
        if(!isset($clientNo))
        {
            \Yii::getLogger()->log('Client: '.$clientNo,Logger::LEVEL_ERROR);
            $rst['msg'] = '蜜播账号不能为空!';
            echo json_encode($rst);
            exit;
        }
        if(!isset($user))
        {
            \Yii::getLogger()->log('user: '.var_export($user,true),Logger::LEVEL_ERROR);
            $rst['msg'] = '该蜜播账号用户信息不存在!';
            echo json_encode($rst);
            exit;
        }

        if(!isset(\Yii::$app->session['openid']))
        {
            \Yii::getLogger()->log('OpenID: '.\Yii::$app->session['recharge_openid'],Logger::LEVEL_ERROR);
            $rst['msg'] = '系统信息错误!';
            echo json_encode($rst);
            exit;
        }
        $openid = \Yii::$app->session['openid'];
        $userId = \Yii::$app->session['recharge_user_id'];//初始肯定是空的
        if(!ClientInfoUtil::UpdateBindUserPay($userId,$clientNo,$openid,$error))
        {
            $rst['msg'] = $error;
            \Yii::getLogger()->log('rst :'.var_export($rst,true),Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        \Yii::getLogger()->log('$rst :'.var_export($rst,true),Logger::LEVEL_ERROR);
        echo json_encode($rst);
        exit;
    }
} 