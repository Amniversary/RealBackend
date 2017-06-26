<?php
/**
 * Created by PhpStorm.
 * User: WangWei
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\IndexAction',
    ],
    'detail'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\DetailAction',
    ],
    
    'edit'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\EditAction',
    ],
    
    'delete'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\DeleteAction',
    ],
    
    'create'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\CreateAction',
    ],
    
    'updatestatus'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\StatusAction',
    ],

    'saletype'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\SaleTypeAction',
    ],
    
    'goodstype'=>[
        'class'=> 'backend\controllers\GoldsGoodsActions\GoodsTypeAction',
    ],
];