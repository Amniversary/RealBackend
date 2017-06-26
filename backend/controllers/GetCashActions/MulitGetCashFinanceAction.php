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
 * Class IndexAction 批量打款设置
 * @package backend\controllers\GetCashActions
 */
class MulitGetCashFinanceAction extends Action
{
    public function run()
    {
        set_time_limit(0);
        $rst=['code'=>'1','msg'=>''];
        $getCashIds = \Yii::$app->request->post('GetCashId');
        if(!isset($getCashIds) || count($getCashIds) <= 0)
        {
           $rst['msg'] = '没有提现记录';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $okCount = 0;
        $failCount = 0;
        foreach($getCashIds as $get_cash_id)
        {
            $getCash = GetCashUtil::GetCashRecordById($get_cash_id);
            if(!isset($getCash))
            {
                $failCount ++;
                \Yii::getLogger()->log($rst['msg']. ' get_cash_id:'.$get_cash_id,Logger::LEVEL_ERROR );
                continue;
            }
            $getCash->finance_remark = $remark;
            $getCash->finace_ok_time = date('Y-m-d H:i:s');
            $error = '';
            if(!GetCashUtil::SetFinaceOk($getCash,\Yii::$app->user->id,$error))
            {
                \Yii::getLogger()->log($error. ' get_cash_id:'.$get_cash_id,Logger::LEVEL_ERROR );
                $failCount ++;
                continue;
            }
            $okCount ++;
        }
        if($failCount === 0)
        {
            $msg = '全部设置成功';
        }
        else
        {
            $msg = sprintf('失败了【%s】条，成功了【%s】条',$failCount,$okCount);
        }

        $rst['code']='0';
        $rst['msg']= $msg;
        echo json_encode($rst);
    }
} 