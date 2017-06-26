<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\GiftActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\GiftActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\GiftActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\GiftActions\CreateAction',
    ],
    'setstatus'=>[
        'class'=>  'backend\controllers\GiftActions\SetStatusAction',
    ],
    'black'=>[
        'class'=>  'backend\controllers\GiftActions\BlackAction',
    ],

];