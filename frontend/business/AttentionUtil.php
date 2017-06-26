<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 14:56
 */

namespace frontend\business;


use common\components\tenxunlivingsdk\TimRestApi;
use common\models\Attention;
use common\models\BlackList;
use common\models\FriendsList;
use frontend\business\SaveRecordByransactions\SaveByTransaction\AttentionNumModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FunsNumModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\FunsTimeStatisticByTrans;
use yii\db\Query;
use yii\log\Logger;

class AttentionUtil
{

    /**
     * 获取个推用户
     * @param $user_id
     * @param $page_no
     * @param $page_size
     */
    public static function GetFunForGeTui($user_id,$page_no,$page_size)
    {
        $query = new Query();
        $query->offset(($page_no -1)*$page_size)
            ->limit($page_size)
            ->select(['getui_id','unique_no'])
            ->from('mb_attention an')
            ->innerJoin('mb_client ct','an.user_id=ct.client_id and an.friend_user_id=:fid',[':fid'=>$user_id])
            ->where(['an.friend_user_id'=>$user_id]);
        return $query->all();
    }

    /**
     * 获取关注主播的人数
     * @param $living_master_id
     * @param $app_id
     * @return mixed
     */
    public static function GetAttentionFriendsToGetTui( $living_master_id,$app_id )
    {
        $SQL = "SELECT COUNT( * ) AS num  FROM  mb_attention  AS  a  INNER JOIN  mb_client as c ON c.client_id = a.user_id   WHERE  a.friend_user_id='$living_master_id' AND  c.app_id='$app_id'";
        $data = \Yii::$app->db->createCommand( $SQL )->queryOne();
        return  $data['num'];
    }

    /**
     * 分页获取关注主播的人数
     * @param $page
     * @param $pageSize
     * @param $living_master_id
     * @param $app_id
     * @return array
     */
    public static function GetAttentionFriendsPageToGetTui( $page,$pageSize,$living_master_id,$app_id )
    {
        $start_Limit = ( $page - 1 ) * $pageSize;
        $SQL = "SELECT c.getui_id AS cid, c.unique_no AS alias, c.app_id  FROM  mb_attention  AS  a  INNER JOIN  mb_client as c ON c.client_id = a.user_id  WHERE  a.friend_user_id='$living_master_id' AND  c.app_id='$app_id' Limit  $start_Limit,$pageSize ";
        $data = \Yii::$app->db->createCommand( $SQL )->queryAll();
        return $data;
    }

    /**
     * 获取一个朋友
     * @param $user_id
     * @param $friend_user_id
     * @return null|static
     */
    public static function  GetFriendOne($user_id, $friend_user_id)
    {
        return Attention::findOne([
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
    public static function GetNewModel($user_id, $friend_user_id)
    {
        $model = new Attention();
        $model->user_id = $user_id;
        $model->friend_user_id = $friend_user_id;
        $model->recive_msg = 1;
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
         * 获取user_id 的所有朋友
         * @param $user_id
         * @return static[]
         */
        public static function GetFriendListInfo($user_id){
            return Attention::findAll('user_id='.$user_id);
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

    /**
     * 异步处理关注
     * @param $jobData
     */
    public static function BeanTalkdAttention($jobData,&$error)
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }
        /*
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
            'op_type'=>'attention'
         */
        $op_type =$jobData->op_type;
        $user_id = $jobData->user_id;
        $attention_id = $jobData->attention_id;
        $key = (($op_type === 'attention')? 'set_attention_im':'delete_attention_im');
        $data = [
            'key_word'=>$key,
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
        ];
        if($op_type === 'attention')
        {
            //im 加好友
            if(!JobUtil::AddImJob('tencent_im',$data,$error))
            {
                \Yii::getLogger()->log('set_attention job save error:'.$error,Logger::LEVEL_ERROR);
            }
            /*if(!TimRestApi::sns_friend_import($user_id,$attention_id,$error))
            {
                return false;
            }*/
        }
        else
        {
            //$key_word = 'delete_attention';
            if(!JobUtil::AddImJob('tencent_im',$data,$error))
            {
                \Yii::getLogger()->log('delete_attention job save error:'.$error,Logger::LEVEL_ERROR);
            }
            /*if(!TimRestApi::sns_friend_delete($user_id,$attention_id,$error))
            {
                return false;
            }*/
        }
        //更新关注数
        $transactions= [];
        $clentActive = ClientActiveUtil::GetClientActiveInfoByUserId($user_id);
        $transactions[] = new AttentionNumModifyByTrans($clentActive,['op_type'=>$op_type]);
        //更新粉丝数
        $clentActiveFuns = ClientActiveUtil::GetClientActiveInfoByUserId($attention_id);
        $transactions[] = new FunsNumModifyByTrans($clentActiveFuns,['op_type'=>$op_type]);
        //加入粉丝周月日统计
        $transactions[] = new FunsTimeStatisticByTrans($clentActiveFuns,['op_type'=>$op_type]);
        if(!SaveByTransUtil::RewardSaveByTransaction($transactions,$error))
        {
            return false;
        }
        return true;
    }

    /**
     * 异步处理注册
     * @param $jobData
     * @param $error
     * @return bool
     */
    public static function BeansTalkLogin($jobData,&$error)
    {
        if(!($jobData instanceof \stdClass))
        {
            $error = '不是json对象，数据异常';
            return false;
        }

        $userId = strval($jobData->user_id);
        $nickName = $jobData->nick_name;
        $Pic = $jobData->pic;

        if(!TimRestApi::account_import($userId,$nickName,$Pic,$error))
        {
            return false;
        }

        return true;
    }




    /**
     * 获取个人关注的直播
     * @param $id
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetAttentionLiving($id,$page,$page_size = 5){
        $query = (new Query())
            ->select(['c.is_contract','l.living_pic_url','l.pull_rtmp_url','c.client_no','c.client_id as user_id','c.nick_name','ifnull(c.main_pic,c.pic) as pic','l.city','l.living_id','r.other_id as group_id',
            'l.living_title','l.device_type','p.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(l.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'l.game_name','l.living_type','ifnull(l.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_attention a')
            ->innerJoin('mb_client c','a.friend_user_id=c.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = c.client_id')
            ->innerJoin('mb_level ll','ll.level_name = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_living l','a.friend_user_id=l.living_master_id')
            ->innerJoin('mb_chat_room r','r.living_id=l.living_id')
            ->innerJoin('mb_living_personnum p','l.living_id=p.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=l.living_id and lp.living_before_id=l.living_before_id and lp.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=l.living_id and lpt.living_before_id=l.living_before_id and lpt.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_guess_record gr','gr.living_id=l.living_id and gr.room_no=l.room_no and gr.user_id=:uid',[':uid'=>$id])
            ->where('a.user_id=:id and l.status=2',[':id'=>$id])
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }

    /**
     * 获取个人关注的直播
     * @param $livingType [1,2] 或 1 或 [1,2,3]
     * @param $id
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetAttentionLivingByLivingType($livingType,$id,$page,$page_size =5){
        $query = (new Query())
            ->select(['c.is_contract','l.living_pic_url','l.pull_rtmp_url','c.client_no','c.client_id as user_id','c.nick_name','ifnull(c.main_pic,c.pic) as pic','l.city','l.living_id','r.other_id as group_id',
                'l.living_title','l.device_type','p.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(l.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'l.game_name','l.living_type','ifnull(l.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_attention a')
            ->innerJoin('mb_client c','a.friend_user_id=c.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = c.client_id')
            ->innerJoin('mb_level ll','ll.level_name = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_living l','a.friend_user_id=l.living_master_id')
            ->innerJoin('mb_chat_room r','r.living_id=l.living_id')
            ->innerJoin('mb_living_personnum p','l.living_id=p.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=l.living_id and lp.living_before_id=l.living_before_id and lp.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=l.living_id and lpt.living_before_id=l.living_before_id and lpt.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_guess_record gr','gr.living_id=l.living_id and gr.room_no=l.room_no and gr.user_id=:uid',[':uid'=>$id])
            ->where('a.user_id=:id and l.status=2',[':id'=>$id])
            ->andFilterWhere(['l.living_type'=>$livingType])
            ->orFilterWhere(['l.status'=>2,'l.living_type'=>5])
            ->groupBy('l.living_id')
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }

    /**
     * 获取个人关注的直播
     * @param $appID
     * @param $livingType [1,2] 或 1 或 [1,2,3]
     * @param $id
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetAttentionLivingByAppIDForLivingType($appID,$livingType,$id,$page,$page_size =5){
        $query = (new Query())
            ->select(['c.is_contract','l.living_pic_url','l.pull_rtmp_url','c.client_no','c.client_id as user_id','c.nick_name','ifnull(c.main_pic,c.pic) as pic','l.city','l.living_id','r.other_id as group_id',
                'l.living_title','l.device_type','p.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(l.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'l.game_name','l.living_type','ifnull(l.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_attention a')
            ->innerJoin('mb_client c','a.friend_user_id=c.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = c.client_id')
            ->innerJoin('mb_level ll','ll.level_name = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_living l','a.friend_user_id=l.living_master_id')
            ->innerJoin('mb_chat_room r','r.living_id=l.living_id')
            ->innerJoin('mb_living_personnum p','l.living_id=p.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=l.living_id and lp.living_before_id=l.living_before_id and lp.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=l.living_id and lpt.living_before_id=l.living_before_id and lpt.living_master_id=l.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$id])
            ->leftJoin('mb_guess_record gr','gr.living_id=l.living_id and gr.room_no=l.room_no and gr.user_id=:uid',[':uid'=>$id])
            ->where('a.user_id=:id and l.status=2',[':id'=>$id])
            ->andFilterWhere(['l.app_id'=>$appID])
            ->andFilterWhere(['l.living_type'=>$livingType])
            ->orFilterWhere(['l.status'=>2,'l.living_type'=>5])
            ->groupBy('l.living_id')
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }
} 