<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\GetCashActions;


use backend\components\ExitUtil;
use frontend\business\GetCashUtil;
use yii\base\Action;

/**
 * Class IndexAction 提现管理显示
 * @package backend\controllers\GetCashActions
 */
class GetCashFinanceShowAction extends Action
{
    public function run($get_cash_id)
    {
        if(empty($get_cash_id))
        {
            ExitUtil::ExitWithMessage('提现记录id不能为空');
        }
        $getCash = GetCashUtil::GetCashRecordById($get_cash_id);
        if(!isset($getCash))
        {
            ExitUtil::ExitWithMessage('提现记录不存在');
        }
        $this->controller->getView()->title = '提现打款';
        $this->controller->layout = 'main_empty';
        return $this->controller->render('financeshow',
            [
                'get_cash'=>$getCash
            ]
        );
    }
} 