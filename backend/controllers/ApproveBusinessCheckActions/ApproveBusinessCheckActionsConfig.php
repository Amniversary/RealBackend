<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/5/12
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\ApproveBusinessCheckActions\IndexAction',
    ],
    'detail'=>[
        'class'=>  'backend\controllers\ApproveBusinessCheckActions\DetailAction',
    ],
    'checkrefuse'=>[
        'class'=>  'backend\controllers\ApproveBusinessCheckActions\CheckRefuseAction',
    ],
    'indexaudited'=>[
        'class'=>  'backend\controllers\ApproveBusinessCheckActions\IndexAuditedAction',
    ],
    'checkbatch'=>[
        'class'=>  'backend\controllers\ApproveBusinessCheckActions\CheckBatchBusinessCheckActions',
    ],




];