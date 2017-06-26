<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\GoldsAccountActions\IndexAction',
    ],
    'detail'=>[
        'class'=> 'backend\controllers\GoldsAccountActions\DetailAction',
    ],
    
    'edit'=>[
        'class'=> 'backend\controllers\GoldsAccountActions\EditAction',
    ],
    'status'=>[                       
        'class'=> 'backend\controllers\GoldsAccountActions\StatusAction',
    ],
    'back'=>[
        'class'=> 'backend\controllers\GoldsAccountActions\BackAction',
    ],
];