<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 14:50
 */

namespace common\components\getui\templatefactory;


use common\components\getui\GeTuiUtil;

class GetLinkTemplate implements IGetMessageTemplate
{
    function GetTemplate($data)
    {
        $template =  new \IGtLinkTemplate();
        $template ->set_appId(GeTuiUtil::APPID);//应用appid
        $template ->set_appkey(GeTuiUtil::APPKEY);//应用appkey
        $template ->set_title($data['title']);//通知栏标题
        $template ->set_text($data['content']);//通知栏内容
        $template ->set_logo($data['logo']);//通知栏logo
        $template ->set_isRing(true);//是否响铃
        $template ->set_isVibrate(true);//是否震动
        $template ->set_isClearable(true);//通知栏是否可清除
        $template ->set_url($data['url']);//打开连接地址
        // iOS推送需要设置的pushInfo字段
        //$template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("",2,"","","","","","");
        return $template;
    }
} 