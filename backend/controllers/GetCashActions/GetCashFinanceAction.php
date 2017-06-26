<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\GetCashActions;


use frontend\business\GetCashUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * Class IndexAction 打款设置
 * @package backend\controllers\GetCashActions
 */
class GetCashFinanceAction extends Action
{
    public function run($get_cash_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($get_cash_id))
        {
           $rst['msg'] = '提现记录id不能为空';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $getCash = GetCashUtil::GetCashRecordById($get_cash_id);
        if(!isset($getCash))
        {
            $rst['msg'] = '提现记录不存在';
            \Yii::getLogger()->log($rst['msg']. ' get_cash_id:'.$get_cash_id,Logger::LEVEL_ERROR );
            echo json_encode($rst);
            exit;
        }
        $getCash->finance_remark = $remark;
        $getCash->finace_ok_time = date('Y-m-d H:i:s');
        $error = '';
        if(!GetCashUtil::SetFinaceOk($getCash,\Yii::$app->user->id,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 