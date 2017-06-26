<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/21
 * Time: 14:18
 */

namespace frontend\business;

use yii\log\Logger;
use common\components\getui\GeTuiUtil;
use common\models\Client;
use yii\data\Pagination;
use common\models\SendMsg;
use yii\db\Query;
use frontend\business\AttentionUtil;
/**
 * 推送信息
 * Class SendMsgUtil
 * @package frontend\business
 */
class SendMsgUtil
{

    /**
     * 向所有用户推送,已不用
     * @param $title 标题
     * @param $content  内容
     * @param $totalCount 推送总记录数
     * @param $page 当前页
     * @param $error 错误提示信息
     * @return bool
     */
    public static function SendMsgToAll( $title,$content,$totalCount,$page,&$error )
    {
        $pages = new Pagination([
                 'totalCount' =>$totalCount,
                 'pageSize'   =>50,
                 'params'=>['page'=>$page]
        ]);

        $ClientInfo =  Client::find()
                        ->select(['getui_id','app_id'])
                        ->andWhere(['not', ['app_id' =>null]])
                        ->offset($pages->offset)
                        ->limit( $pages->limit )
                        ->orderBy(['client_id' =>SORT_DESC])
                        ->all();

        if( !$ClientInfo )
        {
            $error = "全量推送获取用户个推id信息不能为空";
            return false;
        }

        foreach ( $ClientInfo as $key=>$val )
        {
            if( $val['getui_id']  && $val['app_id']  )
            {
                if( !GeTuiUtil::PushSingleMessage( $content,$title,$val['getui_id'],$val['app_id'],$error ) )
                {
                    $error = "定向推送=>向个推id为:$val[getui_id]推送时发生了错误:".$error;
                    \Yii::getLogger()->log( $error,Logger::LEVEL_ERROR );
                }
                sleep( 3 );//暂停3秒
            }
        }

        return true;
    }

    /**
     * 向所有的app用户推送
     * @param $title 标题
     * @param $content 内容
     * @param $error 错误提示信息
     */
    public static function SendMsgToAllApp( $title,$content,&$error )
    {
        if( !GeTuiUtil::PushMessageToAllApp( $title,$content,$error ) )
        {
            \Yii::getLogger()->log( '向所有app推送时发生了错误：'.$error,Logger::LEVEL_ERROR );
        }
    }

    /**
     *  定向推送
     * @param $title 标题
     * @param $content 内容
     * @param $msg_id 推送id
     * @param $target 目标
     * @param $error 错误提示
     * @return bool
     */
    public static function GuidingToSendMsg( $msg_id,$title,$content,$target,&$error )
    {
        try
        {

            if( !$target )
            {
                $error = '定向推送的目标不能为空';
                return false;
            }
            $sql = "SELECT client_id,phone_no,getui_id,app_id  FROM mb_client WHERE app_id IS NOT  NULL and phone_no in( $target ) ORDER BY  client_id DESC ";

            $ClientInfo = \Yii::$app->db->createCommand( $sql
            )->queryAll();

            if( !$ClientInfo )
            {
                $error = '定向推送获取用户个推id信息不能为空';
                return false;
            }

            foreach ( $ClientInfo as $key=>$val )
            {
                if( $val['getui_id'] && $val['getui_id']!='jiashuju' && $val['app_id']  )
                {
                    if( GeTuiUtil::PushSingleMessage( $content,$title,$val['getui_id'],$val['app_id'],$error ) )
                    {
                        $alreadyTarget[] = $val['phone_no'];
                    }
                    else
                    {
                        $error = "定向推送=>向个推id为:$val[getui_id]推送时发生了错误:".$error;
                    }
                }
            }

            if( $alreadyTarget && is_array( $alreadyTarget )  )
            {
                $alreadyTarget = implode(",",$alreadyTarget);
                $update = " UPDATE mb_send_msg SET send_status=:send_status, already_target=:already_target WHERE msg_id=:msg_id";
                $retVal = \Yii::$app->db->createCommand($update,[
                    ":send_status"   =>2,
                    ":already_target"=>$alreadyTarget,
                    ":msg_id"=>$msg_id
                ])->execute();

                if( $retVal<= 0 )
                {
                    $error = "更新推送状态时发生了错误!";
                    return false;
                }
            }
        }
        catch( Exception $ex )
        {
            $error = '定向推送获取用户个推id时发生成错误:'.$ex->getMessage();
            return false;
        }

        return true;
    }

    /**
     * 启动队列
     * @param $msg_id
     * @param $error
     * @return bool
     */
    public static function StartupBeanstalkToSendMsgAll( $msg_id ,&$error )
    {

        $clientCount = \Yii::$app->db->createCommand("SELECT count(*) as  num FROM mb_client WHERE app_id IS NOT  NULL ORDER BY  client_id DESC ")->queryOne();
        $count = $clientCount['num'];
        if( !$count )
        {
            $error = '全量推送时对像人数为空';
            return false;
        }

        $pages = ceil($count/50);
        for($i=1;$i<=$pages;$i++)
        {
            $data = ['msg_id'=>$msg_id,
                     'page'  =>$i,
                     'count' =>$count
            ];

            if( strlen( strval($i) ) >1 )
            {
                $currentPage = substr(strval($i),-1);
                if( $currentPage == 0 || $currentPage=='0') {
                    $currentPage = 10;
                }
            }else{
                $currentPage = $i;
            }

            if( $currentPage<=10 ) {
                $sendmsgBeanstalk = "sendmsg" . $currentPage . "Beanstalk";//第几个队列
                \Yii::$app->$sendmsgBeanstalk->putInTube('sendmsg', $data);
            }

            sleep( 3 );//暂停3秒
        }

        $updateSQL = " UPDATE mb_send_msg SET send_status=2  WHERE msg_id =:msg_id";
        $rst = \Yii::$app->db->createCommand($updateSQL,
            [
                ':msg_id' => $msg_id
            ])->execute();

    }

    /**
     * @param $msg_id
     * @param $error
     * @return bool
     */
    public static function StartupBeanstalkToGuiding( $msg_id,&$error )
    {
        $SendMsg = SendMsg::findOne(['msg_id'=>$msg_id]);
        if( $SendMsg->target )
        {
            $target =  $SendMsg->target;
            if( !$target )
            {
                $error = '定向推送的用户对像数据为空';
                return false;
            }

            $sql = "SELECT count(*) as  num FROM mb_client WHERE app_id IS NOT  NULL and phone_no in( $target ) ORDER BY  client_id DESC ";

            $clientCount = \Yii::$app->db->createCommand( $sql
            )->queryOne();

            if( !$clientCount['num'] )
            {
                $error = '定向推送的用户对像数据为空';
                return false;
            }

            $count = $clientCount['num'];
            $pages = ceil($count/50);
            for($i=1;$i<=$pages;$i++)
            {
                $data = ['msg_id'=>$msg_id,
                    'page'  =>$i,
                    'count' =>$count
                ];

                $currentIndex = substr(strval($i),-1);
                $mod = intval( $currentIndex ) %2;
                if( $mod == 0 )
                {
                    $mod = 1;
                }
                else if( $mod == 1 )
                {
                    $mod = 2;
                }
                $sendmsgBeanstalk = "sendmsg" . $mod . "Beanstalk";//第几个队列
                \Yii::$app->$sendmsgBeanstalk->putInTube('sendmsg', $data);

                sleep( 3 );//暂停3秒
            }

            $updateSQL = " UPDATE mb_send_msg SET send_status=2  WHERE msg_id =:msg_id";
            $rst = \Yii::$app->db->createCommand($updateSQL,
                [
                    ':msg_id' => $msg_id
                ])->execute();
        }
    }

    /**
     * 主播开播时发送推送信息给用户
     * @param $living_master_id
     * @param $nick_name
     * @param $group_id
     * @param $living_id
     * @return bool
     */
    public static function CreateLivingToSendGutui( $living_master_id,$nick_name,$group_id ,$living_id)
    {

        $count = AttentionUtil::GetAttentionFriendsToGetTui( $living_master_id );
        $pageSize = 50;
        $pages = ceil( $count / $pageSize );
        $alreadySend = array();
        for ( $i=1;$i<=$pages;$i++ )
        {
            $data = AttentionUtil::GetAttentionFriendsPageToGetTui( $i,$pageSize,$living_master_id );

            if( $data )
            {
                foreach ( $data as $key=>$val ){

                    if( $val['cid']  &&  $val['app_id'] && !in_array($val['cid'],$alreadySend) ) {

                        if ( $val['living_type'] == 1 || $val['living_type'] == 2 ) {

                            $title = "您关注的" . $nick_name . "主播现在开播了~,她在蜜播等你来撩~,不要让她等得着急~,点击进入~";
                            $content = '5-' . strval($living_master_id) . '-' . $group_id . '-' . strval($living_id) . '-' . $nick_name;
                            $cid = ['cid' => $val['cid'],
                                'alias' => $val['alias']
                            ];
                            GeTuiUtil::PushListMessage( $title, $content, [$cid], $val['app_id'], $error );
                        }
                        else if( $val['living_type'] == 3 || $val['living_type'] == 4 )
                        {
                            $title = "5-您关注的".$nick_name."主播现在开播了~";
                            $content = "您关注的".$nick_name."主播现在开播了~,她在蜜播等你来撩~,不要让她等得着急~";
                            GeTuiUtil::PushSingleMessage( $content,$title,$val['cid'],$val['app_id'],$error );
                        }
                        $alreadySend[$val['cid']] = $val['cid'];
                    }
                }
            }
        }
        return true;
    }

    /**
     * 批量的推送
     * @param $living_master_id
     * @param $nick_name
     * @param $group_id
     * @param $living_id
     */
    public static function BatchToSendGetui( $living_master_id,$nick_name,$group_id ,$living_id )
    {
        $configFile =  \Yii::$app->cache->get("batch_sendmsg_to_getui_app_list");
        if( !$configFile )
        {
            $configFile = require( \Yii::$app->basePath.  '/../common/components/getui/GetuiVersions/VersionsConfig.php');
            \Yii::$app->cache->set("batch_sendmsg_to_getui_app_list",$configFile,60*60*12*7);
        }
        $alreadySend = array();
        foreach ( $configFile as $key=>$val ) {
            $count = AttentionUtil::GetAttentionFriendsToGetTui( $living_master_id,$key );
            if( $count>0 ) {
                $pageSize = 50;
                $pages = ceil( $count / $pageSize );
                for ($i = 1; $i <= $pages; $i++) {
                    $data = AttentionUtil::GetAttentionFriendsPageToGetTui( $i, $pageSize, $living_master_id,$key );
                    if ($data) {

                        $cidList = array();
                        foreach ( $data as $ckey=>$cval ) {
                            if ( $cval['cid'] && $cval['cid']!='jiashuju' && $cval['app_id']  && !in_array($cval['cid'], $alreadySend ) ) {
                                $cidList[] = ['cid' => $cval['cid'],
                                    'alias' => $cval['alias']
                                ];
                                $alreadySend[$cval['cid']] = $cval['cid'];
                            }
                        }
                        //if( $living_type == 1 ){
                            $title = "您关注的" . $nick_name . "主播现在开播了~,她在蜜播等你来撩~,不要让她等得着急~,点击进入~";
                            $content = '5-' . strval($living_master_id) . '-' . $group_id . '-' . strval($living_id) . '-' . $nick_name;
                        //}else if( $living_type == 2 ){
                         //   $title = "您关注的".$nick_name."主播现在开播了~";
                        //    $content = "5-您关注的".$nick_name."主播现在开播了~,她在蜜播等你来撩~,不要让她等得着急~";
                        //}
                        if( $cidList ){

                           GeTuiUtil::PushListMessage( $title, $content, $cidList , $key, $error );
                        }
                    }
                }
            }
        }
    }

}