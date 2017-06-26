<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/8
 * Time: 15:34
 */

namespace frontend\business;


use common\components\CharToPingYinManager;
use common\components\tenxunlivingsdk\TimRestApi;
use common\models\AccountInfo;
use common\models\ChatGroup;
use common\models\ChatRoom;
use common\models\LivingAdmin;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class ChatGroupUtil
{

    /**
     * 退出直播
     * @param $living_id
     * @param $user_id
     * @param $device_type
     * @param $error
     * @param bool $is_error_out
     * @param bool $is_tx_im
     * @return bool
     * @throws \yii\db\Exception
     *
     */
    public static function QuitRoom($living_id,$user_id,$device_type,&$error,$is_error_out = false,$is_tx_im = false)
    {

        //修改进入时间、更新时间
        //beantalkd任务处理经验
        $zhiboInfo = ChatPersonGroupUtil::GetZhiBoInfoForQuitRoom($living_id,$user_id);
        if($zhiboInfo === false || empty($zhiboInfo))
        {
            $error = '不是该直播间成员';
            \Yii::getLogger()->log($error.' living_id:'.$living_id.' user_id:'.$user_id,Logger::LEVEL_ERROR);
            return false;
        }
       
        if($zhiboInfo['owner'] == 1)
        {
            $error = '主播不允许该操作，请结束直播';
            return;
        }

        if($zhiboInfo['status'] == 0)
        {
            $error = '已经退出';
            return false;
        }
        //机器人不处理经验  异常退出不做经验处理

        if($zhiboInfo['is_rebot'] == 0 && $is_error_out === false)
        {
            //不是主播计算观看时间
            $disTime = time() - $zhiboInfo['create_time'];
            if($disTime < 0)
            {
                $disTime = 0;
            }
            $disTime = intval($disTime / 60);//转为分钟
          
            if($disTime > 0)
            {
                $data=[
                    'living_id'=>$living_id,
                    'user_id'=>$user_id,
                    'role'=>$zhiboInfo['owner'],//身份，区分主播和其他成员，他们经验转化率不同
                    'heart_count'=>$disTime,
                    'device_type'=>$device_type,
                    'living_no'=>$zhiboInfo['living_before_id'],
                    'op_type'=>'2'
                ];
                if(!JobUtil::AddExpJob('living_experience',$data,$error))
                {
                    \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
                }
            }
        }
        //\Yii::getLogger()->log('quit room ffffff ok,living_id:'.$living_id.' user_id:'.$user_id,Logger::LEVEL_ERROR);

/*
        //暂时去除人数不减
        $key = 'enter_room_no_sub_person_'.$living_id;
        $rst = \Yii::$app->cache->get($key);
        if($rst !== false && !$is_error_out)
        {
            return true;//不执行退出
        }*/
        //$sql = 'update mb_chat_room_member set create_time=0,modify_time=0,status=0 where record_id=:rid and status  = 1';
        $sql = 'delete from mb_chat_room_member where record_id=:rid and status  = 1';
        //放置记录一直增加，不可行，因为管理员信息在这个里面，删除后管理员信息无法保存
        //$sql = 'delete from mb_chat_room_member where record_id=:rid and status  = 1';
        \Yii::$app->db->createCommand($sql,[
            ':rid'=>$zhiboInfo['record_id']
        ])->execute();
//        if($rst <= 0)
//        {
//            $error = '用户退出直播间状态异常';
//            return false;
//        }
        //\Yii::getLogger()->log('quit room tttttt ok,living_id:'.$living_id.' user_id:'.$user_id,Logger::LEVEL_ERROR);
        //已经结束的不发送这个消息
        if($is_tx_im)
        {
            $group_id = $zhiboInfo['other_id'];
            if(!TimRestApi::group_delete_group_member($group_id,$user_id,'1',$error))
            {
                \Yii::getLogger()->log('退出群失败：'.$error,Logger::LEVEL_ERROR);
            }
        }
        if($zhiboInfo['living_status'] == 2)
        {
            //加入异步任务处理
            $data=[
                'living_id'=>$living_id,
                'user_id'=>$user_id,
                'op_type'=>'quit',
                'source_status'=>'0',
                'living_master_id'=>$zhiboInfo['living_master_id']
            ];

            $poise = $living_id % 5;
            switch($poise)
            {
                case 0: $jobServer = 'peopleBeanstalk'; break;
                case 1: $jobServer = 'people2Beanstalk'; break;
                case 2: $jobServer = 'people3Beanstalk'; break;
                case 3: $jobServer = 'people4Beanstalk'; break;
                case 4: $jobServer = 'people5Beanstalk'; break;
            }
            if(!JobUtil::AddCustomJob($jobServer,'living_enter_quit',$data,$error))
            {
                \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);

            }
            /*if(!JobUtil::AddPeopleJob('living_enter_quit',$data,$error))
            {
                \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
            }*/
        }
        return true;
    }

    /**
     * 进入直播间
     * @param $living_id
     */
    public static function QiNiuEnterRoom($living_id,$user_id,$device_type,&$owner,&$error,$is_rebot=0,$is_police = 0,$extra = false)
    {
        try
        {
            $livingInfo = LivingUtil::GetLivingById($living_id);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return false;
        }

        if(!isset($livingInfo))
        {
            $error = '直播不存在';
            return false;
        }

        if($livingInfo->living_master_id == $user_id)
        {
            $error = '主播不能进行该操作';
            return false;
        }
        $attention = AttentionUtil::GetFriendOne($user_id,$livingInfo->living_master_id);
        $error =[
            'living_master_id'=>$livingInfo->living_master_id,
           'attention'=> isset($attention) ? '1': '0']
        ;
        if($livingInfo->status !== 2)
        {
            $error = ['errno'=>'1105','errmsg'=>'直播已停止'];
            return false;
        }
        //不是群成员，拉入群
        //修改进入时间、更新时间
        //beantalkd任务
        $groupInfo = self::GetChatGroupByLivingId($living_id);
        if(!isset($groupInfo))
        {
            $error = '数据异常，直播间不存在';
            \Yii::getLogger()->log($error.' living_id:'.$living_id,Logger::LEVEL_ERROR);
            return false;
        }
        $groupUser = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        //\Yii::getLogger()->log('进入直播间:'.var_export($groupUser,true),Logger::LEVEL_ERROR);
        if(!isset($groupUser))
        {
            //加入直播间  owner 2 管理员  3 普通人员
            if(!ChatPersonGroupUtil::AddUserToGroup($groupInfo->room_id,$user_id,3,$error,$groupUser))
            {
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
                return false;
            }
            //$groupUser = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        }
        $owner = $groupUser->owner;
        //$heartCount = 0;
        $source_status = $groupUser->status;
        //if($groupUser->status === 1)//异常离开再进入
        //{
        /*            $time1 = (!empty($groupUser->modify_time)?$groupUser->modify_time:$groupUser->create_time);
                    $cur_time = time();
                    $dis_time = $cur_time - $time1;
                    $enableDisTime = SystemParamsUtil::GetSystemParam('unable_heart_dis_time',true,'value1');
                    $enableDisTime = intval($enableDisTime);//单位秒
                    if(empty($enableDisTime))
                    {
                        $error = '获取系统心跳无效参数异常';
                        return false;
                    }*/
        //if($dis_time <= $enableDisTime)
        //{
        //$heartCount =1;
        //加入异步任务处理，获取经验
        /*                $data=[
                            'living_id'=>$living_id,
                            'user_id'=>$user_id,
                            'role'=>$groupUser->owner,//身份，区分主播和其他成员，他们经验转化率不同
                            'starttime'=>$groupUser->create_time,
                            'endtime'=>$groupUser->modify_time,
                            'device_type'=>$device_type,
                            'op_type'=>'2'
                        ];
                        if(!JobUtil::AddJob('living_experience',$data,$error))
                        {
                            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
                        }*/
        //}
        //}

        $sql = 'update mb_chat_room_member set create_time=:ct, modify_time=:mt,status=1,is_rebot=:rbt,is_police=:plc where group_id=:gid and 	user_id=:uid and modify_time < :t1';
//不再做心跳处理，如果异常退出则不再给经验，清空时间重新计算
        /*        if($heartCount > 0)
                {
                    $sql = 'update mb_chat_room_member set create_time=:ct, modify_time=:mt,status=1,is_rebot=:rbt,heart_count=heart_count + 1 where record_id=:lid and modify_time < :t1';
                }*/
        $time = time();
        /*        $groupUser->create_time=$time;
                $groupUser->modify_time=$time;
                $groupUser->status = 1;*/
        $rst = \Yii::$app->db->createCommand($sql,[':ct'=>$time,
            ':mt'=>$time,
            ':gid'=>$groupUser->group_id,
            ':uid'=>$groupUser->user_id,
            ':t1'=>$time,
            ':rbt'=>$is_rebot,
            ':plc'=>$is_police
        ])->execute();
        if($rst <= 0)
        {
            \Yii::getLogger()->log('enter room error group_id='.$groupUser->group_id.' user_id:'.$groupUser->user_id, Logger::LEVEL_ERROR);
            $error = '进入直播室失败，状态不正确';
            \Yii::getLogger()->log('sql:'.\Yii::$app->db->createCommand($sql,[':ct'=>$time,':mt'=>$time,':gid'=>$groupUser->group_id, ':uid'=>$groupUser->user_id,':t1'=>$time,':rbt'=>$is_rebot,':plc'=>$is_police])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        else
        {
            //\Yii::getLogger()->log('enter room ok group_id='.$groupUser->group_id.' user_id:'.$groupUser->user_id, Logger::LEVEL_ERROR);
        }
        if($is_rebot == '1')//机器人进入房间，人数加1
        {
            //机器人，设置成0
            $source_status = 0;
        }

        //加入异步任务处理
        $data=[
            'living_id'=>$living_id,
            'user_id'=>$user_id,
            'living_master_id'=>$livingInfo->living_master_id,
            'op_type'=>'enter',
            'source_status'=>$source_status
        ];

        if ($extra) {
            $data['extra'] = $extra;
        }
        $poise = $living_id % 5;
        switch($poise)
        {
            case 0: $jobServer = 'peopleBeanstalk'; break;
            case 1: $jobServer = 'people2Beanstalk'; break;
            case 2: $jobServer = 'people3Beanstalk'; break;
            case 3: $jobServer = 'people4Beanstalk'; break;
            case 4: $jobServer = 'people5Beanstalk'; break;
        }
        if(!JobUtil::AddCustomJob($jobServer,'living_enter_quit',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }
        /*if(!JobUtil::AddPeopleJob('living_enter_quit',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }*/
        return true;
    }

    /**
     * 进入直播间
     * @param $living_id
     */
    public static function EnterRoom($living_id,$user_id,$device_type,&$owner,&$error,$is_rebot=0,$is_police = 0)
    {
        try
        {
            $livingInfo = LivingUtil::GetLivingById($living_id);
        }
        catch(Exception $e)
        {
            $error = $e->getMessage();
            return false;
        }

        if(!isset($livingInfo))
        {
            $error = '直播不存在';
            return false;
        }

        if($livingInfo->living_master_id == $user_id)
        {
            $error = '主播不能进行该操作';
            return false;
        }
        $attention = AttentionUtil::GetFriendOne($user_id,$livingInfo->living_master_id);
        $error = isset($attention) ? '1': '0';
        if($livingInfo->status !== 2)
        {
            $error = ['errno'=>'1105','errmsg'=>'直播已停止'];
            return false;
        }
        //不是群成员，拉入群
        //修改进入时间、更新时间
        //beantalkd任务
        $groupInfo = self::GetChatGroupByLivingId($living_id);
        if(!isset($groupInfo))
        {
            $error = '数据异常，直播间不存在';
            \Yii::getLogger()->log($error.' living_id:'.$living_id,Logger::LEVEL_ERROR);
            return false;
        }
        $groupUser = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        //\Yii::getLogger()->log('进入直播间:'.var_export($groupUser,true),Logger::LEVEL_ERROR);
        if(!isset($groupUser))
        {
            //加入直播间  owner 2 管理员  3 普通人员
            if(!ChatPersonGroupUtil::AddUserToGroup($groupInfo->room_id,$user_id,3,$error,$groupUser))
            {
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
                return false;
            }
            //$groupUser = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        }
        $owner = $groupUser->owner;
        //$heartCount = 0;
        $source_status = $groupUser->status;
        //if($groupUser->status === 1)//异常离开再进入
        //{
/*            $time1 = (!empty($groupUser->modify_time)?$groupUser->modify_time:$groupUser->create_time);
            $cur_time = time();
            $dis_time = $cur_time - $time1;
            $enableDisTime = SystemParamsUtil::GetSystemParam('unable_heart_dis_time',true,'value1');
            $enableDisTime = intval($enableDisTime);//单位秒
            if(empty($enableDisTime))
            {
                $error = '获取系统心跳无效参数异常';
                return false;
            }*/
            //if($dis_time <= $enableDisTime)
            //{
                //$heartCount =1;
                //加入异步任务处理，获取经验
/*                $data=[
                    'living_id'=>$living_id,
                    'user_id'=>$user_id,
                    'role'=>$groupUser->owner,//身份，区分主播和其他成员，他们经验转化率不同
                    'starttime'=>$groupUser->create_time,
                    'endtime'=>$groupUser->modify_time,
                    'device_type'=>$device_type,
                    'op_type'=>'2'
                ];
                if(!JobUtil::AddJob('living_experience',$data,$error))
                {
                    \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
                }*/
            //}
        //}

        $sql = 'update mb_chat_room_member set create_time=:ct, modify_time=:mt,status=1,is_rebot=:rbt,is_police=:plc where group_id=:gid and 	user_id=:uid and modify_time < :t1';
//不再做心跳处理，如果异常退出则不再给经验，清空时间重新计算
/*        if($heartCount > 0)
        {
            $sql = 'update mb_chat_room_member set create_time=:ct, modify_time=:mt,status=1,is_rebot=:rbt,heart_count=heart_count + 1 where record_id=:lid and modify_time < :t1';
        }*/
        $time = time();
/*        $groupUser->create_time=$time;
        $groupUser->modify_time=$time;
        $groupUser->status = 1;*/
        $rst = \Yii::$app->db->createCommand($sql,[':ct'=>$time,
            ':mt'=>$time,
            ':gid'=>$groupUser->group_id,
            ':uid'=>$groupUser->user_id,
            ':t1'=>$time,
            ':rbt'=>$is_rebot,
            ':plc'=>$is_police
        ])->execute();
        if($rst <= 0)
        {
            \Yii::getLogger()->log('enter room error group_id='.$groupUser->group_id.' user_id:'.$groupUser->user_id, Logger::LEVEL_ERROR);
            $error = '进入直播室失败，状态不正确';
            \Yii::getLogger()->log('sql:'.\Yii::$app->db->createCommand($sql,[':ct'=>$time,':mt'=>$time,':gid'=>$groupUser->group_id, ':uid'=>$groupUser->user_id,':t1'=>$time,':rbt'=>$is_rebot])->rawSql,Logger::LEVEL_ERROR);
            return false;
        }
        else
        {
            //\Yii::getLogger()->log('enter room ok group_id='.$groupUser->group_id.' user_id:'.$groupUser->user_id, Logger::LEVEL_ERROR);
        }
        if($is_rebot == '1')//机器人进入房间，人数加1
        {
            //机器人，设置成0
            $source_status = 0;
        }

        //加入异步任务处理
        $data=[
            'living_id'=>$living_id,
            'user_id'=>$user_id,
            'living_master_id'=>$livingInfo->living_master_id,
            'op_type'=>'enter',
            'source_status'=>$source_status
        ];
        if(!JobUtil::AddPeopleJob('living_enter_quit',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }

        return true;
    }
    /**
     * 获取用户所有的群
     * @param $user_id
     */
    public static function GetUserAllChatGroup($user_id)
    {
        $query = new Query();
        $queryUnion = new Query();
        $queryUnion->from('my_chat_group cg')->select(['chat_group_id','group_name','group_master_id','icon','other_id','group_type','concat(\'1\',\'\') as relate','describtion','cpg.hide_msg'])
            ->innerJoin('my_chat_personal_group cpg','cg.chat_group_id = cpg.group_id and cpg.is_owner = 2 and user_id=:uid',[':uid'=>$user_id])
            ->innerJoin('my_account_info ai','cpg.user_id=ai.account_id')
            ->where(['and','cg.status=1']);
        $query->from('my_chat_group cg')->select(['chat_group_id','group_name','group_master_id','icon','other_id','group_type','concat(\'2\',\'\') as relate','describtion','cpg.hide_msg'])
            ->innerJoin('my_chat_personal_group cpg','cg.chat_group_id = cpg.group_id and cpg.is_owner = 1 and user_id=:uid',[':uid'=>$user_id])
            ->innerJoin('my_account_info ai','cpg.user_id=ai.account_id')
            ->where(['cg.status'=>'1'])
            ->union($queryUnion,true);
        $groupList = $query->all();
        $out = [];
        foreach($groupList as $one)//加上全拼和简拼
        {
            $one['full_code'] = CharToPingYinManager::getAllPY($one['group_name']);
            $one['simple_code'] = CharToPingYinManager::getFirstPY($one['group_name']);
            $out[] = $one;
        }
        return $out;
    }

    /**
     * 获取单个群信息
     * @param $group_id
     * @param $user_id
     */
    public static function GetSingleGroupInfo($group_id,$user_id)
    {
        $query = new Query();
        $queryUnion = new Query();
        $queryUnion->from('my_chat_group cg')->select(['chat_group_id','group_name','group_master_id','icon','other_id','group_type','concat(\'1\',\'\') as relate','describtion','cpg.hide_msg'])
            ->innerJoin('my_chat_personal_group cpg','cg.chat_group_id = cpg.group_id and cpg.is_owner = 2 and user_id=:uid',[':uid'=>$user_id])
            ->innerJoin('my_account_info ai','cpg.user_id=ai.account_id')
            ->where(['and','cg.status=1',['cg.chat_group_id'=>$group_id]]);
        $query->from('my_chat_group cg')->select(['chat_group_id','group_name','group_master_id','icon','other_id','group_type','concat(\'2\',\'\') as relate','describtion','cpg.hide_msg'])
            ->innerJoin('my_chat_personal_group cpg','cg.chat_group_id = cpg.group_id and cpg.is_owner = 1 and user_id=:uid',[':uid'=>$user_id])
            ->innerJoin('my_account_info ai','cpg.user_id=ai.account_id')
            ->where(['cg.status'=>'1','cg.chat_group_id'=>$group_id])
            ->union($queryUnion,true);
        $groupList = $query->one();
        if(!empty($groupList))
        {
            $groupList['full_code'] = CharToPingYinManager::getAllPY($groupList['nick_name']);
            $groupList['simple_code'] = CharToPingYinManager::getFirstPY($groupList['nick_name']);
        }
        else
        {
            $groupList = null;
        }
        return $groupList;
    }

    /**
     * 根据愿望id获取群
     * @param $wish_id
     * @return null|static
     */
    public static function GetGroupByWishId($wish_id)
    {
        return ChatGroup::findOne(['group_type'=>'1','wish_id'=>$wish_id]);
    }

    /**
     * 获取群的人员信息
     * @param $group_id
     */
    public static function GetGroupUsers($group_id)
    {
        $queryList = AccountInfo::find()->select(['account_id','pic','nick_name'])->from('my_account_info ai')
            ->innerJoin('my_chat_personal_group cpg','ai.account_id=cpg.user_id and cpg.group_id=:gid',[':gid'=>$group_id])
            ->all();
        $rst = [];
        foreach($queryList as $one)
        {
            $rst[] = $one->getAttributes(['account_id','pic','nick_name']);
        }
        return $rst;
    }

    /**
     * 创建群
     * @param $groupname
     * @param $desc
     * @param $owner
     * @param $pic
     * @param $other_id
     * @param int $group_type
     * @param int $wish_id
     * @param $error
     * @param array $user_ids
     * @return bool
     */
    public static function CreateChatGroup($groupname,$desc,$owner,$pic,$other_id,$chat_pic,$group_type=1,$wish_id=0,&$error,$user_ids=[])
    {
        if(empty($wish_id))
        {
            $wish_id = 0;
        }
        $model = new ChatGroup();
        $model->create_time = date('Y-m-d H:i:s');
        $model->group_name = $groupname;
        $model->other_id = $other_id;
        $model->group_master_id = $owner;
        $model->group_member_count = 300;
        $model->icon = (empty($pic)?'http://image.matewish.cn/system/icon.png':$pic);
        $model->status = 1;
        $model->public = 1;
        $model->approval = 2;
        $model->describtion = $desc;
        $model->group_type = $group_type;
        $model->wish_id = $wish_id;
        $model->chat_pic = $chat_pic;
        array_unshift($user_ids,$owner);
        $is_owner = 1;
        $trans = \Yii::$app->db->beginTransaction();
        try
        {
            if(!$model->save())
            {
                $error = '保存群数据失败';
                \Yii::getLogger()->log('保存群数据异常：'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                throw new Exception($error);
            }
            foreach($user_ids as $uid)
            {
                //第一个是群主
                $gUser=ChatPersonGroupUtil::GetNewModel($model->chat_group_id,$uid,$is_owner);
                if(!$gUser->save())
                {
                    $error = '保存群成员数据异常';
                    \Yii::getLogger()->log('保存群成员数据异常：'.var_export($gUser->getErrors(),true),Logger::LEVEL_ERROR);
                    throw new Exception($error);
                }
                if($is_owner === 1)
                {
                    $is_owner ++;
                }
            }
            $trans->commit();
        }
        catch(Exception $e)
        {
            $error=$e->getMessage();
            $trans->rollBack();
            return false;
        }
        $error = $model->chat_group_id;
        return true;
    }

    /**
     * 修改群昵称
     * @param $group_id
     * @param $group_name
     * @param $error
     */
    public static function ModifyGroupName($group,$group_name,$desc,$icon,$chat_pic,&$error)
    {
        if(!($group instanceof ChatGroup))
        {
            $error = '不是群对象';
            return false;
        }
        if(empty($group_name) && empty($desc) && empty($icon) && empty($chat_pic))
        {
            $error= '修改参数不能同时为空';
            return false;
        }
        if(!empty($group_name))
        {
            $group->group_name = $group_name;
        }
        if(!empty($desc))
        {
            $group->describtion = $desc;
        }
        if(!empty($icon) && strpos($icon,'http://') === 0)
        {
            $group->icon = $icon;
        }
        if(!empty($chat_pic) && strpos($chat_pic,'http://') === 0)
        {
            $group->chat_pic = $chat_pic;
        }
        if(!$group->save())
        {
            $error = '保存群昵称异常';
            \Yii::getLogger()->log($error.' '.var_export($group->getErrors(),true).var_export($group->attributes,true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 获取聊天群id
     * @param $group_id
     * @return null|static
     */
    public static function GetChatGroupById($group_id)
    {
        return ChatGroup::findOne(['chat_group_id'=>$group_id]);
    }

    /**
     * 用第三方群id查找群
     * @param $other_id
     */
    public static function GetChatGroupByOtherId($other_id)
    {
        return ChatGroup::findOne(['other_id'=>$other_id]);
    }

    /**
     * 根据群id删除群
     * @param $group_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function DelGroupById($group_id, &$error)
    {
        $model = self::GetChatGroupById($group_id);
        if(isset($model))
        {
            //$groupUserList = ChatPersonGroupUtil::GetGroupMembers($group_id);
            $sqlDeleteMember = 'delete from my_chat_personal_group where group_id=:gid';
            $trans = \Yii::$app->db->beginTransaction();
            try
            {
                if(!$model->delete())
                {
                    $error = '删除群失败';
                    \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                    throw new Exception($error);
                }
                \Yii::$app->db->createCommand($sqlDeleteMember,[':gid'=>$group_id])->execute();

                $trans->commit();
            }
            catch(Exception $e)
            {
                $trans->rollBack();
                $error = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /**
     * 根据第三方群id删除群
     * @param $other_id
     * @param $error
     * @return bool
     */
    public static function DelGroupByOtherId($other_id, &$error)
    {
        $model = self::GetChatGroupByOtherId($other_id);
        if(isset($model))
        {
            $groupUserList = ChatPersonGroupUtil::GetGroupMembers($model->chat_group_id);
            $trans = \Yii::$app->db->beginTransaction();
            try
            {
                if(!$model->delete())
                {
                    $error = '删除群失败';
                    \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                    throw new Exception($error);
                }
                foreach($groupUserList as $gUser)
                {
                    if(!$gUser->delete())
                    {
                        \Yii::getLogger()->log('删除群用户关系失败：'.var_export($gUser->getErrors(),true),Logger::LEVEL_ERROR);
                        throw new Exception('删除群用户关系失败');
                    }
                }

                $trans->commit();
            }
            catch(Exception $e)
            {
                $trans->rollBack();
                $error = $e->getMessage();
                return false;
            }
        }
        return true;
    }

    /**
     * 获取群组聊天背景图片列表
     */
    public static function GetChatPicList()
    {
        return [
            'http://image.matewish.cn/app-banner/background0.png',
            'http://image.matewish.cn/app-banner/background1.jpg',
            'http://image.matewish.cn/app-banner/background2.jpg',
            'http://image.matewish.cn/app-banner/background3.jpg',
            'http://image.matewish.cn/app-banner/background4.jpg',
            'http://image.matewish.cn/app-banner/background5.jpg'
        ];
    }

    /**
     * 根据id获取群
     * @param $living_id
     */
    public static function GetChatGroupByLivingId($living_id)
    {
        return ChatRoom::findOne(['living_id'=>$living_id]);
    }

    /**
     * 禁言操作
     * @param $user_id
     * @param $member_id
     * @param $op_type
     * @param $error
     */
    public static function ShutupForGrooupMember($living_id, $user_id,$member_id,$op_type,&$error,$is_police=0)
    {
        $groupInfo = self::GetChatGroupByLivingId($living_id);
        if(!isset($groupInfo))
        {
            $error = '群组信息不存在';
            //\Yii::getLogger()->log($error.' group_id:'.$groupInfo->room_id,Logger::LEVEL_ERROR);
            return false;
        }
        $op_member = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        if(!isset($op_member))
        {
            $error = '操作人不是群成员';
            return false;
        }
        $member = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$member_id);
        if(!isset($member))
        {
            $error = '不是该群成员';
            if(!TimRestApi::group_forbid_send_msg($groupInfo->other_id,strval($member_id),3600*5,$error)) {
                $error = '禁言失败';
                return false;
            }
            return true;
        }
        if($op_member->owner > 2 && $is_police < 1)
        {
            $error = '操作人不是管理人员';
            return false;
        }
        if($op_type == '1') //禁言
        {
            if($member->hide_msg === 0)
            {
                $error = '已经被禁言';
                return false;
            }
            if($member->owner < 3)
            {
                $error = '不能对管理员进行禁言';
                return false;
            }
            $member->hide_msg = 0;
            if(!$member->save())
            {
                $error = '禁言保存失败';
                \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
            if(!TimRestApi::group_forbid_send_msg($groupInfo->other_id,strval($member_id),3600*5,$error))
            {
                $member->hide_msg = 1;
                if(!$member->save())
                {
                    $error = '禁言异常，回滚失败';
                    \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                }
                return false;
            }
        }
        else //取消禁言
        {
            if($member->hide_msg === 1)
            {
                $error = '没有被禁言，无需解禁';
                return false;
            }
            $member->hide_msg = 1;
            if(!$member->save())
            {
                $error = '解禁保存失败';
                \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }
            if(!TimRestApi::group_forbid_send_msg($groupInfo->other_id,strval($member_id),0,$error))
            {
                $member->hide_msg = 0;
                if(!$member->save())
                {
                    $error = '解禁保存失败';
                    \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                }
                return false;
            }
        }
        return true;
    }

    /**
     * 通过直播ID获取直播间管理员列表
     * @param $living_id
     * @return array|bool
     */
    public static function GetAdminUserList($living_id)
    {
        $query = (new Query())
            ->select(['ct.sex','ct.client_id as user_id','ct.pic','ct.nick_name','ct.sign_name','living_id'])
            ->from('mb_living_admin la')
            ->innerJoin('mb_client ct','la.admin_id=ct.client_id')
            ->where('la.living_id=:lid',[':lid' => $living_id])
            ->all();
        return $query;
    }

    /**
     * 设置管理员缓存
     * @param $living_id
     * @param $error
     * @return bool
     */
    public static function SetAdminUserListCache($living_id,&$outinfo,&$error)
    {
        $admin_list = self::GetAdminUserList($living_id);
        $outinfo = $admin_list;
        $admin_list = json_encode($admin_list);
        $res_cache = \Yii::$app->cache->set('manager_admin_list_'.$living_id,$admin_list);
        if(!$res_cache)
        {
            $error = '管理员缓存写入失败';
            return false;
        }

        return true;
    }

    /**
     * @param $living_id
     * @param $user_id
     * @param $member_id
     * @param $op_type
     * @param $error
     * @return bool
     */
    public static function SetGroupManager($living_id, $user_id,$member_id,$op_type,&$error)
    {
        if($user_id == $member_id)
        {
            $error = '不能对自己进行该操作';
            return false;
        }

        $groupInfo = self::GetChatGroupByLivingId($living_id);
        if(!isset($groupInfo))
        {
            $error = '群组信息不存在';
            \Yii::getLogger()->log($error.' group_id:'.$groupInfo->room_id,Logger::LEVEL_ERROR);
            return false;
        }


        $op_member = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$user_id);
        if(!isset($op_member))
        {
            $error = '操作人不是群成员';
            return false;
        }
        $member = ChatPersonGroupUtil::GetGroupUser($groupInfo->room_id,$member_id);
        if(!isset($member))
        {
            $error = '不是该群成员';
            return false;
        }
        if($op_member->owner !== 1)
        {
            $error = '操作人不是群主';
            \Yii::getLogger()->log('$groupInfo=:'.var_export($groupInfo,true),Logger::LEVEL_ERROR);
            return false;
        }
        $admin_list = LivingAdmin::findOne(['living_id' => $living_id,'admin_id' => $member_id]);
        $transtion = \Yii::$app->db->beginTransaction();
        if($op_type == '1') //设置成管理员
        {
            if($groupInfo->manager_num <= $groupInfo->cur_manager_num)
            {
                $error = '管理员数量已达上限';
                $transtion->rollBack();
                return false;
            }
            if($admin_list->admin_id > 0)
            {
                $error = '已经是管理员无需重复设置';
                $transtion->rollBack();
                return false;
            }

            $sql = 'insert into mb_living_admin(living_id,admin_id) VALUES (:lid,:uid)';
            $res = \Yii::$app->db->createCommand($sql,[':lid' => $living_id,':uid' => $member_id])->execute();
            if($res <= 0)
            {
                $error = '设置成管理员保存失败';
                \Yii::getLogger()->log($error.'   sql=:'.\Yii::$app->db->createCommand($sql,[':lid' => $living_id,':uid' => $member_id])->rawSql,Logger::LEVEL_ERROR);
                $transtion->rollBack();
                return false;
            }
            //机器人不发送腾讯云
            if($member->is_rebot == 0)
            {
                //加入腾讯云群
                if(!TimRestApi::group_add_group_member($groupInfo->other_id,strval($member_id),1, $error))
                {
                    $transtion->rollBack();
                    return false;
                }
                if(!TimRestApi::group_modify_group_member_info($groupInfo->other_id,strval($member_id),'Admin',$error))
                {
                    $error = '设置成管理员异常，回滚失败';
                    \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                    $transtion->rollBack();
                    return false;
                }

            }
            $sql = 'update mb_chat_room set cur_manager_num=cur_manager_num+1 where room_id=:rid';
            $res = \Yii::$app->db->createCommand($sql,[':rid' => $groupInfo->room_id])->execute();
            if($res <= 0){
                $error = '设置成管理员异常!';
                $transtion->rollBack();
                return false;
            }
        }
        else //取消管理员
        {
            if($admin_list->admin_id < 0)
            {
                $error = '已经取消无需再取消';
                $transtion->rollBack();
                return false;
            }
            $sql = 'delete from mb_living_admin WHERE living_id=:lid and admin_id=:uid';
            $res = \Yii::$app->db->createCommand($sql,[':lid' => $living_id,':uid' => $member_id])->execute();
            if($res <= 0 )
            {
                $error = '取消管理员保存失败';
                \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                $transtion->rollBack();
                return false;
            }
            if($member->is_rebot == 0)
            {
                if(!TimRestApi::group_modify_group_member_info($groupInfo->other_id,strval($member_id),'Member',$error))
                {
                    $error = '取消管理员保存失败';
                    \Yii::getLogger()->log($error.' :'.var_export($member->getErrors(),true),Logger::LEVEL_ERROR);
                    $transtion->rollBack();
                    return false;
                }
            }
            $sql = 'update mb_chat_room set cur_manager_num=cur_manager_num-1 where room_id=:rid';
            $res = \Yii::$app->db->createCommand($sql,[':rid' => $groupInfo->room_id])->execute();
            if($res <= 0){
                $error = '设置成管理员异常!';
                $transtion->rollBack();
                return false;
            }
        }

        \Yii::$app->cache->delete('manager_admin_list_'.$living_id);
        $transtion->commit();
        return true;
    }

} 