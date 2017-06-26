<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 16:49
 */

return [
    'index' => [
        'class' => 'backend\controllers\CommonWordsActions\IndexAction'
    ],
    'delete' => [
        'class' => 'backend\controllers\CommonWordsActions\DeleteAction'
    ],
    'update'=>[
        'class'=> 'backend\controllers\CommonWordsActions\UpdateAction',
    ],
    'add'=>[
        'class'=> 'backend\controllers\CommonWordsActions\AddAction',
    ]
];