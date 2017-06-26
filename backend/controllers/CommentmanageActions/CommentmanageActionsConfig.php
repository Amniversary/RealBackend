<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/22
 * Time: 16:41
 */
return [
    'index' => [
        'class' => 'backend\controllers\CommentmanageActions\IndexAction',
    ],
    'indexhis'=>[
        'class' => 'backend\controllers\CommentmanageActions\IndexHistoryAction',
    ],
    'forbid'=>[
        'class' => 'backend\controllers\CommentmanageActions\ForbidAction',
    ],
    'forbidcomment'=>[
        'class' => 'backend\controllers\CommentmanageActions\ForbidCommentAction',
    ],
    'forbidreward'=>[
        'class' => 'backend\controllers\CommentmanageActions\ForbidRewardAction',
    ],
    'forbidrewardcomment'=>[
        'class' => 'backend\controllers\CommentmanageActions\ForbidRewardCommentAction',
    ],
];