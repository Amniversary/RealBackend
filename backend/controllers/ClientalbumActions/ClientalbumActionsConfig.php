<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:30
 */
return [
    'index' => [
        'class' => 'backend\controllers\ClientalbumActions\IndexAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\ClientalbumActions\DeleteAction',
    ],
    'checkbatch'=>[
        'class' => 'backend\controllers\ClientalbumActions\CheckbatchAction',
    ],
    'view_all'=>[
        'class' => 'backend\controllers\ClientalbumActions\ViewallAction',
    ],
];