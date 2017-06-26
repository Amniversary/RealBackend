<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\ClientmanageActions\IndexAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\ClientmanageActions\DeleteAction',
    ],
    'setstatus'=>[
        'class'=>'backend\controllers\ClientmanageActions\SetStatusAction',
    ],
    'setinner'=>[
        'class'=>'backend\controllers\ClientmanageActions\SetIsInnerAction'
    ],
    'modify_balance'=>[
        'class'=>'backend\controllers\ClientmanageActions\ModifyBalanceAction'
    ],
];