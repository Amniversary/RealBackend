<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 16:58
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\IndexAction',
    ],
    'set_state'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\SetStateAction',
    ],
    'set_address'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\SetAddressAction',
    ],
    'detail'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\DetailAction',
    ],
    'check_delivery'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\CheckDeliveryAction',
    ],
    'indexexamine'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\IndexExamineAction',
    ],
    'all_check'=>[
        'class'=> 'backend\controllers\ChangeRecordActions\AllCheckAction',
    ],
];