<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\VersionManageActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\VersionManageActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\VersionManageActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\VersionManageActions\CreateAction',
    ],
    'setstatus'=>[
        'class'=>  'backend\controllers\VersionManageActions\SetStatusAction',
    ],
    'indexson'=>[
        'class'=>  'backend\controllers\VersionManageActions\IndexSonAction',
    ],
    'createson'=>[
        'class'=>  'backend\controllers\VersionManageActions\CreateSonAction',
    ],
    'updateson'=>[
        'class'=>  'backend\controllers\VersionManageActions\UpdateSonAction',
    ],
    'setstatusson'=>[
    'class'=>  'backend\controllers\VersionManageActions\SetStatusSonAction',
    ],
    'detailson'=>[
        'class'=>  'backend\controllers\VersionManageActions\DetailSonAction',
    ],
    'deleteson'=>[
        'class'=> 'backend\controllers\VersionManageActions\DeleteSonAction',
    ],
    'set_version_inner'=>[
        'class'=> 'backend\controllers\VersionManageActions\SetVersionInnerAction',
    ],
    'set_register'=>[
        'class'=>'backend\controllers\VersionManageActions\SetIsRegisterAction',
    ],

];