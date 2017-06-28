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
	const APPID = 'wx25d7fec30752314f';//
	const MCHID = ''; //
	const KEY = ''; //
	const APPSECRET = '1ea949d73cdda25dda89566b46a944f0';//
    const ENCRYPT_KEY = '63n65FMYpIdj2FvUiH7M9rhG0susnRrcKXzZg86h0fK';
    const TOKEN = 'hongbao';//





    //app端支付需要的配置 wxf91a7e689f98d15c  Android
    const APPID_APP = '';//
    const APPSECRET_APP = '';//
    const MCHID_APP = '';//
    const KEY_APP = '';//

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
        'APPID'        => '', //微信公众号ID
        'MCHID'        => '', //微信商户号ID
        'APPSECRET'    => '', //公众号secert
        'KEY'          => '', //商户支付密钥
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
