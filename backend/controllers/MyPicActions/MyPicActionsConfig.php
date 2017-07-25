<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:31
 */
return [
    'upload_pic' => [
        'class' => 'backend\controllers\MyPicActions\UploadAction',
    ],
    'get'=>[
        'class' => 'backend\controllers\MyPicActions\GetAction',
    ],
    'upload_video'=> [
        'class' => 'backend\controllers\MyPicActions\UploadMp3Action',
    ]
];