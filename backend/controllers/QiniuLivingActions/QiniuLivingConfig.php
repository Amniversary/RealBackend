<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 14:08
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\QiniuLivingActions\QiniuSonParamsIndexAction',
    ],
    'living_parameters' =>[
            'class' => 'backend\controllers\QiniuLivingActions\LivingParameterAction',
        ],
    'update' =>[
        'class' => 'backend\controllers\QiniuLivingActions\UpdateAction',
    ],
    'create' =>[
        'class' => 'backend\controllers\QiniuLivingActions\CreateAction',
    ],
    'delete' =>[
        'class' => 'backend\controllers\QiniuLivingActions\DeleteAction',
    ],
    'client_params'=>[
        'class'=> 'backend\controllers\QiniuLivingActions\QiniuClientParamsAction',
    ],
    'create_client'=>[
        'class'=> 'backend\controllers\QiniuLivingActions\CreateClientAction',
    ],
    'update_client'=>[
        'class'=>'backend\controllers\QiniuLivingActions\UpdateClientAction',
    ],
    'delete_client'=>[
        'class'=>'backend\controllers\QiniuLivingActions\DeleteClientAction',
    ],
    'living_info'=>[
        'class'=>'backend\controllers\QiniuLivingActions\LivingInfoAction',
    ],
];