<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 13:52
 */

return [
    'index' =>[
        'class'=>'backend\controllers\AdvertiseActions\IndexAction',
    ],
    'create'=>[
        'class'=>'backend\controllers\AdvertiseActions\CreateAction',
    ],
    'delete'=>[
        'class'=>'backend\controllers\AdvertiseActions\DeleteAction',
    ],
    'update'=>[
        'class'=>'backend\controllers\AdvertiseActions\UpdateAction',
    ],
    'status'=>[
        'class'=>'backend\controllers\AdvertiseActions\StatusAction',
    ]

];