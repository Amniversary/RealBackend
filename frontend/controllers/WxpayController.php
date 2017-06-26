<?php
namespace frontend\controllers;

use common\components\wxpay\WxAppPay;
use common\components\wxpay\WxNotifyUtil;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\wxpay\lib\WxPayApi;
use common\components\wxpay\lib\WxPayConfig;
use common\components\wxpay\lib\WxPayUnifiedOrder;
use common\components\wxpay\JsApiPay;

/**
 * WxpayController
 */
class WxpayController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['wxpay_notify','getapppayparams','wxpay_notify_app', 'wxpay_notify_app_other'],
                'rules' => [
                    [
                        'actions' => ['wxpay_notify','getapppayparams','wxpay_notify_app', 'wxpay_notify_app_other'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'wxpay_notify' => ['post'],
                    'getapppayparams'=>['post','get'],
                    'wxpay_notify_app'=>['post'],
                    'wxpay_notify_app_other'=>['post'],
                ],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionWxpay_notify()
    {
        set_time_limit(0);
        WxNotifyUtil::DealWxNotify();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionWxpay_notify_app()
    {
        set_time_limit(0);
        //http://front.matewish.cn/wxpay/wxpay_notify_app
        WxNotifyUtil::DealWxNotifyApp();
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionWxpay_notify_app_other()
    {
        set_time_limit(0);
        //http://front.matewish.cn/wxpay/wxpay_notify_app
        WxNotifyUtil::DealWxNotifyAppOther();
    }

    public function printf_info($data)
    {
        foreach($data as $key=>$value){
            echo "<font color='#00ff55;'>$key</font> : $value <br/>";
        }
    }

    public function actionTestpay()
    {
        //http://front.matewish.cn/wxpay/testpay
        $this->layout = 'empty_layout';
        //require_once "WxPay.JsApiPay.php";
        //require_once 'log.php';

//初始化日志
/*        $logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
        $log = Log::Init($logHandler, 15);*/

//打印输出数组信息


//①、获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

//②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        //$input->SetNotify_url(WxPayConfig::NOTIFY_URL);
        $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL);
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        //var_dump($input);exit;
        $order = WxPayApi::unifiedOrder($input);
        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
        $this->printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();
        return $this->render('test',[
                'jsApiParameters'=>$jsApiParameters,
                'editAddress'=>$editAddress
            ]
            );
    }

    public function actionGetapppayparams()
    {
        exit;
        //http://front.meiyuan.com/wxpay/getapppayparams
        //http://front.matewish.cn/wxpay/getapppayparams
        $tools = new WxAppPay();
//②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test=test&type=app");
        $input->SetOut_trade_no(WxPayConfig::MCHID_APP.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url('http://'.$_SERVER['HTTP_HOST'].WxPayConfig::NOTIFY_URL_APP);
        $input->SetTrade_type("APP");
        //var_dump($input);exit;
        $order = WxPayApi::unifiedOrderForApp($input);
        //$this->printf_info($order);
        $appPayParameters = $tools->GetAppPayParameters($order);
        echo $appPayParameters;
    }
}
