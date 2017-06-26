<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 14:49
 */

namespace common\components\getui\templatefactory;


use common\components\getui\GeTuiUtil;

class GetTransmissionTemplate implements IGetMessageTemplate
{
    /**
     * @param array $data：content字段穿透消息内容
     * @return \IGtBaseTemplate|\IGtTransmissionTemplate
     */
    function GetTemplate($data)
    {
        $template =  new \IGtTransmissionTemplate();
        //应用appid
        //$template->set_appId(GeTuiUtil::APPID);
        $template->set_appId($data['APPID']);
        //应用appkey
        //$template->set_appkey(GeTuiUtil::APPKEY);
        $template->set_appkey($data['APPKEY']);
        //透传消息类型
        $template->set_transmissionType(2);
        //透传内容
        $template->set_transmissionContent($data['content']);
        $template->set_pushInfo("","",$data['show_content'],"",$data['content'],"","","");

        /*IOS 推送需要对该字段进行设置具体参数详见2.4*/
        return $template;
    }
} 