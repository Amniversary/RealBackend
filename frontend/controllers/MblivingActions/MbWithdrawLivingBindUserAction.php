<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/8
 * Time: 14:57
 */

namespace frontend\controllers\MblivingActions;


use common\models\OffUserLiving;
use frontend\business\ApiCommon;
use frontend\business\ClientInfoUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWithdrawLivingBindUserAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $clientNo = \Yii::$app->request->post('client_no');

        $user = ClientInfoUtil::GetClientNo($clientNo);

        $white_user_id = OffUserLiving::findOne(['client_no'=>$clientNo]);

        if(empty($white_user_id))
        {
            if($user->is_centification != 3)
            {
                $rst['msg'] = '请先在APP中通过高级认证';
                echo json_encode($rst);
                exit;
            }
        }

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
        if(!isset(\Yii::$app->session['draw_unionid']))
        {
            \Yii::getLogger()->log('OpenID: '.\Yii::$app->session['draw_unionid'],Logger::LEVEL_ERROR);
            $rst['msg'] = '系统信息错误!';
            echo json_encode($rst);
            exit;
        }
        $openid = \Yii::$app->session['draw_unionid'];

        if(!ApiCommon::GetLoginInfo($user->unique_no,$LoginInfo,$error))
        {
            $rst['msg'] = 'bbb'.$error;
            return false;
        }

        $registerType = 2;
        if(!ClientInfoUtil::GetBindInfo($LoginInfo,$openid,$registerType,$error))
        {
            $rst['msg'] = 'aaa'.$error;
            return false;
        }

        \Yii::$app->session['draw_user_id'] = $LoginInfo['user_id'];
        \Yii::getLogger()->log('draw_user_id:'.\Yii::$app->session['draw_user_id'],Logger::LEVEL_ERROR);
        $rst['code'] = '0';
        //\Yii::getLogger()->log('$rst :'.var_export($rst,true),Logger::LEVEL_ERROR);
        echo json_encode($rst);
        exit;
    }
}