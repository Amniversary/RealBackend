<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 14:56
 */

namespace frontend\business;


use common\models\FriendsList;
use yii\log\Logger;

class FriendsUtil
{
    /**
     * 获取一个朋友
     * @param $user_id
     * @param $friend_user_id
     * @return null|static
     */
    public static function GetFriendOne($user_id, $friend_user_id)
    {
        return FriendsList::findOne([
            'user_id'=>$user_id,
            'friend_user_id'=>$friend_user_id
        ]);
    }
    /**
     * 获取新模型
     * @param $user_id
     * @param $friend_user_id
     * @return FriendsList
     */
    public  static function GetNewModel($user_id, $friend_user_id)
    {
        $model = new FriendsList();
        $model->user_id = $user_id;
        $model->friend_user_id = $friend_user_id;
        return $model;
    }
        /**
         * 获取好友列表
         * @param $flag
         * @param $start_id
         * @param $user_id 用户id
         */
        public static function GetFriendList($flag,$start_id,$user_id)
        {
            $friend_list  = self::GetFriendRecords($flag,$start_id,$user_id);
            $out =  self::GetFriendsFormate($friend_list);
            return $out;
        }

        /**
         * 获取朋友记录
         * @param $user_id
         */
        public static function GetFriendRecords($flag,$start_id,$user_id)
        {
            $sql = 'select record_id as row_id, account_id as user_id,ai.nick_name as user_name,pic from my_friends_list fl inner JOIN my_account_info ai on fl.friend_user_id = ai.account_id where fl.user_id=:uid ';
            $paramAry = [':uid' => $user_id];
            switch($flag)
            {
                case 'up':
                    $sql .= ' and record_id > :sid';
                    $paramAry[':sid']=$start_id;
                    break;
                case 'down':
                    $sql .= ' and record_id < :sid';
                    $paramAry[':sid']=$start_id;
                    break;
                default:
                    break;
            }
            $sql .= ' order by fl.record_id desc limit 10';
/*            \Yii::getLogger()->log($sql, Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('flag:'.$flag, Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('start_id:'.$start_id, Logger::LEVEL_ERROR);*/
            $rcList = \Yii::$app->db->createCommand($sql, $paramAry)->queryAll();
            return $rcList;
        }

        /**
         * 格式化输出
         * @param $friend_list
         */
        public static function GetFriendsFormate($friend_list)
        {
            $out = [];
            if(empty($friend_list))
            {
                return $out;
            }
            foreach($friend_list as $friend)
            {
                $friend['unread_count'] ='0';
                $other_data = self::GetNewestInfo($friend['user_id']);
                if(empty($other_data))
                {
                    $other_data =
                        [0=>[
                        'content'=>'',
                        'time'=>'',
                        'wish_id'=>'',
                        'id'=>''
                    ]];
                }

                $ary = array_merge($friend, $other_data[0]);

                $out[] = $ary;
            }
            return $out;
        }

    /**
     * 获取用户最新动态信息，打赏或评论
     * @param $user_id
     */
    public static function GetNewestInfo($user_id)
    {
        //wish_id content time
        $sql ='
select * from (
SELECT reward_id as id, wh.wish_id,CONCAT(ai1.nick_name, \'打赏了\',ai.nick_name,\'的愿望\') as content,rl.`create_time` as time FROM `my_reward_list` rl inner join my_wish wh on rl.wish_id = wh.wish_id inner join my_account_info ai on wh.publish_user_id = ai.account_id inner join my_account_info ai1 on rl.reward_user_id = ai1.account_id where wh.publish_user_id=:uid and rl.pay_status = 2
union all
select wish_comment_id as id, wh.wish_id,CONCAT(content_title,content) as content, wc.create_time as time from my_wish_comment wc  inner join my_wish wh on wc.wish_id = wh.wish_id where wh.publish_user_id=:uid1
) c order by  time desc limit 1
';
        $paramAry = [':uid'=>$user_id,':uid1'=>$user_id];
        $data = \Yii::$app->db->createCommand($sql, $paramAry)->queryAll();
        return $data;
    }
} 