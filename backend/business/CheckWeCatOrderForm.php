<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 2016/06/27
 * Time: 11:51
 */

namespace backend\business;

use common\components\wxpay\lib\WxPayConfig;
use frontend\business\TicketToCashUtil;

class CheckWeCatOrderForm
{
    public static $values = array();

    /**
     * 查询微信订单
     * @param $out_trade_no   商户订单号
     * @param $error
     * @return bool
     */
    public static function CheckOrder($out_trade_no,&$outInfo)
    {
        self::$values['appid'] = WxPayConfig::APPID;
        self::$values['mch_id'] = WxPayConfig::MCHID;
        self::$values['out_trade_no'] = $out_trade_no;
        self::$values['nonce_str'] = TicketToCashUtil::GetNonceStr();
        $sign = TicketToCashUtil::SetWeChatSign(self::$values);
        self::$values['sign'] = $sign;
        $toXml = TicketToCashUtil::arrayToXml(self::$values);
        $url = 'https://api.mch.weixin.qq.com/pay/orderquery';  //查询订单地址
        $sendResult = TicketToCashUtil::curl_post_ssl($url,$toXml);
        $res = json_decode(json_encode(simplexml_load_string($sendResult, 'SimpleXMLElement', LIBXML_NOCDATA)),true); //创建 SimpleXML对象

        if($res['return_code'] !== 'SUCCESS'){
            $status = 0;
        }else
        {
            switch($res['trade_state'])
            {
                case 'SUCCESS':
                    $status = 1;    //支付成功
                    break;
                case 'USERPAYING':
                    $status = 3;   //用户支付中
                    break;
                default:
                    $status = 4;   //失败
            }
        }

        $outInfo['trade_no'] = $res['transaction_id'];   //微信单号
        $outInfo['trade_state_desc'] = $res['trade_state_desc'];   //状态描述
        return $status;


    }
}