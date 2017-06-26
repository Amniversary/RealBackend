<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/13
 * Time: 18:41
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\BalanceUtil;
use frontend\business\ClientUtil;
use frontend\business\GoodsTicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWxGetGoodsListAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $user_id = \Yii::$app->session['draw_user_id'];
        //$user_id = 81;
        if(!isset($user_id))
        {
            $rst['code'] = '2';
            $rst['msg'] = '您还未绑定蜜播账号，请先绑定蜜播账号！'.\Yii::$app->session['draw_user_id'];
            echo json_encode($rst);
            exit;
        }
        $userInfo = ClientUtil::GetClientById($user_id);
        if(!isset($userInfo))
        {
            $rst['msg'] = '用户信息不存在！';
            echo json_encode($rst);
            exit;
        }
        $unionid = \Yii::$app->session['draw_unionid'];
        
        if(!isset($unionid))
        {
            $rst['msg'] = '系统信息错误:unionid';
            echo json_encode($rst);
            exit;
        }

        $otherInfo = ClientUtil::GetClientOtherInfo($unionid);
        if(!isset($otherInfo))
        {
            $rst['code'] = '2';
            $rst['msg'] = '用户未绑定微信账号，请到蜜播App绑定微信账号!';
            echo json_encode($rst);
            exit;
        }
        \Yii::$app->session['draw_user_id'] = $userInfo->client_id;
        //用户id  头像  蜜播id  昵称 ,账户余额  商品票数  兑换金额
        $goods = GoodsTicketToCashUtil::GetGoodsTicketList();
        $list = [];
        $list['data'] = BalanceUtil::GetUserBalanceById($user_id);
        $list['goods'] = $goods;

        $rst['code'] = '0';
        $rst['msg'] = $list;
        echo json_encode($rst);
    }
} 