<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/13
 * Time: 11:47
 */

namespace frontend\controllers;

use backend\models\UserContactSearch;
use common\components\OssUtil;
use yii\web\Controller;
use common\components\tenxunlivingsdk\TimRestApi;
use yii\web\User;

//use yii\redis\Connection;

class Mytest1Controller extends Controller
{

    private $data = [];

    private $defaultData = [
        'app_id'        => '1119990982',
        'device_no'     => '273492027402342s3',
        'device_type'   => 2,
        'data'          => []
    ];

    private $apiNamespace = 'frontend\zhiboapi\v1\\';

    /**
     * 测试方法
     */
    private function testFunctionExcute($functionName)
    {
        $result    = null;
        $msg       = null;
        $className = $this->apiNamespace . $functionName;

        $class = new $className();
        $class->excute_action($this->data, $result, $msg);


        if (function_exists('ladybug_dump')) {
            ladybug_dump($result);
            ladybug_dump($msg);
        } else {
            echo '<span style="color:#080">'.$functionName.'</span>';
            echo '<br/>';
            var_dump($result);
            echo '<br/>';
            var_dump($msg);
        }
    }

    private function setData($data)
    {
        $this->data = array_merge($this->defaultData, $data);
        return $this;
    }

    /**
     * test:手机登录 ZhiBoLoginQiNiu
     */
    public function actionLogin()
    {
        $data = [
            'data'          => [
                'unique_no' => '13884732861',
                'getui_id'  => '1',
                'register_type' => 4,
                'validate_code' => '6584',
                'pic'       => '',
                'nick_name' => '',
                'sex'       => '',
                'other_unique_no' => ''
            ]
        ];

        //伪造验证码
        \Yii::$app->cache->set(
            'mb_api_verifycode_1_' . $data['data']['unique_no'],
            $data['data']['validate_code']
        );

        $this->setData($data)->testFunctionExcute('ZhiBoLoginQiNiu');

        $cache = \Yii::$app->cache->get('mb_api_login_' . $data['data']['unique_no']);
        var_dump($cache);
    }

    /**
     * test:获取验证码 ZhiBoGetValidateCode
     */
    public function actionGetvalidatecode()
    {
        $data = [
            'data' => [
                'phone_no'  => '13884732861',
                'code_type' => 1
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetValidateCode');
    }

    /**
     * test:完善个人信息 ZhiBoUpdateClient
     */
    public function actionUpdateclient()
    {
        $data = [
            'data' => [
                'unique_no'     => '13884732861',
                'register_type' => 1,
                'nick_name'     => 'zjm',
                'pic'           => 'image' . rand(10000, 20000),
                'sex'           => '男',
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoUpdateClient');
    }

    /**
     * test:队列
     */
    public function actionBeanstalk()
    {
        $data = null;
        $error = null;
        $key_word = 'my_test';
        $data = [
            'client_id' => '12345',
            'pic' => 'test',
        ];

        $beanstalk = \Yii::$app->dealPicBeanstalk;
        //$result = $beanstalk->putInTube($key_word, $data);
        //$result = \frontend\business\JobUtil::AddPicJob($key_word, $data, $error);

        //ladybug_dump($result);
        //ladybug_dump($error);

        //$beanstalk->watch($key_word);
        //ladybug_dump($beanstalk->reserveFromTube($key_word));

        //$beanstalk->useTube($key_word);
        //echo json_encode($data);
        $beanstalk->useTube($key_word);
        $jobId = $beanstalk->put('{"client_id":"12345","pic":"test"}');


        $beanstalk->watch($key_word);
        $job = $beanstalk->peek($jobId);
        var_dump($job->getId());
        var_dump(json_encode($job->getData()));

        //$beanstalk->delete($job);

        //ladybug_dump($beanstalk->statsJob($job));
    }

    public function actionPhpinfo()
    {
        phpinfo();
    }

    /**
     * test:获取个人资料
     */
    public function actionGet_client_info()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'fields' => '',
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetClientInfo');
    }

    /**
     * test:获取多人资料
     */
    public function actionGet_multi_client_info()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'user_id'   => ['27', '29'],
                'fields'    => '',
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetMultiClientInfo');
    }

    /**
     * test:关注
     */
    public function actionAttention()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'attention_id' => '2',
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoAttention');
    }

    /**
     * test:获取内购商品
     */
    public function actionGet_goods()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'sale_type' => 8,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetGoods');
    }

    /**
     * test:第三方支付支付前参数请求
     */
    public function actionGet_other_pay_params()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'pay_type' => 20, //wechat
                'pay_target' => 'recharge', //recharge
                'params' => ['goods_id' => 3]
            ]
        ];

        $config = array(
            'APPID_APP' => 'wx99d2096812de10d1',
            'MCHID_APP' => '1421882302',
            'APPSECRET_APP' => '6906f81dbf3fd48bc974ad3a2698581c',
            'KEY_APP' => 'hangzhoumibokejiwangluoyouxiango',
        );
        //\common\components\wxpay\lib\WxPayConfig::setConfig($config);

        $this->setData($data)->testFunctionExcute('ZhiBoGetOtherPayParams');
    }

    /**
     * test:票提现
     */
    public function actionTicket_to_cash()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'money_value' => 1,
                'cash_type' => 2,
                'op_unique_no' => '1234'
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoTicketToCash');
    }

    /**
     * test:牛牛开始游戏
     */
    public function actionStart_niuniu_game()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'game_id' => 1,
                'living_id' => '4650',
            ]
        ];
        $this->setData($data)->testFunctionExcute('niuniu\ZhiBoStartNiuNiuGame');
    }

    public function actionGet_hot_living()
    {
        $data = [
            'app_id'      => '1171658345',
            'action_name' => 'get_hot_living',
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'page_no' => 1,
                'page_size' => 20,
            ]
        ];
        echo '<pre>';
        //$this->setData($data)->testFunctionExcute('ZhiBoGetHotLiving');

        //$data['action_name'] = 'get_attention_living';
        //$this->setData($data)->testFunctionExcute('ZhiBoGetAttentionLiving');

        $data['action_name'] = 'get_newest_living';
        $this->setData($data)->testFunctionExcute('ZhiBoGetNewestLiving');
    }

    /**
     * test:创建直播
     */
    public function actionQiniu_create_living()
    {
        $data = [
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'page_no' => 1,
                'page_size' => 20,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoQiNiuCreateLiving');
    }

    /**
     * test:心跳
     */
    public function actionHeart_beat()
    {

    }

    /**
     * test:获取支付方式
     */
    public function actionGet_payments()
    {
        $data = [
            'action_name' => 'get_payments',
            'app_version_inner' => 1,
            'device_type' => 2,
            'app_id' => '1177913201',
            'data' => [
                'unique_no' => '13884732861',
                'register_type' => 1,
                'page_no' => 1,
                'page_size' => 20,
            ]
        ];
        echo '<pre>';
        $this->setData($data)->testFunctionExcute('ZhiBoGetPayments');
    }

    public function actionTestsysparam()
    {
        $star = microtime();
        for ($i = 0; $i < 100000; $i++) {
            \common\components\SystemParamsUtil::getSystemParamWithOne('public_living');
        }
        echo microtime() - $star;
    }

    public function actionNo_words()
    {
        $data = [
            'action_name' => 'get_payments',
            'app_version_inner' => 2,
            'device_type' => 2,
            'data' => [
                'user_id' => 253582,
                'living_id' => 13841,
                'op_type' => 1,
                'unique_no' => '13884732861',
                'register_type' => 1,
                'page_no' => 1,
                'page_size' => 20,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoNoWords');
    }

    public function actionBan_client()
    {
        $data = [
            'data' => [
                'unique_no'     => '13884732861',
                'register_type' => 1,
                'client_id'     => 1,
                'ban_content'   => 20,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoBanClient');
    }

    public function actionGet_im_config()
    {
        TimRestApi::init();
        echo '<pre>';
        var_dump(TimRestApi::getParams());
    }

    public function actionUpdate_key()
    {
        $data = [
            'data' => [
                'unique_no'     => '13884732861',
                'register_type' => 1,
                'client_id'     => 1,
                'ban_content'   => 20,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoUpdateKey');
    }

    public function actionIos_buy_verify()
    {
        //echo time();
        echo strtotime('2017-03-08 00:00:10');
        exit;
        echo date('Y-m-d H:i:s', 1488860000);
        $unique_no = \Yii::$app->request->post('unique_no');
        $receipt_data = \Yii::$app->request->post('receipt_data');

        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no'     => $unique_no,
                'pay_type'      => 6,
                'goods_id'      => 10,
                'receipt-data'  => $receipt_data
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoIosBuyVerify');
    }

    public function actionTicket_to_bean()
    {
        $data = [
            'data' => [
                'unique_no'     => '13884732861',
                'register_type' => 1,
                'bean_goods_id' => 4,
                'op_unique_no'  => '221'
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoTicketToBean');
    }

    public function actionTest_redis()
    {
        $redisDll = new \Redis();
        $redisDll->open('localhost');

        $redisYii = new \yii\redis\Connection();
        $redisYii->hostname = 'localhost';
        $redisYii->open();

        $c = 100;
        $cal = function($str) {
            return array_sum(explode(' ', $str));
        };
        $t1 = $cal(microtime());

        for ($i = 0; $i < $c; $i++) {
            $redisDll->get('first');
        }

        $t2 = $cal(microtime());

        for ($i = 0; $i < $c; $i++) {
            $redisYii->get('first');
        }

        $t3 = $cal(microtime());

        echo 'by dll  ' . ($t2 - $t1) . '<br>';
        echo 'by yii  ' . ($t3 - $t2) . '<br>';
    }

    public function actionApple()
    {
        var_dump(\common\components\IOSBuyUtil::GetIosBuyVerify('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'));
    }

    public function actionRongcloud()
    {
        // $u = \Yii::$app->im->User();
        $g = \Yii::$app->im->User();

        // 创建群
        //$rst = $g->create('test1000', '00001', '第一个群');

        $rst = $g->getToken('test1001', 'aaa', 'aa');

        echo '<pre>';
        if (!$rst) {
            var_dump($u->getErrorMessage());
            var_dump($u->getErrorCode());
        }
        var_dump($rst);
    }

    public function actionGroup_manager()
    {
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no'     => '13884732861',
                'user_id'       => 1632,
                'living_id'     => 12344,
                'op_type'       => 1,
                'register_type' => 2
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGroupManager');
    }

    public function actionTest11()
    {
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'living_id' => 14040,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetLivingRtmpUrl');
    }

    public function actionGroup_create()
    {
        $gg = new \frontend\business\RongCloud\GroupUtil();
        var_dump($gg->testGetGroupMember(11252137));
        exit;

        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'user_id' => 14040,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoCreateFansGroup');
    }

    public function actionGroup_apply()
    {
        $a = microtime();
        $a = explode(' ', $a);
        $s = $a[0] + $a[1];
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'user_id' => 41,
                'group_id' => '76',
                'apply_status' => 1
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoFansApplyApprove');
        $a = microtime();
        $a = explode(' ', $a);
        $e = $a[0] + $a[1];
        echo $e - $s;
    }

    public function actionGroup_kicking()
    {

        $gg = new \frontend\business\RongCloud\GroupUtil();
        var_dump($gg->testGetGroupMember(11470643));

        exit;
        $a = microtime();
        $a = explode(' ', $a);
        $s = $a[0] + $a[1];
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'user_id' => 41,
                'group_id' => '76'
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoKickingFans');
        $a = microtime();
        $a = explode(' ', $a);
        $e = $a[0] + $a[1];
        echo $e - $s;
    }

    public function actionTest_chat()
    {
        $chatroom = \Yii::$app->im->Chatroom();
        $user = \Yii::$app->im->User();
        $message = \Yii::$app->im->Message();



        echo '<pre>';
        echo '创建聊天室', PHP_EOL;
        // var_dump($user->getToken(110, 'zjm110', 'a'));

        // var_dump($user->getToken(100, 'zjm100', 'a'));

        // var_dump($chatroom->create([2000 => '阿满的chatroom']));

        // var_dump($chatroom->join(100, 2000));
        // var_dump($chatroom->getErrorMessage());

        var_dump($message->publishChatroom(110, 7558, 'RC:TxtMsg', '{"content": "hello"}'));
        var_dump($message->getErrorMessage());

        var_dump($chatroom->queryUser(7558, 500, 2));

        // var_dump($chatroom->query(2000));
    }

    function actionUploadoss()
    {
        $dir = \Yii::$app->getBasePath().'/web/tttt';
        $picList = [];
        $files = scandir($dir);
        // print_r($files);
        $picStrList = '';
        foreach($files as $file)
        {
            $items = explode('\\',$file);
            $len = count($items);
            $file_name = $items[$len -1];
            if(strpos($file_name,'.jpg') === false)
            {
                continue;
            }
            $file = $dir.'/'.$file;
            $fName = str_replace('.jpg','',$file_name);
            $suffix = 'jpg';
            $picUrl = '';
            $error = '';
            /**
            echo "<pre>";
            var_dump($fName);
            echo "<br>";
            var_dump($suffix);
            echo "<br>";
            var_dump($file);
             * */

            if(!OssUtil::UploadFile($fName,$suffix,'test',$file,$picUrl,$error))
            {
                var_dump($error);
                exit;
            }
            $picStrList .= $picUrl."\r\n";
            $picList[]=$picUrl;
        }
        var_dump($picList);
        $fileStore = $dir.'/picurl.txt';
        file_put_contents($fileStore,$picStrList);
        exit;
    }

    public function actionSend()
    {
        $mm = new \frontend\business\RongCloud\SystemMessageUtil();
        // $mm->sendBroadcastMessage('这是一条广播消息', '你收到一条广播消息');
        /*
        $mm->sendGeneralMessage(253639, '评论了你的照片: 真的不怎么好看！111', [
            'user_client_id' => 253744,
            'user_nick_name' => '额呃呃呃',
            'user_icon_pic'  => 'http://oss-cn-hangzhou.aliyuncs.com/mblive-demo/client-pic/ocimg_58b62fa79a675.jpg',
            'dynamic_id'     => 803,
            'dynamic_pic'    => 'http://mbpic.mblive.cn/user/c23049e4a14558a34c318ff2061af460.jpg',
            'create_time'    => date('m月d日 H:i'),
        ]);
        */
        // $mm->sendGeneralInformation(253639, 'test', ['user_id' => -1]);
        // $mm->sendSystemMessage(253639, 'as萨芬萨芬艾丝凡是否as发生放');
        $mm->sendGroupMessage(1696, '群消息申请加入群');
    }

    public function actionBan_client_test()
    {
        $mm = new \frontend\business\RongCloud\ChatroomMessageUtil();
        var_dump($mm->sendBanClientMessage(7558, [
            'attend_user_count' => 1000,
            'tickets_num' => 500,
            'living_time' => 20
        ]));
    }

    public function actionGroup_search()
    {
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'key_word' => 11470643,
                'page_no' => 1,
                'page_size' => 20,
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoFansGroupSearch');
    }

    public function actionTaa()
    {
        $data = [
            'app_version_inner' => '1',
            'data' => [
                'unique_no' => '13884732861',
                'living_id' => 9021,
                'register_type' => '2',
                'room_no' => 1
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoGetViewsShare');
    }

    public function actionTaa2()
    {
        $chatroomManager = \Yii::$app->im->Chatroom();

        $chatroomManager->rollbackGagUser(253630, 7558);


        var_dump($chatroomManager->ListGagUser(7558));
    }

    public function actionPay()
    {
        $trade = new \common\components\xiaoxiaopay\Trade();
        $parmas = [
            'waresname' => 'test1',
            'cporderid' => 3213544354358, // md5(time()).rand(10000,99999)
            'price'     => number_format(0.01, 2),
            'paytype'   => 10008,
        ];
        $paymentData = $trade->getAppSign($parmas);
        $parmas['notifyUrl'] = \common\components\xiaoxiaopay\Config::$notifyUrl;
        $parmas['sign'] = $paymentData['info']['signValue'];
        \Yii::error(var_export($parmas, true));
        echo '<pre>';
        var_export($parmas);
    }

    public function actionTest_send()
    {
        // 601663
        $bb = array(
            1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,18,19,20,21,22,23,24,26
        );
        $aa = array_rand($bb);
        var_dump($bb[$aa]);

        exit;
        $data = [
            'app_version_inner' => '1',
            'data' => [
                "unique_no"=>"15857108643",
                "register_type"=>"1",
                "op_unique_no"=>"111ty111sazzz11zxz0g1a" . time() . rand(10000,99999),
                "gift_id"=>"1",
                "money_type"=>"1",
                "living_id"=>"1310"
            ]
        ];
        $this->setData($data)->testFunctionExcute('ZhiBoSendGift');
    }
}
