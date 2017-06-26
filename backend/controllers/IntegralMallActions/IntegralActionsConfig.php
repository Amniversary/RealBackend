<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 13:50
 */
return [
    'index'=>[
        'class'=> 'backend\controllers\IntegralMallActions\IndexAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\IntegralMallActions\CreateAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\IntegralMallActions\DeleteAction',
    ],
    'set_money'=>[
        'class'=>'backend\controllers\IntegralMallActions\SetMoneyAction',
    ],
    'set_integral'=>[
        'class'=>'backend\controllers\IntegralMallActions\SetIntegralAction',
    ],
    'update'=>[
        'class'=>'backend\controllers\IntegralMallActions\UpdateAction',
    ],
    'set_gift_num'=>[
        'class'=>'backend\controllers\IntegralMallActions\SetGiftNumAction',
    ],
];