<?php
return [
    'index' => 'backend\controllers\PublicListActions\IndexAction',
    'create' => 'backend\controllers\PublicListActions\CreateAction',
    'attention' => 'backend\controllers\PublicListActions\AttentionReplyAction',
    'status' => 'backend\controllers\PublicListActions\StatusCacheAction',
    'createmsg' => 'backend\controllers\PublicListActions\CreateMsgAction',
    'delete' => 'backend\controllers\PublicListActions\DeleteEventAction',
    'update' => 'backend\controllers\PublicListActions\UpdateAction',
    'set_count' => 'backend\controllers\PublicListActions\SetCountAction',
    'order_no' => 'backend\controllers\PublicListActions\OrderNoAction',
    'get_tag_list' => 'backend\controllers\PublicListActions\GetTagListAction',
    'set_tag_list' => 'backend\controllers\PublicListActions\SetTagListAction',
    'set_alarm' => 'backend\controllers\PublicListActions\SetAlarmAction',
];