<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=>'zh-CN',
    'timeZone'=>'Asia/Shanghai',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached'=>true,
            'keyPrefix' => 'wc',
              'servers' => [
                  [
                      'host' => '127.0.0.1',
                      'port' => 11211,
                     // 'weight' => 60,
                  ],
              ],
        ],
        'wechat' => [
            'class' => 'callmez\wechat\sdk\Wechat',
            'appId' => 'wx25d7fec30752314f', //test: wx25d7fec30752314f
            'appSecret' => '1ea949d73cdda25dda89566b46a944f0',//test: 1ea949d73cdda25dda89566b46a944f0
            'token' => 'hongbao',
            'encodingAesKey'=>'63n65FMYpIdj2FvUiH7M9rhG0susnRrcKXzZg86h0fK'
        ],
        /*'im' => [
            'class'  => 'common\components\rongcloudsdk\RongCloud',
            'appKey' => 'qd46yzrfqd75f',  //test : qd46yzrfqd75f
            'appSecret' => 'VwHess4AwGTDRD' //test : VwHess4AwGTDRD
        ],*/
	    'beanstalk'=>[
            'class' => 'udokmeci\yii2beanstalk\Beanstalk',
            'host'=> "127.0.0.1", // default host 127.0.0.1 "192.168.2.108",
            'port'=>11300, //default port
            'connectTimeout'=> 1,
            'sleep' => 0, // or int for usleep after every job
        ],
        'wechatBeanstalk'=>[
            'class'=>'udokmeci\yii2beanstalk\Beanstalk',
            'host'=>"127.0.0.1",
            'port'=>11301,
            'connectTimeout'=>1,
            'sleep'=> 0,
        ]
    ],
];
