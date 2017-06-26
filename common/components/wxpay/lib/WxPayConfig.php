<?php
/**
* 	配置账号信息
*/
namespace common\components\wxpay\lib;

class WxPayConfig
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 * 
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 * 
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 * 
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * 
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	const APPID = 'wx50cbf1dc9e28f460';//mibo-real:wx19f6ec4aec39c380 test:wx465dd7091f1904a9  real:wx1024c6215af20360  bh:wx36fb557b6766a884 八卦:wx1024c6215af20360 蜜播娱乐:wx50cbf1dc9e28f460
	const MCHID = '1451566102'; //: 1451566102  real: 1358670802
	const KEY = '4u406ckr11k6s87folqjj21ohjmrzh1h'; //娱乐: 4u406ckr11k6s87folqjj21ohjmrzh1h  real:alssa0kjlHKJH7623174kafjahwesakf
	const APPSECRET = 'd497e6d35d765454530cc9e1045cffdb';//mibo-real:7249f6968a64f60128eb629e37748dcb  test:9f8e18ff2d5fff9855df7b2dbc9a0d31   real:6ef22c0208fda1188b7d5bfae09dfe19  bh:7c3e1c58327ffc4c8c8b2eda68cabe33 八卦:b6e176ce3b2f63b1d0bb13eafc617a82 蜜播娱乐:d497e6d35d765454530cc9e1045cffdb




    //app端支付需要的配置 wxf91a7e689f98d15c  Android
    const APPID_APP = 'wxf91a7e689f98d15c';// real  wxf91a7e689f98d15c  old wx3cf21f506b7cd9ad
    const APPSECRET_APP = '0680cbdae4c7ecef8f92c9fc17654af2';//real 0680cbdae4c7ecef8f92c9fc17654af2    old 18fd6025d3f52d8167e9f973f58c7332
    const MCHID_APP = '1357976702';//real 1357976702    old 1302829601
    const KEY_APP = '98lasfjd9asfd0asdfLASDF90asf8900';//real   98lasfjd9asfd0asdfLASDF90asf8900    old     duwpojfw23j02jrdfjWWEREW49676416

	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const SSLCERT_PATH = '../cert/apiclient_cert.pem';
	const SSLKEY_PATH = '../cert/apiclient_key.pem';
	
	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const CURL_PROXY_PORT = 0;//8080;
	
	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const REPORT_LEVENL = 1;

    const NOTIFY_URL='/wxpay/wxpay_notify'; //test:http://front.matewish.cn/wxpay/wxpay_notify
    const NOTIFY_URL_APP='/wxpay/wxpay_notify_app';

    /**
     * @var array 支付参数
     */
    private static $config = [
        'APPID'        => 'wx99d2096812de10d1', //微信公众号ID
        'MCHID'        => '1421882302', //微信商户号ID
        'APPSECRET'    => '6906f81dbf3fd48bc974ad3a2698581c', //公众号secert
        'KEY'          => 'hangzhoumibokejiwangluoyouxiango', //商户支付密钥
        'NOTIFY_URL'   => '/wxpay/wxpay_notify_app_other', //回调URL
        'SSLCERT_PATH' => '../cert/apiclient_cert.pem', //证书路径cert
        'SSLKEY_PATH'  => '../cert/apiclient_cert.pem', //证书路径key
    ];

    /**
     * 设置支付参数
     * @param $config array
     * @param $value string
     */
    public static function setConfig($config, $value = null)
    {
        if (is_array($config)) {
            self::$config = $config;
        }

        if (!empty($value) && is_string($value)) {
            self::$config[$config] = $value;
        }
    }

    /**
     * 获取支付参数
     * @param $key
     * @return mixed
     */
    public static function getConfig($key = null)
    {
        if (empty($key)) {
            return self::$config;
        }
        return self::$config[$key];
    }
}
