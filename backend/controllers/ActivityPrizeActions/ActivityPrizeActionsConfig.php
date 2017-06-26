<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 16:00
 */

return [
	'index' => [
		'class' => 'backend\controllers\ActivityPrizeActions\IndexAction'
	],
	'create' => [
		'class' => 'backend\controllers\ActivityPrizeActions\CreateAction'
	],
	'update' => [
		'class' => 'backend\controllers\ActivityPrizeActions\UpdateAction'
	],
	'delete' => [
		'class' => 'backend\controllers\ActivityPrizeActions\DeleteAction'
	],
    'statistic' => [
        'class' => 'backend\controllers\ActivityPrizeActions\StatisticAction'
    ],
    'set_attributes' => [
        'class' => 'backend\controllers\ActivityPrizeActions\SetAttributesAction'
    ],
    'prize_record'=>[
        'class' => 'backend\controllers\ActivityPrizeActions\ActivityPrizeSendAction',
    ],
    'set_prize_record'=>[
        'class' => 'backend\controllers\ActivityPrizeActions\SetPrizeRecordAction',
    ],
];