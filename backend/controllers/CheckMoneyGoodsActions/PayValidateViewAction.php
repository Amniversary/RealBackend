<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 11:08
 */

namespace backend\controllers\CheckMoneyGoodsActions;

use yii\base\Action;
use common\components\alipay\AlipayUtil;
use frontend\business\RechargeListUtil;
use common\models\Recharge;

class PayValidateViewAction extends Action
{
    public function run()
    {
        return $this->controller->render('payvalidate');
    }
} 