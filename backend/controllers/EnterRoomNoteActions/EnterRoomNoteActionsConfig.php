<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/6/6
 * Time: 10:00
 */

return [
    'index'=>[
        'class'=> 'backend\controllers\EnterRoomNoteActions\IndexAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\EnterRoomNoteActions\DeleteAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\EnterRoomNoteActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\EnterRoomNoteActions\CreateAction',
    ],
    'setstatus'=>[
        'class'=>  'backend\controllers\EnterRoomNoteActions\SetStatusAction',
    ],
    'black'=>[
        'class'=>  'backend\controllers\EnterRoomNoteActions\BlackAction',
    ],

];