<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/6
 * Time: 13:23
 */

namespace frontend\business;


use common\components\PhpLock;
use common\components\tenxunlivingsdk\TimRestApi;
use common\models\EnterRoomNote;
use common\models\EnterRoomNoteUpdata;
use common\models\SystemMessage;
use yii\db\Query;
use yii\log\Logger;

class EnterRoomNoteUtil
{

    /**
     * 发送进入房间提示
     * @param $living_id
     * @param $user_id
     * @param $error
     * @return bool
     */
        public static function SendEnterRoomNote($living_id,$user_id,&$error,$extra = null)
        {
            $userActive = UserActiveUtil::GetUserActiveByUserId($user_id);
            if(!isset($userActive))
            {
                $error = '数据异常，活跃记录不存在';
                return false;
            }
            $level = $userActive->level_no;
            $notInfo = self::GetNoteByLevel($level);
            if(isset($notInfo))
            {
                $user = ClientUtil::GetClientById($user_id);
                if(!isset($user))
                {
                    $error = '用户不存在';
                    \Yii::getLogger()->log($error.' :'.$user_id,Logger::LEVEL_ERROR);
                    return false;
                }
                $nick_name = $user->nick_name;
                $words = $notInfo->note_words;
                $words =str_replace('{nick_name}',$nick_name,$words);
                $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($living_id);
                if(!isset($chatRoom))
                {
                    $error = '直播间不存在，数据异常';
                    return false;
                }
                $sendInfo = [
                    'type'=>3,
                    'note_type'=>$notInfo->level_no_start.'_'.$notInfo->level_no_end,//欢迎消息类别，适用于不同消息不同效果
                    'nick_name'=>$user->nick_name,
                    'level'=>$level,
                    'words'=>$words,
                    'user_id'=>$user_id
                ];
                $sv = json_encode($sendInfo);
                $data = [
                    'key_word'=>'send_people_im',
                    'user_id'=>$user_id,
                    'chat_room'=>$chatRoom['other_id'],
                    'living_id'=>$chatRoom['living_id'],
                    'sv'=>$sv,
                ];

                if ($extra) {
                    $data['extra'] = $extra;
                    $data['tag'] = 306;
                }

                if(!JobUtil::AddImJob('tencent_im',$data,$error))
                {
                    \Yii::getLogger()->log('enter_living_hint job save error:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
                    return false;
                }

                //延迟1秒发送人数消息
                /*if(!JobUtil::AddImDelayJob('tencent_im',$data,1,$error))
                {
                    \Yii::getLogger()->log('enter_living_hint job save error:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
                    return false;
                }*/

                /*if(!TimRestApi::group_send_group_msg_custom($user_id,$chatRoom['other_id'],$sv,$error))
                {
                    \Yii::getLogger()->log('进入房间提示im消息发送失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
//                    return false;
                }*/
            }
            return true;
        }


    /**
     * 发送进入直播间系统消息
     * @param $living_id
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function SendEnterMessage($living_id,$user_id,&$error)
    {
        $userActive = UserActiveUtil::GetUserActiveByUserId($user_id);
        if(!isset($userActive))
        {
            $error = '数据异常，活跃记录不存在';
            return false;
        }
        $notInfo = self::GetSystemMessage();
        if(isset($notInfo))
        {
            $user = ClientUtil::GetClientById($user_id);
            if(!isset($user))
            {
                $error = '用户不存在';
                \Yii::getLogger()->log($error.' :'.$user_id,Logger::LEVEL_ERROR);
                return false;
            }

            $chatRoom = ChatGroupUtil::GetChatGroupByLivingId($living_id);
            if(!isset($chatRoom))
            {
                $error = '直播间不存在，数据异常';
                return false;
            }


            //获得用户的等级
            $user_grade = self::GetUserGrade($user_id);

            //获得等级特效信息
            $grade  = self::GetGradeEffect($user_grade);


            foreach($notInfo as $n)
            {
                $words = $n->system_message;
                $sendInfo = [
                    'type'=>6,
                    'user_id'=>$user_id,
                    'words'=>$words,
                    'effect_id' => $grade,
                    'grade' => $user_grade,
                ];
                $sv = json_encode($sendInfo);
                if(!TimRestApi::group_send_group_msg_custom($user_id,$chatRoom['other_id'],$sv,$error))
                {
                    \Yii::getLogger()->log('进入直播间系统im消息发送失败：'.'fail:'.$error.' date_time:'.date('Y-m-d H:i:s'),Logger::LEVEL_ERROR);
//                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 数组的方式放回系统消息
     * @return array
     */
    public static function  GetSystemMsgToArray($flush = false)
    {
        $rst = [];
        $key = 'mb_get_system_msg_living';
        if($flush)
        {
            $systemInfo = self::GetSystemMessage();
            foreach($systemInfo as $n)
            {
                $rst[] = $n->system_message;
            }
            $str = json_encode($rst);
            if(!\Yii::$app->cache->set($key,$str))
            {
                \Yii::getLogger()->log('设置系统消息缓存失败',Logger::LEVEL_ERROR);
            }
        }
        else
        {
            $hasData =false;
            $str = \Yii::$app->cache->get($key);
            if(!empty($str))
            {
                $hasData =true;
                $rst = json_decode($str);
            }
            if((!$hasData))
            {
                $lock = new PhpLock('get_living_system_note');
                $lock->lock();
                $str = \Yii::$app->cache->get($key);
                if(!empty($str))
                {
                    $rst = json_decode($str);
                }
                else
                {
                    $systemInfo = self::GetSystemMessage();
                    foreach($systemInfo as $n)
                    {
                        $rst[] = $n->system_message;
                    }
                    $str = json_encode($rst);
                    if(!\Yii::$app->cache->set($key,$str))
                    {
                        \Yii::getLogger()->log('设置系统消息缓存失败',Logger::LEVEL_ERROR);
                    }
                }
                $lock->unlock();
            }
        }
        return $rst;
    }

    /**
     * 获取进入直播提示
     * @param $level_no
     * @return null|static
     */
    public static function GetNoteByLevel($level_no)
    {
        return EnterRoomNote::findOne(['and',['<=','level_no_start',$level_no],['>=','level_no_end',$level_no]]);
    }

    /**
     * 获取进入直播系统信息
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetSystemMessage()
    {
          $query = SystemMessage::find()
            ->select(['system_message'])
            ->where('status =1')
            ->orderBy('order ASC')
            ->all();
        return $query;
    }

    public static function GetEnterRoomNoteById($record_id)
    {
        return EnterRoomNoteUpdata::findOne(['record_id'=>$record_id]);
    }

    /**
     * 拿到用户等级特效id
     * @param $user_grade
     * @return array
     */
    public static function GetGradeEffect($user_grade)
    {
        $query = (new Query())
            ->select(['effect_id'])
            ->from('mb_enter_room_note')
            ->where(':user_grade between level_no_start and level_no_end',[':user_grade' => $user_grade ])
            ->scalar();

        return $query;
    }

    /**
     * 获取用户等级
     * @param $user_id
     * @return bool|string
     */
    public static function GetUserGrade($user_id)
    {
        $query = (new Query())
            ->select(['level_no'])
            ->from('mb_client_active')
            ->where('user_id= :uid',[':uid' => $user_id])
            ->scalar();

        return $query;
    }
} 