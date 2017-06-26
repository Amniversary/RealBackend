<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:21
 */

return [
    'index'=>[
        'class'=>'backend\controllers\FamilyActions\IndexAction',
    ],
    'create'=>[
        'class'=>'backend\controllers\FamilyActions\CreateAction',
    ],
    'set_status'=>[
        'class'=>'backend\controllers\FamilyActions\SetStatusAction',
    ],
    'delete'=>[
        'class'=>'backend\controllers\FamilyActions\DeleteAction',
    ],
    'update'=>[
        'class'=>'backend\controllers\FamilyActions\UpdateAction',
    ],
    'reset_pwd'=>[
        'class'=>'backend\controllers\FamilyActions\ReSetPwdAction',
    ],
    'index_son'=>[
        'class'=>'backend\controllers\FamilyActions\IndexSonAction',
    ],
    'create_son'=>[
        'class'=>'backend\controllers\FamilyActions\CreateSonAction',
    ],
    'delete_son'=>[
        'class'=>'backend\controllers\FamilyActions\DeleteSonAction',
    ],
];
