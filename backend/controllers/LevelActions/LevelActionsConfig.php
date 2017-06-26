<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:44
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\LevelActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\LevelActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\LevelActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\LevelActions\CreateAction',
    ],
    'status'=>[
        'class'=> 'backend\controllers\LevelActions\SetStatusAction',
    ],

];