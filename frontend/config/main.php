<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
    'timeZone'=>'Asia/Shanghai',

    'controllerNamespace' => 'frontend\controllers',
    'components' => [
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
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'=> [
            'enablePrettyUrl'=> true, //开启URL美化
            'showScriptName'=> false,//隐藏index.php
            //'suffix'=> '.html',//后缀，如果设置了此项，那么浏览器地址栏就必须带上.html后缀，否则会报404错误
            'rules'=> [
                'mbapi/response.do'=>'mbapi/doaction',
                'mbapi/testresponse.do'=>'mbapi/testdoaction',
                'mbapi/servercheck.do'=>'mbapi/checkserver',
                'autotest/test.do'=>'autotest/doaction',
                //'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ],
        ],
        'request' => [
            'enableCookieValidation' => false,
            'enableCsrfValidation' => FALSE,
        ],
    ],
    'params' => $params,
];
