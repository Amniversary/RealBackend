<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 13:44
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\GoodsActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\GoodsActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\GoodsActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\GoodsActions\CreateAction',
    ],
    'status'=>[
        'class'=> 'backend\controllers\GoodsActions\SetStatusAction',
    ],
    'sale_type'=>[
        'class'=> 'backend\controllers\GoodsActions\SetSaleTypeAction',
    ],
    'goods_type'=>[
        'class'=> 'backend\controllers\GoodsActions\SetGoodsTypeAction',
    ],
    'goods_order'=>[
        'class'=> 'backend\controllers\GoodsActions\SetOrderNoAction',
    ],
    'goods_led'=>[
        'class'=> 'backend\controllers\GoodsActions\SetHighLedAction',
    ],
];