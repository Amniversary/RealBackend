<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/12
 * Time: 14:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\SendMsgActions\IndexAction',
    ],
    'send'=>[
        'class'=> 'backend\controllers\SendMsgActions\SendAction',
    ],
    'upload'=>[
        'class'=> 'backend\controllers\SendMsgActions\UploadFileAction',
    ],
];