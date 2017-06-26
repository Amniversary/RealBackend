<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 14:59
 */
return [
    'index'=>[
        'class'=> 'backend\controllers\ScoreGiftActions\IndexAction',
    ],
    'update'=>[
        'class'=> 'backend\controllers\ScoreGiftActions\UpdateAction',
    ],
    'create'=>[
        'class'=> 'backend\controllers\ScoreGiftActions\CreateAction',
    ],
    'delete'=>[
        'class'=> 'backend\controllers\ScoreGiftActions\DeleteAction',
    ],
    'gift_score_index' =>[
        'class'=>'backend\controllers\ScoreGiftActions\ScoreIndexAction',
    ],
    'score_create'=> [
        'class' =>'backend\controllers\ScoreGiftActions\ScoreCreateAction',
    ],
    'score_delete'=> [
        'class'=>'backend\controllers\ScoreGiftActions\ScoreDeleteAction',
    ],
    'set_score'=>[
        'class'=>'backend\controllers\ScoreGiftActions\SetScoreAction',
    ],
];