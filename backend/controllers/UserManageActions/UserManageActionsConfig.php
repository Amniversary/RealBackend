<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' =>  'backend\controllers\UserManageActions\IndexAction',
    'create'=> 'backend\controllers\UserManageActions\CreateAction',
    'delete'=> 'backend\controllers\UserManageActions\DeleteAction',
    'resetpwd'=> 'backend\controllers\UserManageActions\ResetPwdAction',
    'update'=> 'backend\controllers\UserManageActions\UpdateAction',
    'setstatus'=>'backend\controllers\UserManageActions\SetStatusAction',
    'setprivilige'=>'backend\controllers\UserManageActions\SetPriviligeAction',
    'getprivilige'=>'backend\controllers\UserManageActions\GetPriviligeAction',
    'get_backend'=>'backend\controllers\UserManageActions\GetBackendAction',
    'set_backend'=>'backend\controllers\UserManageActions\SetBackendAction',
];