<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/4
 * Time: 13:39
 */

namespace frontend\business;


use common\components\chat\emchatserver\Easemob;
use common\components\PhpLock;
use common\models\AccountInfo;
use common\models\ChatGroup;
use yii\log\Logger;

class ChatUtilHuanXin
{
    private static $huanxinHandler = null;

    /**
     * 获取环信处理实例
     * @return Easemob|null
     */
    public static function GetHandler()
    {
        if(!isset(self::$huanxinHandler))
        {
            $lock = new PhpLock('huanxinhandler');
            $lock->lock();
            if(!isset(self::$huanxinHandler))
            {
                self::$huanxinHandler = new Easemob();
            }
            $lock->unlock();
        }
        return self::$huanxinHandler;
    }

    /**
     * @param $from 发起者
     * @param array $to_id 信息接受者
     * @param $msg 内容
     * @param $error
     * @param string $type 类型 users好友  chatgroups 群
     */
    public static function ChatSendMsg($to_id,$msg,&$error,$type='users',$from='admin')
    {
        $handler = self::GetHandler();
        $rst = $handler->yy_hxSend($from, $to_id,$msg,$type,['tt'=>'dd']);
        /*{"action":"post","application":"d0128080-d6e0-11e5-b8de-678d881ecbef","uri":"https:\/\/a1.easemob.com\/meiyuan\/meiyuan","entities":[],
        "data":{"49":"success","2":"success"},
        "timestamp":1458648413812,
        "duration":2,
        "organization":"meiyuan",
        "applicationName":"meiyuan",
        "status":200}*/
        $json = json_decode($rst,true);
        if($json['status'] !== 200)
        {
            $error = $json['error'];
            return false;
        }
        //\Yii::getLogger()->log('发送消息成功'.$msg.' id:'.var_export($to_id,true),Logger::LEVEL_ERROR);
        //var_dump($rst);
        return true;
    }

    /**
     * 修改用户昵称
     * @param $user_id
     * @param $nick_name
     */
    public static function ModifyNickName($user_id,$nick_name,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->ModifyUserName($user_id,$nick_name);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '修改用户昵称返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 添加用户好友
     * @param $user_id
     * @param $friend_user_id
     * @param $error
     */
    public static function AddUserFriends($user_id,$friend_user_id,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->addFriend($user_id,$friend_user_id);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '新增用户好友返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 获取好友列表
     * @param $user_id
     * @param $error
     */
    public static function GetUserFriendsFromHuanXin($user_id,&$friendListInfo,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->showFriend($user_id);
        $friendListInfo = json_decode($rst,true);
        if(!isset($friendListInfo) || !is_array($friendListInfo))
        {
            $error = '获取用户好友返回结果异常';
            return false;
        }
        if($friendListInfo['status'] !== 200)
        {
            $error = $friendListInfo['error'];
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
    public static function DelUserFriends($user_id,$friend_user_id,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->deleteFriend($user_id,$friend_user_id);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '删除用户好友返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 批量删除用户
     * @param $limit
     * @param $sql
     * @param $error
     * @return bool
     */
    public static function BatchDelUserFriends($limit,$sql,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->batchDeleteUser($limit,$sql);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '删除用户好友返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 删除用户
     * @param $user_name
     * @return bool
     */
    public static function DelUser($user_name,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->deleteUser($user_name);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '删除用户好友返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 批量注册到环信服务器，如果一个用户已经存在则返回错误
     * @param array $user_ids
     */
    public static function BatchRegisterUser($user_ids,&$error)
    {
        $pwd = '6578912';
        $handler = self::GetHandler();
        $userList = [];
        if(!is_array($user_ids))
        {
            $error = '参数不是数组';
            return false;
        }
        foreach($user_ids as $user_id)
        {
            $userList[] = [
                'username'=>$user_id,
                'password'=>$pwd,
            ];
        }
        $rst = $handler->accreditBatchRegister($userList);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '批量注册im用户返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 获取群用户信息，重复id不错报
     * @param $group_id
     * @param $error
     */
    public static function GetGroupUserList($group_id,&$groupListInfo,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->groupsUser($group_id);
        $groupListInfo = json_decode($rst,true);
        if(!isset($groupListInfo) || !is_array($groupListInfo))
        {
            $error = '获取im群用户列表返回结果异常';
            return false;
        }
        if($groupListInfo['status'] !== 200)
        {
            $error = $groupListInfo['error'];
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
        $handler = self::GetHandler();
        $rst = $handler->addGroupsUser($group_id,$user_id);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '新增im群用户返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }


    /**
     * 批量添加群用户
     * @param $group_id
     * @param $userList
     * @param $error
     * @return bool
     */
    public static function BatchAddUserToGroup($group_id,$userList,&$error)
    {
        //    public static function usernames
        if(empty($group_id) || !isset($userList) || !is_array($userList))
        {
            $error = '参数错误';
            return false;
        }
        $options['usernames']=[];
        foreach($userList as $user)
        {
            $options['usernames'][] = $user;
        }
        $handler = self::GetHandler();
        $rst = $handler->batchAddGroupsUser($group_id,$options);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '批量添加im群用户返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 删除群用户
     * @param $group_id
     * @param $user_name
     */
    public static function DelGroupUser($group_id,$user_name,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->delGroupsUser($group_id,$user_name);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '删除im群用户返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 删除群
     * @param $group_id
     * @param $error
     * @return bool
     */
    public static function DelGroup($group_id,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->deleteGroups($group_id);
        $rstAry = json_decode($rst,true);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '删除im群返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            return false;
        }
        return true;
    }

    /**
     * 获取所有群信息
     * @param $error
     * @return bool
     */
    public static function GetGroupList(&$groupList,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->getGroupsList();
        $groupList = json_decode($rst,true);
        if(!isset($groupList) || !is_array($groupList))
        {
            $error = '获取im所有群返回结果异常';
            return false;
        }
        if($groupList['status'] !== 200)
        {
            $error = $groupList['error'];
            return false;
        }
        return true;
    }
    

    /**
     * 创建群到环信服务器
     */
    public static function CreateGroup($groupname,$desc,$owner,$group_type=1,&$group_id,&$error,$user_ids=[])
    {
       $options = [
'groupname'=>$groupname,
'desc'=>$desc,
'public'=>false,
'approval'=>false,// ($group_type == '2'?true:false),
'owner'=>strval($owner),
'members'=>$user_ids
         ];
        $handler = self::GetHandler();
        //\Yii::getLogger()->log('error:'.var_export($options,true),Logger::LEVEL_ERROR);
        $rst = $handler->createGroups($options);
        $rstAry = json_decode($rst,true);
        //\Yii::getLogger()->log('error:'.var_export($options,true),Logger::LEVEL_ERROR);
        if(!isset($rstAry) || !is_array($rstAry))
        {
            $error = '注册im群返回结果异常';
            return false;
        }
        if($rstAry['status'] !== 200)
        {
            $error = $rstAry['error'];
            \Yii::getLogger()->log('error:'.$rst,Logger::LEVEL_ERROR);
            return false;
        }
        $group_id = $rstAry['data']['groupid'];
        return true;
    }

    /**
     * 环信服务器获取群信息
     * @param $group_id
     * @param $error
     */
    public static function GetGroupInfo($group_id,&$groupInfo,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->getGroupsDetails($group_id);
        $groupInfo = json_decode($rst,true);
        if(!isset($groupInfo) || !is_array($groupInfo))
        {
            $error = '获取im群信息返回结果异常';
            return false;
        }
        if($groupInfo['status'] !== 200)
        {
            $error = $groupInfo['error'];
            return false;
        }
        return true;
    }

    /**
     * 修改群信息
     * @param $group_id
     * @param $group_name
     * @param $desc
     * @param $error
     * @return bool
     */
    public static function ModifyGroup($group_id,$group_name,$desc,&$error)
    {
        $handler = self::GetHandler();
        $rst = $handler->ModifyGroupInfo($group_id,$group_name,$desc,null);
        $groupInfo = json_decode($rst,true);
        if(!isset($groupInfo) || !is_array($groupInfo))
        {
            $error = '修改im群信息返回结果异常';
            return false;
        }
        if($groupInfo['status'] !== 200)
        {
            $error = $groupInfo['error'];
            return false;
        }
        return true;
    }
} 