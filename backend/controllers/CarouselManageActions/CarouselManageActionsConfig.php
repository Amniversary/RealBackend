<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'index' => [
        'class' => 'backend\controllers\CarouselManageActions\IndexAction',
    ],
    'create'=>[
        'class' => 'backend\controllers\CarouselManageActions\CreateAction',
    ],
    'delete'=>[
        'class' => 'backend\controllers\CarouselManageActions\DeleteAction',
    ],
    'update'=>[
        'class' => 'backend\controllers\CarouselManageActions\UpdateAction',
    ],
    'setstatus'=>[
        'class'=>'backend\controllers\CarouselManageActions\SetStatusAction',
    ],
];