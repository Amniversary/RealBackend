<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\LuckyGiftActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\LuckyGiftActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\LuckyGiftActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\LuckyGiftActions\CreateAction',
    ],
    'setstatus'=>[
        'class'=>  'backend\controllers\LuckyGiftActions\SetStatusAction',
    ]
];