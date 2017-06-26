<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 14:50
 */

namespace common\components\getui\templatefactory;


use common\components\getui\GeTuiUtil;

class GetNotyPopLoadTemplate implements IGetMessageTemplate
{
    function GetTemplate($data)
    {
        $template =  new \IGtNotyPopLoadTemplate();

        $template ->set_appId(GeTuiUtil::APPID);//应用appid
        $template ->set_appkey(GeTuiUtil::APPKEY);//应用appkey
        //通知栏
        $template ->set_notyTitle($data['title']);//通知栏标题
        $template ->set_notyContent($data['content']);//通知栏内容
        $template ->set_notyIcon($data['logo']);//通知栏logo
        $template ->set_isBelled(true);//是否响铃
        $template ->set_isVibrationed(true);//是否震动
        $template ->set_isCleared(true);//通知栏是否可清除
        //弹框
        $template ->set_popTitle($data['pop_title']);//弹框标题
        $template ->set_popContent($data['pop_content']);//弹框内容
        $template ->set_popImage($data['pop_pic']);//弹框图片
        $template ->set_popButton1("下载");//左键
        $template ->set_popButton2("取消");//右键
        //下载
        $template ->set_loadIcon($data['down_pic']);//弹框图片
        $template ->set_loadTitle($data['down_title']);
        $template ->set_loadUrl($data['down_url']);
        $template ->set_isAutoInstall(false);
        $template ->set_isActived(true);

        return $template;
    }
} 