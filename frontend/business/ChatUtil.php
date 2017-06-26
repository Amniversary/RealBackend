<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/4
 * Time: 13:39
 */

namespace frontend\business;


use common\models\AccountInfo;
use common\models\ChatGroup;
use yii\log\Logger;

class ChatUtil
{
    /**
     * 添加用户好友
     * @param $user_id
     * @param $friend_user_id
     * @param $error
     */
    public static function Attention($user_id,$attention_id,&$error)
    {
        if(!ChatFriendsUtil::Attention($user_id,$attention_id,$error))
        {
            return false;
        }
        return true;
    }
    /**
     * 删除好友
     * @param $user_id
     * @param $friend_user_id
     * @param $error
     * @return bool
     */
    public static function CancelAttention($user_id,$attention_id,&$error)
    {
        if(!ChatFriendsUtil::CancelAttention($user_id,$attention_id,$error))
        {
            \Yii::getLogger()->log('删除本地好友异常： '.$error,Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 新增群用户
     * @param $group_id
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function AddUserToGroup($group_id,$user_id,&$error)
    {
        $model = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($model instanceof ChatGroup))
        {
            $error = '该分组不存在';
            return false;
        }
        if(!ChatUtilHuanXin::AddUserToGroup($model->other_id,$user_id,$error))
        {
            return false;
        }
        if(!ChatPersonGroupUtil::AddUserToGroup($group_id,$user_id,2,$error))
        {
            if(ChatUtilHuanXin::DelGroupUser($model->other_id,$user_id,$error))
            {
                \Yii::getLogger()->log('删除环信用户失败：'.$error,Logger::LEVEL_ERROR);
            }
            return false;
        }

        return true;
    }

    /**
     * 批量增加群成员
     * @param $group_id
     * @param $user_ids
     * @param $error
     * @return bool
     */
    public static function BatchAddUserToGroup($group_id,$user_ids,&$error)
    {
        $model = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($model instanceof ChatGroup))
        {
            $error = '该分组不存在';
            return false;
        }
        if(!ChatUtilHuanXin::BatchAddUserToGroup($model->other_id,$user_ids,$error))
        {
            return false;
        }
        if(!ChatPersonGroupUtil::BatchAddUserToGroup($group_id,$user_ids,$error))
        {
            foreach($user_ids as $user_id)
            {
                if(ChatUtilHuanXin::DelGroupUser($model->other_id,$user_id,$error))
                {
                    \Yii::getLogger()->log('删除环信用户失败：'.$error,Logger::LEVEL_ERROR);
                }
            }
            return false;
        }

        return true;
    }

    /**
     * 删除群组
     * @param $group_id
     * @param $error
     * @return bool
     */
    public static function DelGroup($group_id,&$error)
    {
        $group = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($group instanceof ChatGroup))
        {
            $error = '找不到群';
            return false;
        }
        if(!ChatUtilHuanXin::DelGroup($group->other_id,$error))
        {
            return false;
        }
        if(!ChatGroupUtil::DelGroupById($group_id,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 群主剔除成员
     * @param $group_id
     * @param $user_id
     * @param $error
     */
    public static function KickGroupMember($operate_user_id, $group_id,$user_id,&$error)
    {
        $model = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($model instanceof ChatGroup))
        {
            $error = '该分组不存在';
            return false;
        }
        if($operate_user_id != $model->group_master_id)
        {
            $error = '您不是群主，无法剔除成员';
            return false;
        }
        else
        {
            if($operate_user_id == $user_id)
            {
                $error = '自己不能剔除自己';
                return false;
            }
            if(!ChatPersonGroupUtil::DelUserFromGroup($group_id,$user_id,$error))
            {
                return false;
            }
            if(!ChatUtilHuanXin::DelGroupUser($model->other_id,$user_id,$error))
            {
                if(!ChatPersonGroupUtil::AddUserToGroup($group_id,$user_id,2,$error))
                {
                    \Yii::getLogger()->log('删除群用户失败回滚,新增用户到群失败'.$error.' user_id:'.$user_id,' group_id:'.$group_id,Logger::LEVEL_ERROR);
                }
                return false;
            }
        }

        return true;
    }

    /**
     * 删除群用户
     * @param $group_id
     * @param $user_name
     */
    public static function DelGroupUser($group_id,$user_id,&$error)
    {
        $model = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($model instanceof ChatGroup))
        {
            $error = '该分组不存在';
            return false;
        }
        if($user_id == $model->group_master_id)
        {
            //群主退群，删除整个群
            if(!self::DelGroup($group_id,$error))
            {
                return false;
            }
        }
        else
        {
            if(!ChatPersonGroupUtil::DelUserFromGroup($group_id,$user_id,$error))
            {
                return false;
            }
            if(!ChatUtilHuanXin::DelGroupUser($model->other_id,$user_id,$error))
            {
                if(!ChatPersonGroupUtil::AddUserToGroup($group_id,$user_id,2,$error))
                {
                    \Yii::getLogger()->log('删除群用户失败回滚,新增用户到群失败'.$error.' user_id:'.$user_id,' group_id:'.$group_id,Logger::LEVEL_ERROR);
                }
                return false;
            }
        }

        return true;
    }


    /**
     * 创建群
     * @param $groupname
     * @param $desc
     * @param $owner
     * @param $pic
     * @param $error
     * @return bool
     */
    public static function CreateGroup($groupname,$desc,$owner,$pic,$chat_pic,$group_type=1,$wish_id=0,&$error)
    {
        $other_id = null;
        if(!ChatUtilHuanXin::CreateGroup($groupname,$desc,$owner,$group_type,$other_id,$error))
        {
            return false;
        }
        if(!ChatGroupUtil::CreateChatGroup($groupname,$desc,$owner,$pic,$other_id,$chat_pic,$group_type,$wish_id,$error))
        {
            if(!self::DelGroup($other_id,$error))
            {
                \Yii::getLogger()->log('删除环信群失败：'.$error,Logger::LEVEL_ERROR);
            }
            $error = '创建本地群失败';
            return false;
        }
        $group_id = $error;
        //返回群id
        $error = [
            'other_id'=>$other_id,
            'group_id'=>$group_id
        ];
        return true;
    }


    /**
     * 创建群
     * @param $groupname
     * @param $desc
     * @param $owner
     * @param $pic
     * @param $chat_pic
     * @param int $group_type
     * @param int $wish_id
     * @param $error
     * @return bool
     */
    public static function CreateGroupNew($params,&$error)
    {
        //$groupname,$desc,$owner,$pic,$chat_pic,$group_type=1,$wish_id=0,
        if(!isset($params['wish_id']))
        {
            $params['wish_id']=0;
        }
        if(!isset($params['group_type']))
        {
            $params['group_type']=1;
        }
        /*
            'group_name'=>$passParams['groupname'],
            'describtion'=>$passParams['desc'],
            'group_master_id'=>$user_id,
            'icon'=>empty($passParams['pic'])?$pic:$passParams['pic'],
            'group_type'=>$passParams['group_type'],
            'wish_id'=>$passParams['wish_id'],
            'chat_pic'=>empty($passParams['chat_pic'])?$chat_pic:$passParams['chat_pic'],
            'user_ids'=>$passParams['user_ids'],
         */
        if(!is_array($params['user_ids']))
        {
            $params['user_ids']=[];
        }
        $other_id = null;
        if(!ChatUtilHuanXin::CreateGroup($params['group_name'],$params['describtion'],$params['group_master_id'],$params['group_type'],$other_id,$error,$params['user_ids']))
        {
            \Yii::getLogger()->log('环信创建群error:'.$error,Logger::LEVEL_ERROR);
            return false;
        }
        if(!ChatGroupUtil::CreateChatGroup($params['group_name'],
            $params['describtion'],
            $params['group_master_id'],
            $params['icon'],$other_id,
            $params['chat_pic'],
            $params['group_type'],
            $params['wish_id'],$error,
            $params['user_ids']))
        {
            $source_error = $error;
            if(!self::DelGroup($other_id,$error))
            {
                \Yii::getLogger()->log('删除环信群失败：'.$error,Logger::LEVEL_ERROR);
            }
            $error =$source_error;
            return false;
        }
        $group_id = $error;
        //返回群id
        $error = [
            'other_id'=>$other_id,
            'group_id'=>$group_id,
            'group_name'=>$params['group_name'],
            'icon'=>$params['icon']
        ];
        return true;
    }


    /**
     * 修改群信息
     * @param $group_id
     * @param $groupname
     * @param $desc
     */
    public static function ModifyGroup($group_id,$groupname,$desc,$icon,$chat_pic,&$error)
    {
        $model = ChatGroupUtil::GetChatGroupById($group_id);
        if(!($model instanceof ChatGroup))
        {
            $error = '群不存在';
            return false;
        }
        if(!ChatGroupUtil::ModifyGroupName($model,$groupname,$desc,$icon,$chat_pic,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 发送消息
     * @param $recived_user_id
     * @param $msg
     * @param $error
     */
    public static function SendMsg($recived_user_id,$msg,$error)
    {

    }
} 