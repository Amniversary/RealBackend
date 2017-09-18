<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'name'=>'Real数据平台', //TODO: 系统项目名称
    'id' => 'app-backend', //TODO: 项目ID
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'language'=>'zh-CN',
    'timeZone'=>'Asia/Shanghai',
    'bootstrap' => ['log','gridview'],
    'modules' => [
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            'downloadAction' => 'gridview/export/download',
            'i18n' => [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@kvgrid/messages',
                'forceTranslation' => true
                ],
        ]
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'someModule.*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ]
            ]
        ],
        'assetManager' => [
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-blue',
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                    //TODO:  logVars:  _GET, _POST, _FILES, _COOKIE, _SESSION, _SERVER
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'=> [
            'enablePrettyUrl'=> true,
            'showScriptName'=> false,//隐藏index.php
            //'suffix'=> '.html',//后缀，如果设置了此项，那么浏览器地址栏就必须带上.html后缀，否则会报404错误
            'rules'=> [
                'wechat/<appid:\w+>/callback'=>'wechat/callback',
            ],
        ],
    ],
    'params' => $params,
];
