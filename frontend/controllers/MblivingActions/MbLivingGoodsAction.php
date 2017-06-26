<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\MblivingActions;


use backend\components\ExitUtil;
use common\components\wxpay\JsApiPay;
use frontend\business\ClientGoodsUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MbLivingGoodsAction extends Action
{

    public function run()
    {

        $rst = ['code'=>'1','msg'=>''];
        $user_id = \Yii::$app->session['recharge_user_id'];
        /*$user_id = 25;*/
        if(!isset($user_id))
        {
            $rst['msg'] = '系统信息错误代号:user_id';
            echo json_encode($rst);
            exit;
        }
        $userInfo = ClientUtil::GetClientById($user_id);
        if(!isset($userInfo))
        {
            $rst['msg'] = '用户信息不存在';
            echo json_encode($rst);
            exit;
        }

        $goods = ClientGoodsUtil::GetBeanCommodityList();
        if(empty($goods))
        {
            $rst['msg'] = '商品信息不存在';
            echo json_encode($rst);
            exit;
        }
        $test = [];
        $test['data']['nick_name'] = $userInfo->nick_name;
        $test['data']['client_no'] = $userInfo->client_no;
        $test['data']['pic'] = $userInfo->pic;
        $test['data']['unique_no'] = $userInfo->unique_no;
        $test['goods'] = $goods;

        $rst['code'] = '0';
        $rst['msg'] = $test;
        \Yii::getLogger()->log('用户商品信息:'.var_export($rst,true),Logger::LEVEL_ERROR);
        echo json_encode($rst);

    }
} 