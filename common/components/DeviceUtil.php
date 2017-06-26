<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/20
 * Time: 11:33
 */

namespace common\components;


use yii\log\Logger;

class DeviceUtil
{

    /**
     * 判断设备是否合法
     * @param $device_no
     */
    public static function IsErrorDevice($device_no)
    {

        $error_devices = \Yii::$app->params['error_unique_no'];
        if(empty($device_no) || strlen($device_no) < 10)
        {
            \Yii::getLogger()->log('device_no error:['.$device_no.']',Logger::LEVEL_ERROR);
            return true;//设备号不允许小于10位
        }
        $subStr = substr($device_no,0,5);
        $subStr = strtoupper($subStr);
        if(in_array($subStr,$error_devices))
        {
            return true;
        }
        return false;
    }

    /**
     * 获取设备类型  1 android 2 ios 3其他
     * @return string
     */
    public static function GetDeviceType()
    {
        //全部变成小写字母
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $type = '3';
        //分别进行判断
        if(strpos($agent, 'iphone') || strpos($agent, 'ipad'))
        {
            $type = '2';
        }

        if(strpos($agent, 'android'))
        {
            $type = '1';
        }
        return $type;
    }

    /**
     * 获取客户端ip地址
     * @return bool
     */
    public static function GetClientRealIp()
    {
        $ip=false;
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match ('/^(10|172\.16|192\.168)\./i', $ips[$i])) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
    /**
     * 判断是否手机微信浏览器
     * @return bool
     */
    public static function IsMobileWeixinBrowse()
    {
        //手机苹果浏览器
        //Mozilla/5.0 (iPhone; CPU iPhone OS 6_1_3 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10B329 Safari/8536.25
        $str = $_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(micromessenger)/i', strtolower($str))> 0 &&
            preg_match('/(mobile)/i', strtolower($str)) > 0)
        {
            return true;
        }
        return false;
    }

    /**
     * 判断手机还是pc机
     * @return bool
     */
    public static function IsMobile()
    {
        $mobile_browser = '0';

        if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|mqqbrowser)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
        }
        if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
        }
        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
        $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda','xda-');

        if(in_array($mobile_ua,$mobile_agents)) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['ALL_HTTP']),'operamini')>0) {
            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
            $mobile_browser=0;
        }

        if($mobile_browser>0) {
            return true;
            //header("Location: mobile.php"); //手机版
        }else {
            return false;
            //header("Location: pc.php");  //电脑版
        }
    }

    public static function IsGoogleBrowse()
    {
        if(strpos(($_SERVER['HTTP_USER_AGENT']),'Chrome') > 0)
        {
            return true;
        }
        return false;
        /**
         *
         * 谷歌浏览器  75
         * Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)
         * Chrome/48.0.2564.116 Safari/537.36
         *
         * 360浏览器  75
         * Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)
         * Chrome/45.0.2454.101 Safari/537.36
         *
         * QQ浏览器   75
         * Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)
         * Chrome/47.0.2526.80 Safari/537.36 QQBrowser/9.3.6872.400
         *
         * 遨游浏览器  94
         * Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko)
         * Maxthon/4.9.1.1000 Chrome/39.0.2146.0 Safari/537.36
         *
         */
    }
}
/**
 浏览器HTTP_USER_AGENT
 *手机微信浏览器：
Mozilla/5.0 (Linux; U; Android 4.2.2; zh-cn; HUAWEI G750-T00 Build/HuaweiG750-T00)
AppleWebKit/533.1 (KHTML, like Gecko)Version/4.0
MQQBrowser/5.4 TBS/025491 Mobile Safari/533.1
MicroMessenger/6.3.13.49_r4080b63.740 NetType/WIFI Language/zh_CN
 *手机UC浏览器：
Mozilla/5.0 (Linux; U; Android 4.2.2; zh-CN; HUAWEI G750-T00 Build/HuaweiG750-T00)
AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0
UCBrowser/9.9.7.500 U3/0.8.0 Mobile Safari/534.30
 * 手机QQ浏览器
Mozilla/5.0 (Linux; U; Android 4.2.2; zh-cn; HUAWEI G750-T00 Build/HuaweiG750-T00)
AppleWebKit/537.36 (KHTML, like Gecko)Version/4.0 Chrome/37.0.0.0
MQQBrowser/6.3 Mobile Safari/537.36
 *pc谷歌浏览器
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.10 Safari/537.36
 * pc微信浏览器
Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36
MicroMessenger/6.5.2.501 NetType/WIFI WindowsWechat
pc上ie9浏览器
Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)"
 */