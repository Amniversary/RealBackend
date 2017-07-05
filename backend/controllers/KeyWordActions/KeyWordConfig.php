<?php
return [
    'createkey'=>[
        'class'=>'backend\controllers\KeyWordActions\KeyWordAction'
    ],
    'create'=>[
        'class'=>'backend\controllers\KeyWordActions\CreateAction'
    ],
    'delete'=>[
        'class'=>'backend\controllers\KeyWordActions\DeleteAction'
    ],
    'keyword'=>[
        'class'=>'backend\controllers\KeyWordActions\KeyWordMsgAction'
    ],
    'update'=>[
        'class'=>'backend\controllers\KeyWordActions\UpdateAction'
    ],
    'createmsg'=>[
        'class'=>'backend\controllers\KeyWordActions\CreateMsgAction'
    ],
    'updatemsg'=>[
        'class'=>'backend\controllers\KeyWordActions\UpdateMsgAction'
    ],
    'deletemsg'=>[
        'class'=>'backend\controllers\KeyWordActions\DeleteMsgAction'
    ],
    'check'=>[
        'class'=>'backend\controllers\KeyWordActions\CheckMsgAction'
    ]
];