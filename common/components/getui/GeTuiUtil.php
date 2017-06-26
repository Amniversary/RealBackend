<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 11:02
 */

namespace common\components\getui;
use common\components\getui\GetuiVersions\GetuiVersionUtil;
use common\components\getui\templatefactory\GeTuiTemplateUtil;
use common\components\PhpLock;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 个推辅助类
 * Class GeTuiUtil
 * @package common\components\getui
 */
class GeTuiUtil
{
    const APPID='PFEYhnHFNPA1EXDn0P3pF1';//正式   PFEYhnHFNPA1EXDn0P3pF1   测试  THCZHv68Cw5kQz58tHBkq7
    const APPKEY='VqPJoeW3hm9HzG5IyY12z8';//正式  VqPJoeW3hm9HzG5IyY12z8   测试 JIssov3Xhj8PEhMWlM9TR5
    const MASTERSECRET='hDPC94UvTu9WLMXM9eDab7';//正式    hDPC94UvTu9WLMXM9eDab7   测试   ZNZjKt27QJ6h35aZNHyS93
    const HOST='http://sdk.open.api.igexin.com/apiex.htm';//正式   测试
    static $getuiInstance = null;

    /**
     * 	透传（payload）,数据经SDK传给您的客户端，由您写代码决定如何处理展现给用户
     */
    const TEMPLATETRANSMISSION='TransmissionTemplate';
    /**
     * 点击通知打开网页,在通知栏显示一条含图标、标题等的通知，用户点击可打开您指定的网页
     */
    const TEMPLATELINK='LinkTemplate';
    /**
     *点击通知启动应用,在通知栏显示一条含图标、标题等的通知，用户点击后激活您的应用
     */
    const TEMPLATETNOTIFICATION='NotificationTemplate';
    /**
     *通知栏弹框下载模版,在通知栏显示一条含图标、标题等的通知，用户点击后弹出框，用户可以选择直接下载应用或者取消下载应用。
     */
    const TEMPLATENOTYPOPLOAD='NotyPopLoadTemplate';


    /**
     * 获取个推实例
     * @return null| IGeTui
     */
    protected static function GetInstance($Class)
    {
        if(GeTuiUtil::$getuiInstance === null)
        {
            $pl = new PhpLock('gettuiinstance');
            $pl->lock();
            if(GeTuiUtil::$getuiInstance === null)
            {
                GeTuiUtil::$getuiInstance = new \IGeTui(self::HOST,$Class::APPKEY, $Class::MASTERSECRET);
            }
            $pl->unlock();
        }
        return GeTuiUtil::$getuiInstance;
    }


    /**
     * 将消息上传到个推，并返回消息id
     * @param $content
     * @param $templateType  GeTuiUtil::TEMPLATETRANSMISSION or GeTuiUtil::TEMPLATELINK or GeTuiUtil::TEMPLATETNOTIFICATION or GeTuiUtil::TEMPLATENOTYPOPLOAD
     * @param $contentId 返回消息id
     * @param $error
     * @return bool
     * @throws \Exception
     */
    public static function GetContentId($iMessage,&$contentId,&$error)
    {
        if(!($iMessage instanceof \IGtMessage))
        {
            $error = '不是个推消息对象';
            return false;
        }
        $geTui = self::GetInstance();
        if(!($geTui instanceof \IGeTui))
        {
            $error = '创建个推实例失败';
            return false;
        }
        try
        {
            if(!$geTui->connect())
            {
                return false;
            }
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return false;
        }
        $contentId = $geTui->getContentId($iMessage);
        if(empty($contentId))
        {
            $error = '获取消息id失败';
            return false;
        }
        return true;
    }

    /**
     * 绑定别名
     * @param $cid
     * @param $alias
     */
    public static function BindClientAlias($cid,$alias,&$error)
    {
        if(!isset($cid) || !isset($alias))
        {
            $error = '参数异常';
            return false;
        }
        $handler = self::GetInstance();
        $rep = $handler->bindAlias(self::APPID,$alias,$cid);
        var_dump($rep);
        if(!isset($rep['result']))
        {
            $error = '未知错误，请查看日志';
            $e = !is_string($rep)?var_export($rep,true):$rep;
            \Yii::getLogger()->log($error.':'.$e,Logger::LEVEL_ERROR);
            return false;
        }
        if($rep['result'] !== 'ok')
        {
            $error = '绑定用户异常';
            \Yii::getLogger()->log($error.',msg:'.$rep['result'].',code:'.$rep['error_code'],Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 绑定别名
     * @param array $userList，如[['cid'=>'sfddfs,'alias'=>'ddss'],......]
     * @param $alias
     */
    public static function BatchBindClientAlias($userList,&$error)
    {
        if(!isset($userList) || !is_array($userList))
        {
            $error = '参数异常';
            return false;
        }
        $handler = self::GetInstance();
        $rep = $handler->bindAliasBatch(self::APPID,$userList);
        //var_dump($rep);
        if(!isset($rep['result']))
        {
            $error = '未知错误，请查看日志';
            $e = !is_string($rep)?var_export($rep,true):$rep;
            \Yii::getLogger()->log($error.':'.$e,Logger::LEVEL_ERROR);
            return false;
        }
        if($rep['result'] !== 'ok')
        {
            $error = '绑定用户异常';
            \Yii::getLogger()->log($error.',msg:'.$rep['result'].',code:'.$rep['error_code'],Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 一次最多50个用户
     * 批量发送信息
     * @param $content
     * @param array $user_list ['cid1'=>'alias','cid2'=>'alias']
     * @param $error
     */
    public static function PushListMessage($show_content,$content,$user_list,$app_id,&$error)
    {
        $deploy = GetuiVersionUtil::GetGetuiVersions($app_id,$error);
        if(!isset($user_list) || !is_array($user_list))
        {
            $error = '人员列表异常';
            return false;
        }
        $handler = self::GetInstance($deploy);
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        //$template = IGtNotyPopLoadTemplateDemo();
        $data = ['content'=>$content,'show_content'=>$show_content,'APPID'=>$deploy::APPID,'APPKEY'=>$deploy::APPKEY];
        $template = GeTuiTemplateUtil::GetTemplate(GeTuiUtil::TEMPLATETRANSMISSION,$data,$error);
        if($template === false)
        {
            return false;
        }
        //$template = IGtNotificationTemplateDemo();
        //$template = IGtTransmissionTemplateDemo();

        //个推信息体
        $message = new \IGtListMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型

        $contentId = $handler->getContentId($message);

        $targetList = [];
        foreach($user_list as $cid => $alias)
        {
            $target = new \IGtTarget();
            $target->set_appId($deploy::APPID);
            $target->set_clientId($alias['cid']);
            $target->set_alias($alias['alias']);
            $targetList[] = $target;
        }
        $rst = $handler->pushMessageToList($contentId, $targetList);
        if($rst['result'] !== 'ok')
        {
            $error = $rst['result'];
            return false;
        }
        //var_dump($rst);
        return true;
    }

    /**
     * 给某人推送消息
     * @param $content
     * @param $cid
     * @param $error
     */
    public static function PushSingleMessage($show_content,$content,$cid,$app_id,&$error)
    {
        $deploy = GetuiVersionUtil::GetGetuiVersions($app_id,$error);
        $handler = self::GetInstance($deploy);
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        $data = ['content'=>$content,'show_content'=>$show_content,'APPID'=>$deploy::APPID,'APPKEY'=>$deploy::APPKEY];
        $template = GeTuiTemplateUtil::GetTemplate(GeTuiUtil::TEMPLATETRANSMISSION,$data,$error);
        if($template === false)
        {
            return false;
        }
        //$template = IGtLinkTemplateDemo();
        //$template = IGtNotificationTemplateDemo();
        //$template = IGtTransmissionTemplateDemo();

        //个推信息体
        $message = new \IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        $message->set_data($template);//设置推送消息类型

        //接收方
        $target = new \IGtTarget();
        $target->set_appId($deploy::APPID);
        $target->set_clientId($cid);

        $rep = $handler->pushMessageToSingle($message,$target);
        //var_dump($rep);
        if($rep['result'] !== 'ok')
        {
            $error = $rep['result'];
            return false;
        }
        return true;
    }

    /**
     * 向app推送消息
     * @param $title  //推送标题
     * @param $content  //推送内容
     * @param $error
     */
    public static function PushAppMessage($show_content,$content,$app_id,&$error)
    {
        $deploy = GetuiVersionUtil::GetGetuiVersions($app_id,$error);
        $handler = self::GetInstance( $deploy );
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板

        $data = ['content'=>$content,'show_content'=>$show_content,'APPID'=>$deploy::APPID,'APPKEY'=>$deploy::APPKEY];
        $template = GeTuiTemplateUtil::GetTemplate(GeTuiUtil::TEMPLATETRANSMISSION,$data,$error); //通知栏上方显示
        if($template === false)
        {
            return false;
        }
        //
        //$template = IGtNotificationTemplateDemo();

        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $message->set_appIdList( array( $deploy::APPID ) );
        //$message->set_phoneTypeList(array('ANDROID'));
//	$message->set_provinceList(array('浙江','北京','河南'));
//	$message->set_tagList(array('开心'));

        $rep = $handler->pushMessageToApp($message);
        if($rep['result'] !== 'ok')
        {
            $error = $rep['result'];
            return false;
        }
        return true;
    }

    /**
     * 向所有app推送消息
     * @param $show_content //推送标题
     * @param $content //推送内容
     * @param $error
     */
    public static function PushMessageToAllApp( $show_content,$content,&$error )
    {
        $configFile = require(__DIR__.'/GetuiVersions/VersionsConfig.php');
        foreach ( $configFile as $key=>$val ) {
            self::PushAppMessage($show_content, $content, $key, $error);
            sleep( 3 );//暂停3秒
        }
    }

} 