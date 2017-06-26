<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\AdvertisingManageActions\IndexAction',
    ],
    'create'=>[
        'class' => 'backend\controllers\AdvertisingManageActions\CreateAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\AdvertisingManageActions\DeleteAction',
    ],
    'update'=>[
        'class' => 'backend\controllers\AdvertisingManageActions\UpdateAction',
    ],
    'setstatus'=>[
        'class'=>'backend\controllers\AdvertisingManageActions\SetStatusAction',
    ],
];