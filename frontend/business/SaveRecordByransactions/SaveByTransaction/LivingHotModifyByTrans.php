<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 17:50
 */

namespace frontend\business\SaveRecordByransactions\SaveByTransaction;


use common\models\LivingHot;
use frontend\business\SaveRecordByransactions\ISaveForTransaction;
use yii\base\Exception;

/**
 * 直播热度修改
 * Class ExperienceModifyByTrans
 * @package frontend\business\SaveRecordByransactions\SaveByTransaction
 */
class LivingHotModifyByTrans implements ISaveForTransaction
{
    private  $livingHot = null;
    private  $extend_params=[];

    /**
     * @param $record
     * @param array $extend_params
     * @throws Exception
     */
    public function __construct($record,$extend_params=[])
    {
        $this->livingId = $record;
        $this->extend_params = $extend_params;
    }

    function SaveRecordForTransaction(&$error,&$outInfo)
    {
        /*if(!($this->livingHot instanceof LivingHot))
        {
            $error = '不是直播在线人数记录';
            return false;
        }*/
/*
 热门排名根据直播间当前所得分数高低排名：（y：分数，a：当前直播间本次直播所得票（礼物）数，b：当前直播间观众人数， c：点赞数）
y=40a+30b+c
 */
        $sql = 'update mb_living_hot set hot_num= 40 * ifnull((select tickets_num from mb_living_tickets where living_id=:li1 limit 1),0) +
30 * ifnull((select person_count from mb_living_personnum where living_id=:li2 limit 1),0) +
ifnull((select goods_num from mb_living_goods where living_id=:li3 limit 1),0)
where living_id=:li';

        $rst = \Yii::$app->db->createCommand($sql,
            [
                ':li'=>$this->livingId,
                ':li1'=>$this->livingId,
                ':li2'=>$this->livingId,
                ':li3'=>$this->livingId
            ])->execute();
        if($rst <= 0)
        {
            //不能以此判断失败，只要sql执行正常即可
            //throw new Exception('更新热度失败');
        }
        return true;
    }
} 