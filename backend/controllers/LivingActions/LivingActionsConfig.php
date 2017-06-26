<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/20
 * Time: 21:07
 */

return [
    'hot_living'=>[
        'class'=> 'backend\controllers\LivingActions\ClientHotLivingAction',
    ],
    'living_status'=>[
        'class'=> 'backend\controllers\LivingActions\SetLivingStatusAction',
    ],
    'look_living'=>[
        'class'=> 'backend\controllers\LivingActions\LookLivingAction',
    ],
    'set_status'=>[
        'class'=> 'backend\controllers\LivingActions\SetStatusAction',
    ],
    'set_order'=>[
        'class'=>'backend\controllers\LivingActions\SetOrderAction',
    ],
    'living_hot'=>[
        'class'=>'backend\controllers\LivingActions\LivingHotNumAction',
    ],
    'closelive'=>[
        'class'=>'backend\controllers\LivingActions\CloseLiveAction',
    ],
    'set_limit_num'=>[
        'class'=>'backend\controllers\LivingActions\SetLimitNumAction',
    ],
    'living_monitor'=>[
        'class'=>'backend\controllers\LivingActions\LivingMonitorAction',
    ],
    'living_monitor_one'=>[
        'class'=>'backend\controllers\LivingActions\LivingMonitorOneAction',
    ],
    'living_operation'=>[
        'class'=>'backend\controllers\LivingActions\LivingOperationAction',
    ],
    'flush'=>[
        'class'=>'backend\controllers\LivingActions\FlushAction',
    ]
];