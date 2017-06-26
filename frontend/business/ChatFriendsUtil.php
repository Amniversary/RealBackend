<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/9
 * Time: 10:30
 */

namespace frontend\business;


use common\components\CharToPingYinManager;
use common\models\AccountInfo;
use common\models\Attention;
use common\models\Client;
use common\models\ClientActive;
use common\models\FriendsList;
use common\models\Level;
use common\models\LevelStage;
use frontend\business\FriendsUtil;
use frontend\business\PersonalUserUtil;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class ChatFriendsUtil
{
    /**
     * 获取好友聊天背景图
     */
    public static function GetChatBackgroundPics()
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
     * 设置免打扰信息
     * @param $user_id
     * @param $friend_id
     * @param $hide_msg
     */
    public static function SetHideFriendsMsg($user_id,$friend_id,$hide_msg,&$error)
    {
        $friend = AttentionUtil::GetFriendOne($user_id,$friend_id);
        if(!isset($friend))
        {
            $error='好友不存在';
            return false;
        }
        if(!in_array(intval($hide_msg),[1,2]))
        {
            $error = '打扰信息状态错误';
            return false;
        }
        $friend->hide_msg = $hide_msg;
        if(!$friend->save())
        {
            \Yii::getLogger()->log('保存免打扰信息失败:'.var_export($friend->getErrors(),true),Logger::LEVEL_ERROR);
            $error='保存免打扰信息失败';
            return false;
        }
        return true;
    }

    /**
     * 获取好友信息，暂定为不分页
     * @param $user_id
     * @param int $page_no 页码从1开始
     * @param $page_size 页记录大小
     */
    public static function GetUserFriends($user_id)
    {
        $query = (new Query())->select(['account_id','ai.sex','ai.sign_name','ai.nick_name as source_nick_name','ai.phone_no','ifnull(fl.nick_name,ai.nick_name) as nick_name','pic','fl.chat_pic','fl.hide_msg'])->from('my_account_info as ai')
            ->limit(300)->innerJoin('my_friends_list fl','ai.account_id=fl.friend_user_id and fl.user_id=:uid',[':uid'=>$user_id])->orderBy(['fl.record_id'=>SORT_DESC]);
        return $query->all();
    }

    /**
     * 获取单独好友信息
     * @param $user_id
     * @param $friend_id
     */
    public static function GetSingleFriend($user_id,$friend_id)
    {
        $query = (new Query())->select(['account_id','ai.sex','ai.sign_name','ai.nick_name as source_nick_name','ai.phone_no','ifnull(fl.nick_name,ai.nick_name) as nick_name','pic','fl.chat_pic','fl.hide_msg'])->from('my_account_info as ai')
            ->limit(1)->innerJoin('my_friends_list fl','ai.account_id=fl.friend_user_id and fl.user_id=:uid and fl.friend_user_id=:fuid',[':uid'=>$user_id,':fuid'=>$friend_id])->orderBy(['fl.record_id'=>SORT_DESC]);
        $rst = $query->all();
        $out = [];
        if(!empty($rst))
        {
            $out = $rst[0];
            $out['full_code'] = CharToPingYinManager::getAllPY($out['nick_name']);
            $out['simple_code'] = CharToPingYinManager::getFirstPY($out['nick_name']);
        }
        return $out;
    }

    /**
     * 格式化朋友信息
     * @param $recordList
     */
    public static function GetFormateUserFriends($recordList)
    {
        if(!isset($recordList))
        {
            return [];
        }
        $rst = [];
        $model = new AccountInfo();
        $model->getAttributes([]);
        foreach($recordList as $record)
        {
            $rst[] = $record->getAttributes(['account_id','nick_name','pic']);
        }
        return $rst;
    }

    /**
     * 获取关注列表
     * @param $user_id
     */
    public static function GetAttentions($user_id,$page_no,$page_size,$self_user_id)
    {
        /*
user_id
nick_name
pic
level_id
level_pic
sign_name
sex
is_attention
         */
        $qurey = new Query();
        if($user_id == $self_user_id)
        {
            $qurey->select(['ct.client_id as user_id','ct.sex','ct.nick_name','ifnull(NULLIF(ct.icon_pic,\'\'), ct.pic) AS pic','ll.level_id','ls.level_pic','ct.sign_name','ct.sex','abs(1) as is_attention'])
                ->from('mb_attention an')
                ->innerJoin('mb_client ct','an.friend_user_id = ct.client_id and an.user_id=:uid',[':uid'=>$user_id])
                ->innerJoin('mb_client_active ca','ct.client_id=ca.user_id')
                ->innerJoin('mb_level ll','ca.level_no=ll.level_id')
                ->leftJoin('mb_level_stage ls','ll.level_max=ls.level_stage');
        }
        else
        {
            $qurey->select(['ct.client_id as user_id','ct.sex','ct.nick_name','ifnull(NULLIF(ct.icon_pic,\'\'), ct.pic) AS pic','ll.level_id','ls.level_pic','ct.sign_name','ct.sex','ifnull(ans.friend_user_id,0) as is_attention'])
                ->from('mb_attention an')
                ->innerJoin('mb_client ct','an.friend_user_id = ct.client_id and an.user_id=:uid',[':uid'=>$user_id])
                ->innerJoin('mb_client_active ca','ct.client_id=ca.user_id')
                ->innerJoin('mb_level ll','ca.level_no=ll.level_id')
                ->leftJoin('mb_level_stage ls','ll.level_max=ls.level_stage')
                ->leftJoin('(select friend_user_id from mb_attention where user_id='.strval(intval($self_user_id)).' ) ans','an.friend_user_id = ans.friend_user_id');
        }
        $out = $qurey->offset(($page_no-1)*$page_size)
            ->limit($page_size)
            ->all();

        //$friendsList = self::GetFormateUserFriends($rst);
        return $out;
    }


    /**
     * 获取粉丝列表
     * @param $user_id
     */
    public static function GetFuns($user_id,$page_no,$page_size,$self_user_id)
    {
        /*
user_id
nick_name
pic
level_id
level_pic
sign_name
sex
is_attention
         */
        $qurey = new Query();
/*        if($user_id == $self_user_id)
        {
            $qurey->select(['ct.client_id as user_id','ct.nick_name','pic','ll.level_id','ls.level_pic','ct.sign_name','ct.sex','abs(1) as is_attention'])
                ->from(Attention::tableName().' an')
                ->innerJoin(Client::tableName().' ct','an.user_id = ct.client_id and an.friend_user_id=:uid',[':uid'=>$user_id])
                ->innerJoin(ClientActive::tableName().' ca','ct.client_id=ca.user_id')
                ->innerJoin(Level::tableName().' ll','ca.level_no=ll.level_id')
                ->leftJoin(LevelStage::tableName().' ls','ll.level_max = ls.level_stage');
        }
        else
        {*/
            $qurey->select(['ct.client_id as user_id','ct.nick_name','pic','ll.level_id','ls.level_pic','ct.sign_name','ct.sex','ifnull(ans.friend_user_id,0) as is_attention'])
                ->from(Attention::tableName().' an')
                ->innerJoin(Client::tableName().' ct','an.user_id = ct.client_id and an.friend_user_id=:uid',[':uid'=>$user_id])
                ->innerJoin(ClientActive::tableName().' ca','ct.client_id=ca.user_id')
                ->innerJoin(Level::tableName().' ll','ca.level_no=ll.level_id')
                ->leftJoin(LevelStage::tableName().' ls','ll.level_max = ls.level_stage')
                ->leftJoin('(select friend_user_id from mb_attention where user_id='.strval(intval($self_user_id)).' ) ans','an.user_id = ans.friend_user_id');
        //}
        $out = $qurey->offset(($page_no-1)*$page_size)
            ->limit($page_size)
            ->all();
//\Yii::getLogger()->log('$user_id='.$user_id,'--------'.'$self_user_id='.$self_user_id,Logger::LEVEL_ERROR);
        //$friendsList = self::GetFormateUserFriends($rst);
        return $out;
    }

    /**
     * 获取个人贡献版
     * @param $user_id
     */
    public static function GetContributionBoard($user_id,$page_no,$page_size,$self_user_id)
    {
        /*
user_id
nick_name
pic
ticket_num
is_attention
         */
        //\Yii::getLogger()->log('11GetContributionBoard_'.$self_user_id,Logger::LEVEL_ERROR);
        $qurey = new Query();
        if($user_id == $self_user_id)
        {
            $qurey->select(['ct.client_id as user_id','ct.nick_name','ct.sex','pic','srt.ticket_num','abs(1) as is_attention'])
                ->from('mb_sum_reward_tickets srt')
                ->innerJoin('mb_client ct','srt.reward_user_id = ct.client_id and srt.living_master_id=:uid',[':uid'=>$user_id]);
        }
        else
        {
            $qurey->select(['ct.client_id as user_id','ct.sex','ct.nick_name','pic','srt.ticket_num','ifnull(ans.friend_user_id,0) as is_attention'])
                ->from('mb_sum_reward_tickets srt')
                ->innerJoin('mb_client ct','srt.reward_user_id = ct.client_id and srt.living_master_id=:uid',[':uid'=>$user_id])
                ->leftJoin('(select friend_user_id from mb_attention where user_id='.strval(intval($self_user_id)).' ) ans','srt.reward_user_id = ans.friend_user_id');
        }
        $out = $qurey->orderBy('srt.ticket_num desc')
            ->offset(($page_no-1)*$page_size)
            ->limit($page_size)

            ->all();
        //if($self_user_id == '61')
        //{
           // \Yii::getLogger()->log('hrl_sql_:'.$qurey->createCommand()->getRawSql(),Logger::LEVEL_ERROR);
        //\Yii::getLogger()->flush(true);
        //}
        //$friendsList = self::GetFormateUserFriends($rst);
        return $out;
    }


    /**
     * 更具用户id 获取用户贡献榜第一名id,头像
     * @param $user_id
     * @return array
     */
    public static function GetFirstContribution($user_id)
    {
        $query = (new Query())
            ->select(['ct.client_id as user_id','pic'])
            ->from('mb_sum_reward_tickets srt')
            ->innerJoin('mb_client ct','srt.reward_user_id = ct.client_id and srt.living_master_id = :ud',[':ud'=>$user_id])
            ->orderBy('srt.ticket_num desc')
            ->limit(1)
            ->all();

        $test = [];
        foreach($query as $q)
        {
            $test = $q;
        }

        return $test;
    }


    /**
     * 删除好友
     * @param $user_id
     * @param $friend_id
     * @param $error
     */
    public static function CancelAttention($user_id,$friend_id,&$error)
    {
        $friend = AttentionUtil::GetFriendOne($user_id, $friend_id);
        if(!isset($friend))
        {
            $error = '未关注，取消失败';
            \Yii::getLogger()->log($error. ' user_id:'.$user_id.' attention_id:'.$friend_id,Logger::LEVEL_ERROR);
            return false;
        }

        if(!$friend->delete())
        {
            $error = '删除好友失败';
            \Yii::getLogger()->log($error.' '.var_export($friend->getErrors(),true),Logger::LEVEL_ERROR);
           throw new Exception($error);
        }

        return true;
    }

    /**
     * 新增好友
     * @param $user_id
     * @param $friend_id
     * @param $error
     */
    public static function Attention($user_id,$attention_id,&$error)
    {
        $friend = AttentionUtil::GetFriendOne($user_id,$attention_id);
        if(!isset($friend))//自己不需要加自己为朋友
        {
            if($attention_id != $user_id)
            {
                $attentionModel = AttentionUtil::GetNewModel($user_id,$attention_id);
                if(!$attentionModel->save())
                {
                    $error = '保存好友失败';
                    \Yii::getLogger()->log($error.' '.var_export($attentionModel->getErrors(),true),Logger::LEVEL_ERROR);
                    return false;
                }
            }
            else
            {
                $error = '不能添加自己为好友';
                return false;
            }
        }
        else
        {
            $error = '已经是好友，无需添加';
            return false;
        }

        return true;
    }

    /**
     * 修改好友信息
     * @param $user_id
     * @param $nick_name
     * @param $chat_pic
     */
    public static function ModifyFriendInfo($user_id,$friend_user_id,$nick_name,$chat_pic,&$error)
    {
        $user = FriendsUtil::GetFriendOne($user_id,$friend_user_id);
        if(!($user instanceof FriendsList))
        {
            $error = '好友不存在';
            return false;
        }
        if(empty($nick_name) && empty($chat_pic))
        {
            $error = '修改内容不能为空';
            return false;
        }
        if(!empty($nick_name))
        {
            $user->nick_name = $nick_name;
        }
        if(!empty($chat_pic))
        {
            $user->chat_pic = $chat_pic;
        }

        if(!$user->save())
        {
            $error = '保存好友信息失败';
            return false;
        }
        return true;
    }

    /**
     * 获取通讯录关注列表
     * @param $user_id
     */
    public static function GetContactsAttentions($user_id,$self_user_id)
    {
        $qurey = new Query();
        if($user_id == $self_user_id)
        {
            $qurey->select(['ct.client_id as user_id','ct.client_no','ct.nick_name','ifnull(NULLIF(ct.icon_pic,\'\'), ct.pic) AS pic','ct.sign_name'])
                ->from('mb_attention an')
                ->innerJoin('mb_client ct','an.friend_user_id = ct.client_id and an.user_id=:uid',[':uid'=>$user_id])
                ->innerJoin('mb_client_active ca','ct.client_id=ca.user_id')
                ->innerJoin('mb_level ll','ca.level_no=ll.level_id')
                ->leftJoin('mb_level_stage ls','ll.level_max=ls.level_stage');
        }
        else
        {
            $qurey->select(['ct.client_id as user_id','ct.client_no','ct.nick_name','ifnull(NULLIF(ct.icon_pic,\'\'), ct.pic) AS pic','ct.sign_name'])
                ->from('mb_attention an')
                ->innerJoin('mb_client ct','an.friend_user_id = ct.client_id and an.user_id=:uid',[':uid'=>$user_id])
                ->innerJoin('mb_client_active ca','ct.client_id=ca.user_id')
                ->innerJoin('mb_level ll','ca.level_no=ll.level_id')
                ->leftJoin('mb_level_stage ls','ll.level_max=ls.level_stage')
                ->leftJoin('(select friend_user_id from mb_attention where user_id='.strval(intval($self_user_id)).' ) ans','an.friend_user_id = ans.friend_user_id');
        }
        $out = $qurey
            ->all();
        return $out;
    }

} 