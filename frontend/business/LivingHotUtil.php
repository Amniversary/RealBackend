<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/28
 * Time: 22:13
 */

namespace frontend\business;


use common\models\LivingHot;
use yii\db\Query;
use yii\log\Logger;

class LivingHotUtil
{
    /**
     * 根据id获取直播热度值
     * @par$living_id
     */
    public static function GetLivingHotByLivingId($living_id)
    {
        return LivingHot::findOne(['living_id'=>$living_id]);
    }

    /**
     * 根据请求获取热门直播列表
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetHotLivingList($page,$page_size = 10)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bc.client_no','bc.unique_no','if(lv.status=2,1,0) as flag','bc.sex','lv.living_pic_url','lv.pull_rtmp_url','lv.living_id','lv.city','living_title','lv.living_master_id as user_id','lv.device_type','bc.nick_name','IFNULL(NULLIF(bc.main_pic,\'\'),bc.pic) as pic','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color','lv.game_name','lv.living_type'
            ])
            ->from('mb_living_hot lh')
            ->innerJoin('mb_living lv','lh.living_id = lv.living_id')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id = lv.living_id')
            ->innerJoin('mb_client bc','lv.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','lv.living_id = cr.living_id')
            ->where('lv.status=2')
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->orderBy('lh.order_no,lh.hot_num desc')
            ->all();
        return $query;
    }

    /**
     * 根据不用的livingType请求获取热门直播列表
     * @param $livingType [1,2] 或1 或 [1,2,3]
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetHotLivingListByLivingType($livingType,$user_id,$page,$page_size = 10)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bc.client_no','bc.unique_no','if(lv.status=2,1,0) as flag','bc.sex','lv.living_pic_url','lv.pull_rtmp_url','lv.living_id','lv.city','living_title','lv.living_master_id as user_id','lv.device_type','bc.nick_name','IFNULL(NULLIF(bc.main_pic,""),bc.pic) as pic','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color','lv.game_name','lv.living_type',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(lv.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'ifnull(lv.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_living_hot lh')
            ->innerJoin('mb_living lv','lh.living_id = lv.living_id')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id = lv.living_id')
            ->innerJoin('mb_client bc','lv.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','lv.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=lv.living_id and lp.living_before_id=lv.living_before_id and lp.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=lv.living_id and lpt.living_before_id=lv.living_before_id and lpt.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=lv.living_id and gr.room_no=lv.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->andFilterWhere(['lv.status'=>2])
            ->andFilterWhere(['lv.living_type'=>$livingType])
            ->orFilterWhere(['lv.status'=>2,'lv.living_type'=>5])
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->groupBy('lv.living_id')
            ->orderBy('lh.order_no,lh.hot_num desc')
            ->all();

        return $query;
    }

    /**
     * 根据不用的appID及livingType请求获取热门直播列表
     * @param $appID 应用程序的id
     * @param $livingType $livingType [1,2] 或1 或 [1,2,3]
     * @param $user_id
     * @param $page
     * @param int $page_size
     * @return array
     */
    public static function GetHotLivingListByAppIDForLivingType($appID,$livingType,$user_id,$page,$page_size = 10)
    {
        $query = (new Query())
            ->select(['bc.is_contract','bc.client_no','bc.unique_no','if(lv.status=2,1,0) as flag','bc.sex','lv.living_pic_url','lv.pull_rtmp_url','lv.living_id','lv.city','living_title','lv.living_master_id as user_id','lv.device_type','bc.nick_name','IFNULL(NULLIF(bc.main_pic,"" ),bc.pic) as pic','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color','lv.game_name','lv.living_type',
                'if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(lv.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'ifnull(lv.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_living_hot lh')
            ->innerJoin('mb_living lv','lh.living_id = lv.living_id')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id = lv.living_id')
            ->innerJoin('mb_client bc','lv.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','lv.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=lv.living_id and lp.living_before_id=lv.living_before_id and lp.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=lv.living_id and lpt.living_before_id=lv.living_before_id and lpt.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=lv.living_id and gr.room_no=lv.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->andFilterWhere(['lv.status'=>2])
            ->andFilterWhere(['lv.app_id'=>$appID])
            ->andFilterWhere(['lv.living_type'=>$livingType])
            ->orFilterWhere(['lv.status'=>2,'lv.living_type'=>5])
            ->offset(($page -1)*$page_size)
            ->limit($page_size)
            ->groupBy('lv.living_id')
            ->orderBy([
                'lh.order_no' => SORT_DESC,
                'lh.hot_num' => SORT_DESC
            ])
            ->all();

        return $query;
    }

    /**
     * 获取单个用户的直播信息
     * @param $user_id
     * @return array|bool
     */
    public static function GetOneHotLivingInfo($living_master_id,$user_id)
    {
        $query = (new Query())
            ->select(['if(bc.client_type=2,1,0) as is_police','bc.is_contract','bc.client_no','bc.unique_no','if(lv.status=2,1,0) as flag','bc.sex','lv.living_pic_url','lv.pull_rtmp_url','lv.living_id','lv.city','living_title','lv.living_master_id as user_id','lv.device_type','bc.nick_name','IFNULL(bc.main_pic,bc.pic) as pic','cr.other_id as group_id',
                'mlp.person_count as living_num',
                'ca.level_no','ls.level_pic','ls.level_bg','ls.font_size','ls.color','if(ifnull(lp.private_id,0) = 0,0,1) as private_status','if(ifnull(lpv.views_id,0)=0,0,1) as private_views','if(lv.living_type=3,lp.password,lpt.password) as password','if(ifnull(lpt.tikcet_id,0) = 0,0,1) as tikcet_status','if(ifnull(lptv.views_id,0)=0,0,1) as tikcet_views',
                'lv.game_name','lv.living_type','ifnull(lv.room_no,0) as room_no','ifnull(guess_num,-1) as guess_num','ifnull(free_num,-1) as over_guess_num','ifnull(lpt.tickets,0) as tickets_num'
            ])
            ->from('mb_living_hot lh')
            ->innerJoin('mb_living lv','lh.living_id = lv.living_id')
            ->innerJoin('mb_living_personnum mlp','mlp.living_id = lv.living_id')
            ->innerJoin('mb_client bc','lv.living_master_id = bc.client_id')
            ->innerJoin('mb_client_active ca','ca.user_id = bc.client_id')
            ->innerJoin('mb_level ll','ll.level_id = ca.level_no')
            ->innerJoin('mb_level_stage ls','ls.level_stage = ll.level_max')
            ->innerJoin('mb_chat_room cr','lv.living_id = cr.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=lv.living_id and lp.living_before_id=lv.living_before_id and lp.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_private_views lpv','lpv.private_id=lp.private_id and lpv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_living_passwrod_ticket lpt','lpt.living_id=lv.living_id and lpt.living_before_id=lv.living_before_id and lpt.living_master_id=lv.living_master_id')
            ->leftJoin('mb_living_passwrod_ticket_views lptv','lptv.tikcet_id=lpt.tikcet_id and lptv.user_id=:uid',[':uid'=>$user_id])
            ->leftJoin('mb_guess_record gr','gr.living_id=lv.living_id and gr.room_no=lv.room_no and gr.user_id=:uid',[':uid'=>$user_id])
            ->where('lv.status=2 and lv.living_master_id=:lmid',[':lmid'=>$living_master_id])
            ->one();
        return $query;
    }

    /**
     * 热门直播前100条数据写入缓存
     * @param $page
     * @param int $page_size
     * @param $error
     * @return bool
     */
    public static function SetCacheHotLivingList(&$error,&$outInfo,$page=1,$page_size = 100)
    {
        $size = intval($page_size/10);
        $query_result = self::GetHotLivingList($page,$page_size);
        $outInfo = $query_result;
        for($i = $page;$i <= $size;$i++)
        {
            $cache_data = array_slice($query_result,($i -1)*$size,$size);
            $result_result = json_encode($cache_data);
            $cache = \Yii::$app->cache->set('mb_api_hot_living_list_'.$i,$result_result,61);
//            \Yii::getLogger()->log('热门直播列表100条缓存写入失败   $size==:'.$size.'   $i==:'.$i.'    $page_size==:'.$page_size,Logger::LEVEL_ERROR);
            if(!$cache)
            {
                \Yii::getLogger()->log('热门直播列表100条缓存写入失败   query_result_list==:'.var_export($query_result,true),Logger::LEVEL_ERROR);
                $error = '热门直播列表获取失败';
                continue;
            }
        }
        return true;
    }

    /**
     * 热门直播列表10条数据的缓存
     * @param $error
     * @param $outInfo
     * @param int $page
     * @param int $page_size
     * @return bool
     */
    public static function SetCacheHotLivingListOther(&$error,&$outInfo,$page=11,$page_size = 10)
    {
        $query_result = self::GetHotLivingList($page,$page_size);
        $outInfo = $query_result;
        $query_result = json_encode($query_result);
        $cache = \Yii::$app->cache->set('mb_api_hot_living_list_'.$page,$query_result,60);
        if(!$cache)
        {
            \Yii::getLogger()->log('热门直播列表缓存写入失败   query_result_list==:'.var_export($query_result,true),Logger::LEVEL_ERROR);
            $error = '热门直播列表获取失败';
            return false;
        }
        return true;
    }

    /**
     * 热门直播列表10条数据的缓存
     * @param $error
     * @param $outInfo
     * @param $appid
     * @param $living_type  [1,2] 或 1 或 [1,2,3]
     * @param int $page
     * @param int $page_size
     * @return bool
     */
    public static function SetCacheHotLivingListOtherByAppID(&$error,&$outInfo,$appid,$livingType,$user_id,$page=11,$page_size =10)
    {
        $query_result = self::GetHotLivingListByAppIDForLivingType($appid,$livingType,$user_id,$page,$page_size);
        $outInfo = $query_result;
        $query_result = json_encode($query_result);
        if( is_array($appid) )
        {
            $appidmd5 = md5( implode(",",$appid) );
        }

        $key = "mb_api_hot_living_list_".$appidmd5."_".$page;
        $cache = \Yii::$app->cache->set($key,$query_result,30);
        if(!$cache)
        {
            \Yii::getLogger()->log('热门直播列表缓存写入失败   query_result_list==:'.var_export($query_result,true),Logger::LEVEL_ERROR);
            $error = '热门直播列表获取失败';
            return false;
        }
        return true;
    }

    /**
     * 获取贡献榜观众
     * @param $living_id
     * @param int $page_size
     * @return array
     */
    public static function GetLivingAudienceFromContribution($living_id,$page_size=5)
    {
        /*
        SELECT `bc`.`client_no`, `crm`.`owner`, `bc`.`client_id` AS `user_id`, IFNULL(bc.icon_pic,bc.pic) as pic, `crm`.`is_police` FROM `mb_chat_room` `cr`
INNER JOIN `mb_chat_room_member` `crm` ON crm.group_id = cr.room_id
inner JOIN mb_sum_reward_tickets srt on cr.room_master_id=srt.living_master_id and crm.user_id = srt.reward_user_id
INNER JOIN `mb_client` `bc` ON srt.reward_user_id = bc.client_id
WHERE cr.living_id = 989 and crm.owner > 1 ORDER BY `srt`.ticket_num DESC LIMIT 5
         */
        $query = new Query();
        $query->select(['bc.client_no','crm.owner','bc.client_id as user_id','IFNULL(bc.icon_pic,bc.pic) as pic','crm.is_police'])
            ->from('mb_chat_room cr')
            ->innerJoin('mb_chat_room_member crm','crm.group_id = cr.room_id')
            ->innerJoin('mb_sum_reward_tickets srt','cr.room_master_id=srt.living_master_id and crm.user_id = srt.reward_user_id')
            ->innerJoin('mb_client bc','srt.reward_user_id = bc.client_id')
            ->where(['and',['cr.living_id'=>$living_id],'crm.owner > 1'])
            ->orderBy('`srt`.ticket_num DESC')
            ->limit($page_size);
        return $query->all();
    }

    /**
     * 获取观众列表
     * @param $page
     * @param $living_id
     * @param int $page_size
     * @return array
     */
    public static function GetLivingAudience($page,$living_id,$page_size = 5)
    {
        $query = (new Query())
            ->select(['bc.client_no','crm.owner','bc.client_id as user_id','IFNULL(bc.icon_pic,bc.pic) as pic','crm.is_police'])
            ->from('mb_chat_room cr')
            ->innerJoin('mb_chat_room_member crm','crm.group_id = cr.room_id')
            ->innerJoin('mb_client bc','crm.user_id = bc.client_id')
            ->where('cr.living_id = :lid and crm.status  = 1 and crm.owner > 1',[':lid'=>$living_id])
            ->orderBy('crm.create_time desc')
            ->limit($page_size)
            ->offset(($page - 1)* $page_size)
            ->all();

        return $query;
    }

    /**
     * 获取所有热门直播
     */
    public static function GetLivingList(){
        $query = new Query();
        $living_info = $query->select(['c.client_no','l.living_id','if(l.status=2,1,0) as flag','l.living_master_id','l.pull_rtmp_url','l.pull_hls_url','l.push_url','pull_http_url','c.main_pic','c.pic','p.person_count'])
            ->from('mb_living_hot h')
            ->innerJoin('mb_living l','h.living_id=l.living_id')
            ->innerJoin('mb_client c','c.client_id=l.living_master_id')
            ->innerJoin('mb_living_personnum p','p.living_id=l.living_id')
            ->where('l.status=2')
            ->all();
        return $living_info;
    }
} 