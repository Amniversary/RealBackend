<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/22
 * Time: 16:41
 */
return [
    'index' => [
        'class' => 'backend\controllers\FundBorrowActions\IndexAction',
    ],
    'indexhis'=>[
        'class' => 'backend\controllers\FundBorrowActions\IndexHistoryAction',
    ],
    'finance'=>[
        'class' => 'backend\controllers\FundBorrowActions\FundBorrowFinanceAction',
    ],
    'financeshow'=>[
        'class' => 'backend\controllers\FundBorrowActions\FundBorrowFinanceShowAction',
    ],
];