<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 15:50
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\ClientGoodsUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWeChatGoldGoodsAction extends Action
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

        $goods = ClientGoodsUtil::GetGoldGoodsList();
        if(empty($goods))
        {
            $rst['msg'] = '金币商品信息不存在';
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
        \Yii::getLogger()->log('用户金币商品信息:'.var_export($rst,true),Logger::LEVEL_ERROR);
        echo json_encode($rst);
    }
} 