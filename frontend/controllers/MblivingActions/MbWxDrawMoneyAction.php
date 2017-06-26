<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/14
 * Time: 10:16
 */

namespace frontend\controllers\MblivingActions;


use frontend\business\TicketToCashUtil;
use yii\base\Action;
use yii\log\Logger;

class MbWxDrawMoneyAction extends Action
{
    public function run()
    {
        $rst = ['code'=>'1','msg'=>''];
        $unionid = \Yii::$app->session['draw_unionid'];
        if(!isset($unionid))
        {
            $rst['msg'] = '系统信息错误';
            echo json_encode($rst);
            exit;
        }
        $open_id = \Yii::$app->session['draw_openid'];
        if(!isset($open_id))
        {
            $rst['msg'] = '获取系统用户信息错误，请联系客服';
            echo json_encode($rst);
            exit;
        }
        $user_id = \Yii::$app->session['draw_user_id'];
        if(!isset($user_id))
        {
            $rst['msg'] = '没有找到提现用户id';
            echo json_encode($rst);
            exit;
        }

        $goods_id = \Yii::$app->request->post('goods_id');
        if(!isset($goods_id))
        {
            $rst['msg'] = '没有找到对应的商品id';
            echo json_encode($rst);
            exit;
        }

        if(!TicketToCashUtil::WxTicketToCash($unionid,$open_id,$goods_id,$user_id,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }

        $rst['code'] = '0';
        echo json_encode($rst);
    }
} 