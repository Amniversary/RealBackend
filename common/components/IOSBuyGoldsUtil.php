<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/24
 * Time: 9:43
 */

namespace common\components;
use yii\log\Logger;

/**
 * 苹果内购辅助类
 * Class IOSBuyUtil
 * @package common\components
 */
class IOSBuyGoldsUtil
{
    /**
     * 获取苹果内购验证结果
     * @param $receipt_data
     * @param bool $is_sandbox
     * 返回的status状态：
     * 0 交易成功
     * 7788 curl返回空
     * 21000 App Store不能读取你提供的JSON对象
     * 21002 receipt-data域的数据有问题
     * 21003 receipt无法通过验证
     * 21004 提供的shared secret不匹配你账号中的shared secret
     * 21005 receipt服务器当前不可用
     * 21006 receipt合法，但是订阅已过期。服务器接收到这个状态码时，receipt数据仍然会解码并一起发送
     * 21007 receipt是Sandbox receipt，但却发送至生产系统的验证服务
     * 21008 receipt是生产receipt，但却发送至Sandbox环境的验证服务
     */
    public static function GetIosBuyVerify($receipt_data,$is_sandbox = true)
    {
        $url_sandbox = "https://sandbox.itunes.apple.com/verifyReceipt";
        $real_url = 'https://buy.itunes.apple.com/verifyReceipt';//正式url
        $url = $real_url;
        if($is_sandbox)
        {
            $url = $url_sandbox;
        }
        $data = ['receipt-data' => $receipt_data];
        $data =json_encode($data);
        $data = UsualFunForNetWorkHelper::HttpsPost($url,$data);
        \Yii::getLogger()->log('ios-retrun=====>'.var_export($data,true), Logger::LEVEL_ERROR);
        if(empty($data))
        {
            $data='{"status":"7788"}';
        }
        $data = json_decode($data,true);
        $iosbuygoods = require(  dirname(__DIR__).'/config/IosBuyGoodsList.php'); //苹果内购商品名称及价格对应信息
        if($data['status'] == 1 )
        {
            $new_data['status'] = 2;
        }
        if($data['status'] == 0 )
        {
            $new_data['status'] = 1;
        }
        if($data['status'] == 21007 )
        {
            $new_data['status'] = 1;
        }
        $new_data['trade_no'] = $data['receipt']['in_app'][0]['transaction_id']; //苹果返回的单号
        $new_data['total_fee'] = $iosbuygoods[$data['receipt']['in_app'][0]['product_id']];  //价格
        return $new_data;
    }
} 