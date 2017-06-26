<?php
/**
 * 七牛直播基于时间戳防盗链
 * User: zhoujiaman
 * Date: 2017/2/22
 */

namespace common\components;

class CdnAntiLeech
{
    // 默认的URL过期时间（5分钟），单位秒
    public static $defaultDuration = 300;

    // 跟七牛约定好的加密字符串，可以联系七牛技术修改
    public static $key = 'WEBXT436NMBIMYJ000ZVK5499XAXP51K';

    // 用于测试的节点，如果设置则访问测试的节点，正式环境设为 null
    public static $test = '180.97.72.174';

    /**
     * 过期签名
     * @param string $url 原始外链
     * @param null|int $duration 过期时间，不设置将使用默认值，单位秒
     * @return string
     */
    public static function signOnTimestamp($url, $duration = null)
    {
        $urlParse = parse_url($url);
        !$duration && $duration = self::$defaultDuration;

        $path = isset($urlParse['path']) ? $urlParse['path'] : '';
        // UNIX 十进制转换为十六进制
        $time = dechex(time() + $duration);
        // md5加密，并且转换为小写字母
        $sign = strtolower(md5(self::$key . $path . $time));

        $rstUrlArray = $urlParse;
        if (isset($rstUrlArray['query'])) {
            $rstUrlArray['query'] .= '&';
        } else {
            $rstUrlArray['query'] = '';
        }
        $rstUrlArray['query'] .= "sign={$sign}&t={$time}";

        // 测试节点
        if (!empty(self::$test)) {
            $rstUrlArray['query'] .= '&domain=' . $rstUrlArray['host'];
            $rstUrlArray['host'] = self::$test;
        }

        $rstUrl = (isset($rstUrlArray['scheme']) ? $rstUrlArray['scheme'] . '://' : '')
                . $rstUrlArray['host']
                . $rstUrlArray['path']
                . '?' . $rstUrlArray['query'];
        return $rstUrl;
    }
}