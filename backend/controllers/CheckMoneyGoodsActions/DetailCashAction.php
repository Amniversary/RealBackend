<?php

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\components\ExitUtil;
use frontend\business\BusinessCheckUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 打款详情
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class DetailCashAction extends Action
{
    public function run($record_id,$data_type)
    {
//        echo \Yii::$app->user->id;exit;
        $model = TicketToCashUtil::GetTickToCashAndUserById($record_id);
        $check_user = BusinessCheckUtil::GetBusinessCheckInfo($record_id);
        $model['admin_user_name'] = $check_user->check_user_name;
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('记录不存在');
        }

        $user_info = [];
        $alipay_or_wechat = '';
        if($model['cash_type'] == 1)
        {
            $model['cash_type'] = '微信';
            $alipay_or_wechat = 'wechat';
        }
        else if($model['cash_type'] == 2)
        {
            $alipay = TicketToCashUtil::CheckBindAlipay($record_id);
            $user_info['real_name'] = $alipay['real_name'];
            $user_info['alipay_no'] = $alipay['alipay_no'];
            $user_info['identity_no'] = $alipay['identity_no'];
            $model['cash_type'] = '支付宝';
            $alipay_or_wechat = 'alipay';
        }
        switch($model['status'])
        {
            case 1 :
                $model['check_time'] = '未审核';
                $model['finace_ok_time'] = '未打款';
                break;
            case  2 :
                $model['finace_ok_time'] = '未打款';
                break;
            case 4 :
                $model['finace_ok_time'] = '已拒绝';
                break;
        }
        $this->controller->layout='main_empty';
        if($data_type == 'unpaid')
        {
            return $this->controller->render('detail_unpaid', [
                'model' => $model,
                'user_info' => $user_info,
                'alipay_or_wechat' => $alipay_or_wechat
            ]);
        }else
        {
            return $this->controller->render('detail_paid', [
                'model' => $model,
                'user_info' => $user_info,
                'alipay_or_wechat' => $alipay_or_wechat
            ]);
        }

    }
}