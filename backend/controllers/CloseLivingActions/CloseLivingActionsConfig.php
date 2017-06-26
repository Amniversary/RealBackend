<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 16:00
 */

return [
	'index' => [
		'class' => 'backend\controllers\CloseLivingActions\IndexAction'
	],
	'delete' => [
		'class' => 'backend\controllers\CloseLivingActions\DeleteAction'
	],
    'close_index'=>[
        'class'=> 'backend\controllers\CloseLivingActions\CloseIndexAction',
    ],
    'wechat_live'=>[
        'class'=> 'backend\controllers\CloseLivingActions\WeChatLiveOffAction',
    ],
    'close_open_index'=>[
        'class'=> 'backend\controllers\CloseLivingActions\CloseOpenIndexAction',
    ],
];