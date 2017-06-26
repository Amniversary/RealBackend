<?php

namespace backend\controllers\CheckMoneyGoodsActions;


use backend\business\BackendBusinessCheckUtil;
use backend\components\ExitUtil;
use frontend\business\BusinessCheckUtil;
use frontend\business\TicketToCashUtil;
use yii\base\Action;

/**
 * 审核详情
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class DetailAction extends Action
{
    public function run($record_id,$date_type)
    {
//        echo \Yii::$app->user->id;exit;
        $model = TicketToCashUtil::GetTickToCashAndUserById($record_id);
        $check_user =  BusinessCheckUtil ::GetBusinessCheckInfo($record_id);
        $model['admin_user_name'] = $check_user->check_user_name;
        if(!isset($model))
        {
            ExitUtil::ExitWithMessage('记录不存在');
        }
        $model['cash_type'] = ($model['cash_type'] == 1)?'微信':(($model['cash_type'] == 2)?'支付宝':'其他');

        switch($model['status']){
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
        if($date_type == 'audited'){
            return $this->controller->render('detailaudited', [
                'model' => $model,
            ]);
        }else{
            return $this->controller->render('detail', [
                'model' => $model,
            ]);
        }

    }
}