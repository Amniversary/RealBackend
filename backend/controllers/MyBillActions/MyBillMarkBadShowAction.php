<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\MyBillActions;


use backend\components\ExitUtil;
use frontend\business\BillUtil;
use yii\base\Action;

/**
 * Class MyBillMarkBadShowAction 坏账设置
 * @package backend\controllers\GetCashActions
 */
class MyBillMarkBadShowAction extends Action
{
    public function run($my_bill_id)
    {
        if(empty($my_bill_id))
        {
            ExitUtil::ExitWithMessage('账单单据记录id不能为空');
        }
        $billInfo = BillUtil::GetBillRecordById($my_bill_id);
        if(!isset($billInfo))
        {
            ExitUtil::ExitWithMessage('账单记录不存在');
        }
        $this->controller->getView()->title = '账单设置成坏账';
        $this->controller->layout = 'main_empty';
        return $this->controller->render('financeshow',
            [
                'bill_info'=>$billInfo
            ]
        );
    }
} 