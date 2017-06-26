<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\FundBorrowActions;


use backend\components\ExitUtil;
use frontend\business\BorrowFundUtil;
use yii\base\Action;

/**
 * Class GetCashFinanceShowAction 美愿基金财务打款
 * @package backend\controllers\GetCashActions
 */
class FundBorrowFinanceShowAction extends Action
{
    public function run($borrow_fund_id)
    {
        if(empty($borrow_fund_id))
        {
            ExitUtil::ExitWithMessage('借款单据记录id不能为空');
        }
        $fundBorrow = BorrowFundUtil::GetBorrowFundRecordById($borrow_fund_id);
        if(!isset($fundBorrow))
        {
            ExitUtil::ExitWithMessage('借款记录不存在');
        }
        $this->controller->getView()->title = '美愿基金打款';
        $this->controller->layout = 'main_empty';
        return $this->controller->render('financeshow',
            [
                'fund_borrow'=>$fundBorrow
            ]
        );
    }
} 