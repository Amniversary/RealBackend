<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\UserManageActions\IndexAction',
    ],
    'create'=>[
        'class' => 'backend\controllers\UserManageActions\CreateAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\UserManageActions\DeleteAction',
    ],
    'resetpwd'=>[
        'class' => 'backend\controllers\UserManageActions\ResetPwdAction',
    ],
    'update'=>[
        'class' => 'backend\controllers\UserManageActions\UpdateAction',
    ],
    /*'setcheckno'=>[
        'class'=>'backend\controllers\UserManageActions\SetCheckNoAction',
    ],*/
    'setstatus'=>[
        'class'=>'backend\controllers\UserManageActions\SetStatusAction',
    ],
    'setprivilige'=>[
        'class'=>'backend\controllers\UserManageActions\SetPriviligeAction',
    ],
    'getprivilige'=>[
        'class'=>'backend\controllers\UserManageActions\GetPriviligeAction',
    ],
];