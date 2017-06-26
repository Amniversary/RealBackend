<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\FundBorrowActions;


use frontend\business\BorrowFundUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * Class IndexAction 打款设置
 * @package backend\controllers\GetCashActions
 */
class FundBorrowFinanceAction extends Action
{
    public function run($borrow_fund_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($borrow_fund_id))
        {
           $rst['msg'] = '借款记录id不能为空';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $borrowFund = BorrowFundUtil::GetBorrowFundRecordById($borrow_fund_id);
        if(!isset($borrowFund))
        {
            $rst['msg'] = '借款记录不存在';
            \Yii::getLogger()->log($rst['msg']. ' get_cash_id:'.$get_cash_id,Logger::LEVEL_ERROR );
            echo json_encode($rst);
            exit;
        }
        $borrowFund->finance_remark = $remark;
        $error = '';
        if(!BorrowFundUtil::SetFinaceOk($borrowFund,\Yii::$app->user->id,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 