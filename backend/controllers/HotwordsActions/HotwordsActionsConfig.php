<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\HotwordsActions\IndexAction',
    ],
    'create'=>[
        'class' => 'backend\controllers\HotwordsActions\CreateAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\HotwordsActions\DeleteAction',
    ],
    'update'=>[
        'class' => 'backend\controllers\HotwordsActions\UpdateAction',
    ],
    'setstatus'=>[
        'class'=>'backend\controllers\HotwordsActions\SetStatusAction',
    ],
];