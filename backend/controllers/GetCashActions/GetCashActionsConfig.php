<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/22
 * Time: 16:41
 */
return [
    'index' => [
        'class' => 'backend\controllers\GetCashActions\IndexAction',
    ],
    'indexhis'=>[
        'class' => 'backend\controllers\GetCashActions\IndexHistoryAction',
    ],
    'finance'=>[
        'class' => 'backend\controllers\GetCashActions\GetCashFinanceAction',
    ],
    'financeshow'=>[
        'class' => 'backend\controllers\GetCashActions\GetCashFinanceShowAction',
    ],
    'mulitfinance'=>[
        'class' => 'backend\controllers\GetCashActions\MulitGetCashFinanceAction',
    ],
];