<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/22
 * Time: 16:41
 */
return [
    'index' => [
        'class' => 'backend\controllers\MyBillActions\IndexAction',
    ],
    'indexhis'=>[
        'class' => 'backend\controllers\MyBillActions\IndexHistoryAction',
    ],
    'remarkbad'=>[
        'class' => 'backend\controllers\MyBillActions\MyBillMarkBadAction',
    ],
    'remarkbadshow'=>[
        'class' => 'backend\controllers\MyBillActions\MyBillMarkBadShowAction',
    ],
];