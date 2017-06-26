<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 11:34
 */

return [
    'index' => [
        'class' => 'backend\controllers\AdvertorialActions\IndexAction'
    ],
    'create' => [
        'class' => 'backend\controllers\AdvertorialActions\CreateAction'
    ],
    'update' => [
        'class' => 'backend\controllers\AdvertorialActions\UpdateAction'
    ],
    'delete' => [
        'class' => 'backend\controllers\AdvertorialActions\DeleteAction'
    ],
    'kupload'=>[
        'class' => 'pjkui\kindeditor\KindEditorAction'
    ],
    'detail'=>[
        'class' => 'backend\controllers\AdvertorialActions\DetailAction'
    ]
];