<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

namespace backend\controllers\PaymentmanageActions;

use kartik\grid\EditableColumnAction;

class UpdateParamAction extends EditableColumnAction
{
    public $modelClass = 'common\models\Payment';
}