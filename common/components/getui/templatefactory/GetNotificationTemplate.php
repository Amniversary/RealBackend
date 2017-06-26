<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 14:50
 */

namespace common\components\getui\templatefactory;


use common\components\getui\GeTuiUtil;

class GetNotificationTemplate implements IGetMessageTemplate
{
    function GetTemplate($data,$actionLocKey='',$badge='',$sound='',$payload='',$locKey='',$locArgs='',$launchImage='')
    {
        $template =  new \IGtNotificationTemplate();
        $template->set_appId(GeTuiUtil::APPID);//应用appid
        $template->set_appkey(GeTuiUtil::APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($data['content']);//透传内容
        $template->set_title($data['title']);//通知栏标题
        $template->set_text($data['content']);//通知栏内容
        //$template->set_logo($data['url']);
        $template->set_logoURL($data['logoUrl']); //通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        // iOS推送需要设置的pushInfo字段
        $message = $data['content'];
        $template ->set_pushInfo($actionLocKey,$badge,$message,$sound,$payload,$locKey,$locArgs,$launchImage);
        //$template ->set_pushInfo("test",1,$message,"","","","","");
        return $template;
    }
} 