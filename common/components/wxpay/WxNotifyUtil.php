<?php
namespace common\components\wxpay;

class WxNotifyUtil
{
    public static function DealWxNotify()
    {
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }

    public static function DealWxNotifyApp()
    {
        $notify = new AppPayNotifyCallBack();
        $notify->Handle(false);
        /*
         * 测试示例
<xml><appid><![CDATA[wx3cf21f506b7cd9ad]]></appid>
<attach><![CDATA[test=test&type=app]]></attach>
<bank_type><![CDATA[CFT]]></bank_type>
<cash_fee><![CDATA[1]]></cash_fee>
<fee_type><![CDATA[CNY]]></fee_type>
<is_subscribe><![CDATA[N]]></is_subscribe>
<mch_id><![CDATA[1302829601]]></mch_id>
<nonce_str><![CDATA[0sjgmkvsfckaf198m9a32tds3ee0x9zi]]></nonce_str>
<openid><![CDATA[orIt2uOwy_8K9vlHOqQJhkxAZHf0]]></openid>
<out_trade_no><![CDATA[130283730120160223160409]]></out_trade_no>
<result_code><![CDATA[SUCCESS]]></result_code>
<return_code><![CDATA[SUCCESS]]></return_code>
<sign><![CDATA[AD5B2496D496D752322C99CC90F9CE87]]></sign>
<time_end><![CDATA[20160223160415]]></time_end>
<total_fee>1</total_fee>
<trade_type><![CDATA[APP]]></trade_type>
<transaction_id><![CDATA[1010210363201602233477896074]]></transaction_id>
</xml>
         */
    }


    public static function DealWxNotifyAppOther()
    {
        $notify = new OtherPayNotifyCallBack();
        $notify->Handle(false);
    }
}

/*
 * 测试示例
 <xml>
  <appid><![CDATA[wx2421b1c4370ec43b]]></appid>
  <attach><![CDATA[支付测试]]></attach>
  <bank_type><![CDATA[CFT]]></bank_type>
  <fee_type><![CDATA[CNY]]></fee_type>
  <is_subscribe><![CDATA[Y]]></is_subscribe>
  <mch_id><![CDATA[10000100]]></mch_id>
  <nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
  <openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
  <out_trade_no><![CDATA[1409811653]]></out_trade_no>
  <result_code><![CDATA[SUCCESS]]></result_code>
  <return_code><![CDATA[SUCCESS]]></return_code>
  <sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
  <sub_mch_id><![CDATA[10000100]]></sub_mch_id>
  <time_end><![CDATA[20140903131540]]></time_end>
  <total_fee>1</total_fee>
  <trade_type><![CDATA[JSAPI]]></trade_type>
  <transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
</xml>
 */