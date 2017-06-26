<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\CheckReportActions\IndexAction',
    ],
    'detail'=>[
        'class'=>  'backend\controllers\CheckReportActions\DetailAction',
    ],
    'checkrefuse'=>[
        'class'=>  'backend\controllers\CheckReportActions\CheckRefuseAction',
    ],
    'checkpass'=>[
        'class'=>  'backend\controllers\CheckReportActions\CheckPassAction',
    ],
    'indexaudited'=>[
        'class'=>  'backend\controllers\CheckReportActions\IndexAuditedAction',
    ],
    'indexcash'=>[
        'class'=>  'backend\controllers\CheckReportActions\IndexCashAction',
    ],
    'detailcash'=>[
        'class'=>  'backend\controllers\CheckReportActions\DetailCashAction',
    ],
    'playmoney'=>[
        'class'=>  'backend\controllers\CheckReportActions\PlayMoneyAction',
    ],

    'set_status'=>[
        'class'=> 'backend\controllers\CheckReportActions\SetStatusAction',
    ],


];