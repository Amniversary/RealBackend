<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/8
 * Time: 13:14
 */

namespace frontend\business;


use common\models\ChatPersonalGroup;
use common\models\ChatRoomMember;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Console;
use yii\log\Logger;

class ChatPersonGroupUtil
{

    /**
     * 获取直播间管理员
     * @param $user_id
     * @param $error
     */
    public static function GetChatRoomManager($user_id)
    {
        $living = LivingUtil::GetLivingByMasterId($user_id);
        if(!isset($living))
        {
            return [];//未直播过没有管理员
        }
        $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($living->living_id);
        if(!isset($chatRoom))
        {
            return [];
        }

        $admin_list = \Yii::$app->cache->get('manager_admin_list_'.$living->living_id);
        if(!$admin_list)
        {
            ChatGroupUtil::SetAdminUserListCache($living->living_id,$outinfo,$error);
            $admin_list = $outinfo;
        }
        else
        {
            $admin_list = json_decode($admin_list,true);
        }
        return $admin_list;
    }

    /**
     * 返回新模型
     * @param $group_id
     * @param $user_id
     * @return ChatPersonalGroup
     */
    public static function GetNewModel($group_id,$user_id,$is_owner = 3)
    {
        $time = time();
        $model = new ChatRoomMember();
        $model->create_time = 0;
        $model->modify_time = 0;
        $model->group_id = $group_id;
        $model->user_id = $user_id;
        $model->hide_msg = 1;
        $model->heart_count = 0;
        $model->owner = $is_owner;
        $model->status = 0;//加入时，状态设置为未进入
        return $model;
    }

    /**
     * 插入新记录
     * @param $model
     * @param $error
     */
    public static function SaveNewModel(&$model,&$error)
    {
        if(!($model instanceof ChatRoomMember))
        {
            $error = '不是chatroom对象';
            return false;
        }
        if(!$model->save())
        {
            $error = '保存用户群关系失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 加入群
     * @param $group_id
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function AddUserToGroup($group_id,$user_id,$is_owner = 2,&$error,&$user_group=null)
    {
        $user_group = self::GetGroupUser($group_id,$user_id);
        if(isset($user_group))
        {
            return true;//已经加入
        }
        $user_group = self::GetNewModel($group_id,$user_id,$is_owner);
        if(!self::SaveNewModel($user_group,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * @param $group_id
     * @param array $user_ids
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function BatchAddUserToGroup($group_id,$user_ids=[],&$error)
    {
        if(empty($user_ids) || count($user_ids) === 0)
        {
            $error ='成员id不能为空';
            return false;
        }
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            foreach($user_ids as $uid)
            {
                $groupUser = self::GetGroupUser($group_id,$uid);
                if(isset($groupUser))
                {
                    continue;
                }
                $model = self::GetNewModel($group_id,$uid,2);
                if(!self::SaveNewModel($model,$error))
                {
                    throw new Exception($error);
                }
            }
            $trans->commit();
        }
        catch(Exception $ex)
        {
            $error = $ex->getMessage();
            $trans->rollBack();
            return false;
        }
        return true;
    }

    /**
     * 获取单个群组人员记录
     * @param $group_id
     * @param $user_id
     * @return null|static
     */
    public static function GetGroupUser($group_id,$user_id)
    {
        return ChatRoomMember::findOne(['group_id'=>$group_id,'user_id'=>$user_id]);
    }

    /**
     * 删除组人员
     * @param $group_id
     * @param $user_id
     * @param $error
     */
    public static function DelUserFromGroup($group_id,$user_id,&$error)
    {
        $model = self::GetGroupUser($group_id,$user_id);
        if(isset($model))
        {
            if(!$model->delete())
            {
                $error = '用户脱离群组失败';
                \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
        }
        return true;
    }

    /**
     * 获取群组用户列表信息
     * @param $group_id
     */
    public static function GetGroupMembers($group_id)
    {
        $query =(new Query())->select(['account_id','ifnull(cpg.nick_name,ai.nick_name) as nick_name','pic','chat_pic','hide_msg'])->from('my_account_info ai')
            ->innerJoin('my_chat_personal_group cpg','ai.account_id = cpg.user_id and group_id=:gid',[':gid'=>$group_id])
        ->orderBy(['chat_personal_group_id'=>SORT_ASC]);
        $userList = $query->all();
        return $userList;
    }

    /**
     * 获取单个成员信息id
     * @param $group_id
     * @param $user_id
     * @return array|bool
     */
    public static function  GetSingleGroupMember($group_id,$user_id)
    {
        $query =(new Query())->select(['account_id','ifnull(cpg.nick_name,ai.nick_name) as nick_name','pic','chat_pic','hide_msg'])->from('my_account_info ai')
            ->innerJoin('my_chat_personal_group cpg','ai.account_id = cpg.user_id and group_id=:gid and cpg.user_id=:uid',[':gid'=>$group_id,':uid'=>$user_id])
            ->orderBy(['chat_personal_group_id'=>SORT_ASC]);
        $userList = $query->one();
        return $userList;
    }

    /**
     * 设置免打扰
     * @param $group_id
     * @param $group_user_id
     * @param $hide_msg
     * @param $error
     * @return bool
     */
    public static function SetGroupMsgHide($group_id,$group_user_id,$hide_msg,&$error)
    {
        $gUser=self::GetGroupUser($group_id,$group_user_id);
        if(!($gUser instanceof ChatPersonalGroup))
        {
            $error = '群员不存在';
            return false;
        }

        if(!in_array(intval($hide_msg),[1,2]))
        {
            $error = '打扰信息状态错误';
            return false;
        }
        $gUser->hide_msg = $hide_msg;
        if($gUser->save())
        {
            \Yii::getLogger()->log('保存免打扰信息失败:'.var_export($gUser->getErrors(),true),Logger::LEVEL_ERROR);
            $error='保存免打扰信息失败';
            return false;
        }
        return true;
    }

    /**
     * 设置群聊天背景图
     * @param $group_id
     * @param $user_id
     * @param $error
     */
    public static function SetChatPicForGroup($group_id,$user_id,$chat_pic,&$error)
    {
        $gUser=self::GetGroupUser($group_id,$user_id);
        if(!($gUser instanceof ChatPersonalGroup))
        {
            $error = '群员不存在';
            return false;
        }
        if(empty($chat_pic) || strpos($chat_pic,'http://') !== 0)
        {
            $error = '背景图片信息错误';
            return false;
        }
        $gUser->chat_pic = $chat_pic;
        if($gUser->save())
        {
            \Yii::getLogger()->log('保存免打扰信息失败:'.var_export($gUser->getErrors(),true),Logger::LEVEL_ERROR);
            $error='保存免打扰信息失败';
            return false;
        }
        return true;
    }

    /**
     * 修改群成员信息
     * @param $group_id
     * @param $user_id
     * @param $nick_name
     * @param $hide_msg
     * @param $chat_pic
     * @param $error
     * @return bool
     */
    public static function ModifyGroupMemberInfo($group_id,$user_id,$nick_name,$hide_msg,$chat_pic,&$error)
    {
        $gUser=self::GetGroupUser($group_id,$user_id);
        if(!($gUser instanceof ChatPersonalGroup))
        {
            $error = '群员不存在';
            return false;
        }
        if(empty($nick_name) && empty($hide_msg) && empty($chat_pic))
        {
            $error = '修改字段不能同时为空';
            return false;
        }
        if(!empty($chat_pic) && strpos($chat_pic,'http://') === 0)
        {
            $gUser->chat_pic = $chat_pic;
        }
        if(!empty($nick_name))
        {
            $gUser->nick_name = $nick_name;
        }
        if(!empty($hide_msg) && in_array(intval($hide_msg),[1,2]))
        {
            $gUser->hide_msg = $hide_msg;
        }
        if(!$gUser->save())
        {
            \Yii::getLogger()->log('保存免打扰信息失败:'.var_export($gUser->getErrors(),true),Logger::LEVEL_ERROR);
            $error='保存群组成员信息失败';
            return false;
        }
        return true;
    }

    /**
     * 获取直播结束后10分钟任然未退出用户列表
     */
    public static function GetFinishLivingUnQuitMembers()
    {
        $query = new Query();
        $query->from(['mb_living li'])->select(['li.living_id','crm.user_id'])
            ->innerJoin('mb_chat_room cr','cr.living_id = li.living_id')
            ->innerJoin('mb_chat_room_member crm','crm.group_id = cr.room_id')
            ->where(['and','li.status=0','crm.status=1','crm.owner > 1',['between','li.finish_time',date('Y-m-d H:i:s',strtotime('-30 min')),date('Y-m-d H:i:s',strtotime('-1 min'))]]);
        return $query->all();
    }

    /**
     * 获取直播机器人，5天没进过直播的，未看直播的
     * @param int $limit
     */
    public static function GetReBots($living_id,$limit = 30)
    {
/*        $query = '
        select user_id FROM(
select distinct cm.user_id from mb_chat_room_member cm inner join mb_chat_room cr on cm.group_id=cr.room_id and cr.living_id=:lid where (cm.create_time < :ct  and cm.status=0) or (cm.is_rebot=1)  limit :lit1
union
select DISTINCT cm.user_id from mb_chat_room_member cm inner join mb_chat_room cr on cm.group_id=cr.room_id where cm.create_time < :ct2  and cm.status=0 and owner=3 and heart_count=0 and
cm.user_id not in(select distinct cm.user_id from mb_chat_room_member cm inner join mb_chat_room cr on cm.group_id=cr.room_id and cr.living_id=:lid1 where cm.create_time < :ct1  and cm.status=0) limit :lit2
) c limit :lit
        ';
        $query = 'select client_id from mb_client where status=1 order by RAND()';
        $dt = strtotime(date('Y-m-d').' -5 day');
        $rebotList = \Yii::$app->db->createCommand($query,[
            ':lid'=>$living_id,
            ':lid1'=>$living_id,
            ':lit'=>$limit,
            ':lit1'=>$limit,
            ':lit2'=>$limit,
            ':ct'=>$dt,
            ':ct1'=>$dt,
            ':ct2'=>$dt,
        ])->queryAll();*/
        $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($living_id);
        $query = 'select user_id from mb_chat_room_member crm where crm.group_id =:gid and crm.status = 0 and owner >1 order by RAND() limit 30';
        $rebotList = \Yii::$app->db->createCommand($query,[':gid'=>$chatRoom->room_id])->queryAll();
        if(count($rebotList) < 30)
        {
            $query = 'select client_id as user_id from mb_client where status=1 order by RAND() limit 30';
            $rebotList = \Yii::$app->db->createCommand($query)->queryAll();
        }
        return $rebotList;
    }

    /**
     *直播自动加入机器人
     */
    public static function CreatelLivingAddRebots($living_id,$device_type,$living_master_id,$limit = 30)
    {
        //直播间未登录人数
        $rebots = self::GetReBots($living_id,$limit);
        $len = count($rebots);
        $owner = 3;
        for($i =0; $i < $len; $i ++ )
        {
            if($rebots[$i]['user_id'] == $living_master_id)
            {
                continue;//主播自己跳过
            }
            if(!function_exists('pcntl_fork'))
            {
                if(!ChatGroupUtil::EnterRoom($living_id,$rebots[$i]['user_id'],$device_type,$owner,$error,1))
                {
                    \Yii::getLogger()->log('机器人进入直播间异常：'.var_export($error,true).' user_id:'.$rebots[$i]['user_id'].' living_id:'.$living_id,Logger::LEVEL_ERROR);
                }
            }
            else
            {
                $user_id = $rebots[$i]['user_id'];
                \Yii::$app->db->close();//现成里面会从新创建，不关闭不创建，所有必须关闭
                $pid = pcntl_fork();
                if ($pid === 0)
                {
                    try
                    {
                        //子线程从新建立beanstalk链接
                        $host = \Yii::$app->beanstalk->host;
                        $port = \Yii::$app->beanstalk->port;
                        $connectTimeout = \Yii::$app->beanstalk->connectTimeout;
                        \Yii::$app->beanstalk->setConnection(new \Pheanstalk\Connection($host, $port, $connectTimeout));
                        if(!ChatGroupUtil::EnterRoom($living_id,$user_id,$device_type,$owner,$error,1))
                        {
                            fwrite(STDOUT, Console::ansiFormat("---error rebot:--living_id:$living_id ,error:$error "."\n", [Console::FG_GREEN]));
                            \Yii::getLogger()->log('机器人进入直播间异常：'.var_export($error,true).' user_id:'.$rebots[$i]['user_id'].' living_id:'.$living_id,Logger::LEVEL_ERROR);
                            \Yii::getLogger()->flush(true);
                        }
                    }
                    catch(Exception $e2)
                    {
                        $msg = $e2->getMessage();
                        fwrite(STDOUT, Console::ansiFormat("---error rebot:--living_id:$living_id ,error:$msg"."\n", [Console::FG_GREEN]));
                        \Yii::getLogger()->log('sub error:'.$e2->getMessage(),Logger::LEVEL_ERROR);
                    }

                    fwrite(STDOUT, Console::ansiFormat("---rebot:--living_id:$living_id index:$i "."\n", [Console::FG_GREEN]));
                    //必须发送关闭信号，否则web页面访问无法关闭该子线程，造成下面pcntl_waitpid 一直等待挂起
                    //如果不是web链接访问，直接php运行则可以结束该现成，不需要加posix_kill，为了兼容所有情况就加上了这句
                    posix_kill(getmypid(),9);
                    exit($i);
                }
            }
        }
        try
        {
            if(function_exists('pcntl_waitpid'))
            {
                //等待所有现成结束，防止僵尸线程
                while (pcntl_waitpid(0, $status) != -1)
                {
                    $status = pcntl_wexitstatus($status);
                    //\Yii::getLogger()->log("---fork rebot finish:--$status ",Logger::LEVEL_ERROR);
                    fwrite(STDOUT, Console::ansiFormat("---fork rebot finish:--$status "."\n", [Console::FG_GREEN]));
                }
            }
        }
        catch(Exception $e)
        {
            \Yii::getLogger()->log('finish error:'.$e->getMessage(),Logger::LEVEL_ERROR);
        }
    }


    /**
     * @param $living_id
     * @param $device_type
     * @param int $limit
     */
    public static function CreatelLivingAddRebotsMulti($living_id,$device_type,$limit = 30)
    {
        //直播间未登录人数
        $rebots = self::GetReBots($living_id,$limit);
        $len = count($rebots);
        $owner = 3;
        //$len = 5;
        for($i =0; $i < $len; $i ++ )
        {
            if(!function_exists('pcntl_fork'))
            {
                if(!ChatGroupUtil::EnterRoom($living_id,$rebots[$i]['user_id'],$device_type,$owner,$error,1))
                {
                    \Yii::getLogger()->log('机器人进入直播间异常：'.var_export($error,true).' user_id:'.$rebots[$i]['user_id'].' living_id:'.$living_id,Logger::LEVEL_ERROR);
                }
            }
            else
            {
                $user_id = $rebots[$i]['user_id'];
                \Yii::$app->db->close();
                $pid = pcntl_fork();
                if ($pid === 0)
                {
                    try
                    {
                        //子线程从新建立链接
                        $host = \Yii::$app->beanstalk->host;
                        $port = \Yii::$app->beanstalk->port;
                        $connectTimeout = \Yii::$app->beanstalk->connectTimeout;
                        \Yii::$app->beanstalk->setConnection(new \Pheanstalk\Connection($host, $port, $connectTimeout));
                        if(!ChatGroupUtil::EnterRoom($living_id,$user_id,$device_type,$owner,$error,1))
                        {
                            \Yii::getLogger()->log('机器人进入直播间异常：'.var_export($error,true).' user_id:'.$rebots[$i]['user_id'].' living_id:'.$living_id,Logger::LEVEL_ERROR);
                            \Yii::getLogger()->flush(true);
                        }
                    }
                    catch(Exception $e2)
                    {
                        \Yii::getLogger()->log('sub error:'.$e2->getMessage(),Logger::LEVEL_ERROR);
                        \Yii::getLogger()->flush(true);
                    }
                    //\Yii::getLogger()->log("---rebot:--$i ",Logger::LEVEL_ERROR);
                    //fwrite(STDOUT, Console::ansiFormat("---rebot:--$i "."\n", [Console::FG_GREEN]));
                    posix_kill(getmypid(),9);
                    exit($i);
                }
            }
        }
        try
        {
            if(function_exists('pcntl_waitpid'))
            {
                while (pcntl_waitpid(0, $status) != -1)
                {
                    $status = pcntl_wexitstatus($status);
                    \Yii::getLogger()->log("---fork rebot finish:--$status ",Logger::LEVEL_ERROR);
                    //fwrite(STDOUT, Console::ansiFormat("---fork rebot finish:--$status "."\n", [Console::FG_GREEN]));
                }
            }
        }
        catch(Exception $e)
        {
            \Yii::getLogger()->log('finish error:'.$e->getMessage(),Logger::LEVEL_ERROR);
        }
    }

    /***
     * 进入直播获取直播信息
     * @param $living_id
     * @param $user_id
     * @return array
     */
    public static function GetChatRoomMember($living_id,$user_id)
    {
        $query = (new Query())
            ->select(['li.status','crm.room_id','rem.owner'])
            ->from('mb_living li')
            ->innerJoin('mb_chat_room crm','crm.living_id=li.living_id')
            ->innerJoin('mb_chat_room_member rem','rem.group_id=crm.room_id')
            ->where(['and','li.living_id=:ld','rem.user_id=:ud'],[
                ':ld' => $living_id,
                ':ud' => $user_id
            ])
            ->one();
        return $query;
    }

    /**
     * 获取退出房间直播信息
     * @param $living_id
     * @param $user_id
     */
    public static function GetZhiBoInfoForQuitRoom($living_id,$user_id)
    {
        $query = (new Query())
            ->select(['li.status as living_status','li.living_master_id','rem.record_id','li.living_before_id','rem.status','rem.is_rebot','rem.owner','rem.create_time','crm.other_id'])
            ->from('mb_living li')
            ->innerJoin('mb_chat_room crm','crm.living_id=li.living_id')
            ->innerJoin('mb_chat_room_member rem','rem.group_id=crm.room_id')
            ->where(['and','li.living_id=:ld','rem.user_id=:ud'],[
                ':ld' => $living_id,
                ':ud' => $user_id
            ])
            ->one();
        return $query;
    }

    /**
     * 根据直播间和用户id 获取用户身份
     * @param $living_id
     * @param $user_id
     * @return array|bool
     */
    public static function GetLivingOwner($living_id,$user_id)
    {
        $query = (new Query())
            ->select(['ml.living_id','ml.living_master_id','crm.owner'])
            ->from('mb_living ml')
            ->innerJoin('mb_chat_room bcr','bcr.living_id = ml.living_id')
            ->innerJoin('mb_chat_room_member crm','bcr.room_id = crm.group_id')
            ->where(['and','ml.living_id = :ld','crm.user_id = :ud'],[
                ':ld' => $living_id,
                ':ud' => $user_id,
            ])->one();

        return $query;
    }

} 