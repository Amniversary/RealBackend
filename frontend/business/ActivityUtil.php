<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 16:31
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\ActivityChance;
use common\models\ActivityInfo;
use common\models\ActivityShareInfo;
use common\models\Advertise;
use common\models\LuckyDrawRecord;
use yii\db\Query;
use yii\log\Logger;

class ActivityUtil {
    /**
     * 获得活动url
     * @param string $activity_id
     *
     */
    public static function GetActivityUrl($activity_id)
    {
        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $rand_str.=$strPol[rand(0,$max)];
        }

        $time = time();
        $params= [
            'activity_id' => $activity_id,
            'rand_str' => $rand_str,
            'time' => $time
        ];

        $sign = self::GetActivitySign($params);
        $filename = self::GetTempFlieNameByActivityID($activity_id);
        //$url = 'http://'.$_SERVER['HTTP_HOST'].'/mibo/activities/template.html'."?activity_id={$params['activity_id']}&rand_str={$rand_str}&time={$time}&sign={$sign}";
        $url = 'http://'.\Yii::$app->params['http_host'].'/mibo/activities/'.$filename."?activity_id={$params['activity_id']}&rand_str={$rand_str}&time={$time}&sign={$sign}";
        return $url;
    }

    /**
     * 得到前端签名sign
     * @param array $params
     * @return string
     *
     */
    public static function GetActivitySign($params)
    {
        ksort($params);
        $token = \Yii::$app->params['activity_key'];
        $str = '';
        foreach($params as $key=>$v){
            $key = strtolower($key);
            $str .= $key.'='.$v.'&';
        }
        $str .= 'key='.$token;
        //\Yii::getLogger()->log($str.' sign:'.sha1($str), Logger::LEVEL_ERROR);
        return sha1($str);
    }
    /**
     * 根据活动ID获取主播活动——主播排行榜数据
     * @param int $activity_id
     * @return array
     */
    public static function GetScoreBoardByActivityID($activity_id)
    {
        $query = new Query();
        $rank_info = \Yii::$app->cache->get('score_board'.$activity_id);
        if($rank_info == false)
        {
            $lock = new PhpLock('activity_score_board'.$activity_id);
            $lock->lock();
            $rank_info = \Yii::$app->cache->get('score_board'.$activity_id);
            if($rank_info == false)
            {
                $rank_info = $query->select(['l.living_master_id', 'l.total_integral', 'c.nick_name', 'IFNULL(c.icon_pic,c.pic) as pic', 'c.sign_name','c.sex', 'c.client_no'])
                    ->from('mb_living_master_score_board l')
                    ->innerJoin('mb_client c','c.client_id=l.living_master_id')
                    ->where('l.activity_id=:cid',[':cid'=>$activity_id])
                    ->orderBy('l.total_integral desc')
                    ->limit(10)
                    ->all();

                //将排行榜信息存入缓存，有效期300s
                if(!empty($rank_info))
                {
                    \Yii::$app->cache->set('score_board'.$activity_id, $rank_info, 300);
                }

            }
            $lock->unlock();
        }
        return $rank_info;
    }

    /**
     * 根据活动ID获取用户活动——土豪排行榜数据
     * @param int $activity_id
     * @return array
     */
    public static function GetUserScoreBoardByActivityID($activity_id)
    {
        $query = new Query();
        $rank_info = \Yii::$app->cache->get('user_score_board'.$activity_id);
        if($rank_info == false)
        {
            $lock = new PhpLock('activity_user_score_board'.$activity_id);
            $lock->lock();
            $rank_info = \Yii::$app->cache->get('score_board'.$activity_id);
            if($rank_info == false)
            {
                $rank_info = $query->select(['l.send_user_id', 'l.total_integral', 'c.nick_name', 'IFNULL(c.icon_pic,c.pic) as pic', 'c.sign_name','c.sex', 'c.client_no'])
                    ->from('mb_living_user_score_board l')
                    ->innerJoin('mb_client c','c.client_id=l.send_user_id')
                    ->where('l.activity_id=:cid',[':cid'=>$activity_id])
                    ->orderBy('l.total_integral desc')
                    ->limit(10)
                    ->all();

                //将排行榜信息存入缓存，有效期300s
                if(!empty($rank_info))
                {
                    \Yii::$app->cache->set('user_score_board'.$activity_id, $rank_info, 300);
                }

            }
            $lock->unlock();
        }
        return $rank_info;
    }

    /**
     * 根据活动ID获取活动模板文件名
     * @param int $activity_id
     * @return string
     */
    public static function GetTempFlieNameByActivityID($activity_id)
    {
        $query = new Query();
        $template_info = $query->select(['ag.template_id', 'at.file_name'])
            ->from('mb_activity_giftscore ag')
            ->leftJoin('mb_activity_template at','ag.template_id=at.template_id')
            ->where('ag.activity_id=:cid',[':cid'=>$activity_id])
            ->one();
        return $template_info['file_name'];
    }
    /**
     * 获得蜜播活动url
     * @param string $activity_id
     *
     */
    public static function GetMBActivityUrl($activity_id, $ext_params=[], $activity_dir='activities/')
    {
        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $rand_str.=$strPol[rand(0,$max)];
        }

        $time = time();
        $params= [
            'activity_id' => $activity_id,
            'rand_str' => $rand_str,
            'time' => $time
        ];
        if(!empty($ext_params)){
            $params['unique_no'] = $ext_params['unique_no'];
        }
        $sign = self::GetActivitySign($params);
        $filename = self::GetTempFlieNameByMBActivityID($activity_id);
        $str = '';


        foreach($ext_params as $key => $value){
            $str = $str.'&'.$key.'='.$value;
        }

        //文件夹activities
        //zff的活动页面是放在activities下面的

        $url = 'http://'.\Yii::$app->params['http_host'].'/mibo/'.$activity_dir.$filename."?activity_id={$params['activity_id']}&rand_str={$rand_str}&time={$time}&sign={$sign}".$str;
        return $url;
    }
    /**
     * 根据活动ID获取蜜播活动模板文件名
     * @param int $activity_id
     * @return string
     */
    public static function GetTempFlieNameByMBActivityID($activity_id)
    {
        $query = new Query();
        $template_info = $query->select(['a.template_id', 'at.file_name'])
            ->from('mb_activity_info a')
            ->innerJoin('mb_activity_template at','a.template_id=at.template_id')
            ->where('a.activity_id=:cid',[':cid'=>$activity_id])
            ->one();
        return $template_info['file_name'];
    }


    /**
     * 根据活动id 获取活动信息记录
     * @param $activity_id
     * @return null|static
     */
    public static function GetActivityInfoById($activity_id)
    {
        return ActivityInfo::findOne(['activity_id'=>$activity_id]);
    }


    /**
     * 根据活动id 和 用户id 获取抽奖记录
     * @param $activity_id
     * @param $user_id
     * @return null|static
     */
    public static function GetActivityUserChance($activity_id,$user_id)
    {
        return ActivityChance::findOne(['activity_id'=>$activity_id,'user_id'=>$user_id]);
    }


    /**
     * 根据活动id 获取奖品信息
     * @param $activity_id
     * @return array
     */
    public static function GetActivityPrizeById($activity_id)
    {
        $condition = 'activity_id = :ad';
        $query = (new Query())
            ->select(['prize_id','activity_id','grade','gift_name','pic','number','unit','rate','type'])
            ->from('mb_activity_prize')
            ->where($condition,[':ad'=>$activity_id])
            ->orderBy('order_no asc')
            ->all();


        return $query;
    }

    /**
     * 从缓存中获取奖品信息 没有则从数据库中获取
     * @param $activity_id
     * @param bool $reflash
     * @return array|mixed
     */
    public static function GetActivityPrizeInfo($activity_id,$reflash = false)
    {
        if($reflash)
        {
            $rst = self::GetActivityPrizeById($activity_id);
            $pStr = serialize($rst);
            \Yii::$app->cache->set('get_prize_info',$pStr);
        }
        else
        {
            $cnt = \Yii::$app->cache->get('get_prize_info');
            if($cnt == false)
            {
                $lock = new PhpLock('get_activity_prize');
                $lock->lock();
                $cnt = \Yii::$app->cache->get('get_prize_info');
                if($cnt == false)
                {
                    $rst = self::GetActivityPrizeById($activity_id);
                    $pStr = serialize($rst);
                    \Yii::$app->cache->set('get_prize_info',$pStr);
                }
                else
                {
                    $rst = unserialize($cnt);
                }
                $lock->unlock();
            }
            else
            {
                $rst = unserialize($cnt);
            }
        }

        return $rst;
    }

    /**
     * 通过活动类型得到信息
     * @param $type
     * @return null|static
     */
    public static function GetActivityByType($type)
    {
        return ActivityInfo::findOne(['type' => $type]);
    }

    /**
     * 通过类型获得信息
     * @param $type
     * @return array
     */
    public static function GetActivityShareInfoByType($type)
    {
        $query = (new Query())
            ->select(['share_id','type','title','content','url','pic'])
            ->from('mb_activity_share_info')
            ->where('type=:ty',[':ty'=>$type])
            ->one();
        return $query;
    }

    /**
     * 生成抽奖记录模型
     * @param $data
     * @return LuckyDrawRecord
     */
    public static function GetActivityRecordModel($data)
    {
        $model = new LuckyDrawRecord();
        $model->attributes = $data;

        return $model;
    }

    /**
     * 根据用户id 活动id 获取用户中奖纪录信息
     * @param $activity_id
     * @param $user_id
     * @return array
     */
    public static function GetWinningInfo($activity_id,$user_id)
    {
        $condition = 'activity_id = :ad and user_id = :ud';
        $query = (new Query())
            ->select(['nick_name','IFNULL(icon_pic,pic) as pic','prize_name','ldr.create_time'])
            ->from('mb_client c')
            ->innerJoin('mb_lucky_draw_record ldr','c.client_id = ldr.user_id')
            ->where($condition,[':ad'=>$activity_id,':ud'=>$user_id])
            ->orderBy('create_time desc')
            ->all();

        return $query;
    }

    /**
     * 根据中奖记录id 获取中奖记录信息
     * @param $record_id
     * @return null|static
     */
    public static function GetPrizeRecordById($record_id)
    {
        return LuckyDrawRecord::findOne(['record_id'=>$record_id]);
    }

    /**
     * 保存中奖记录信息
     * @param $record
     * @param $error
     * @return bool
     */
    public static function SavePrizeRecord($record, &$error)
    {
        if(!($record instanceof LuckyDrawRecord))
        {
            $error = '不是中奖记录对象';
            return false;
        }

        if(!$record->save())
        {
            $error = '保存中奖记录信息失败';
            \Yii::getLogger()->log($error. ' :'.var_export($record->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 获取广告页
     * @param $app_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function GetAdvertisementList($app_id)
    {
        $current_time = date('Y-m-d H:i:s');
        $adList = Advertise::find()
            ->asArray()
            ->select(['app_id','img_url','width','height','duration'])
            ->andFilterWhere(['app_id'=>$app_id,'status'=>1])
            ->andFilterWhere(['<=','effe_time',$current_time])
            ->andFilterWhere(['>=','end_time',$current_time])
            ->orderBy('ordering DESC')
            ->one();

        return $adList;
    }
} 