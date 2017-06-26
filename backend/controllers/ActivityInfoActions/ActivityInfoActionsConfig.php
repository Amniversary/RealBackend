<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 16:00
 */

return [
	'index' => [
		'class' => 'backend\controllers\ActivityInfoActions\IndexAction'
	],
	'create' => [
		'class' => 'backend\controllers\ActivityInfoActions\CreateAction'
	],
	'update' => [
		'class' => 'backend\controllers\ActivityInfoActions\UpdateAction'
	],
	'delete' => [
		'class' => 'backend\controllers\ActivityInfoActions\DeleteAction'
	],
    'enroll_index' => [
        'class' => 'backend\controllers\ActivityInfoActions\IndexCheckAction'
    ],
    'set_status' => [
        'class' => 'backend\controllers\ActivityInfoActions\SetStatusAction'
    ]
    ,
    'enroll_already' => [
        'class' => 'backend\controllers\ActivityInfoActions\AlreadyCheckAction'
    ]
];