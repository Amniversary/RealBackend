<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\PaymentmanageActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\PaymentmanageActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\PaymentmanageActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\PaymentmanageActions\CreateAction',
    ],
    'setstatus'=>[
        'class'=>  'backend\controllers\PaymentmanageActions\SetStatusAction',
    ],
    'apptype'=>[
        'class'=>  'backend\controllers\PaymentmanageActions\AppTypeAction',
    ],
    'update_param' => [
        'class' => 'backend\controllers\PaymentmanageActions\UpdateParamAction',
    ],
    'setappid' => [
        'class' => 'backend\controllers\PaymentmanageActions\SetAppIdAction',
    ],
];