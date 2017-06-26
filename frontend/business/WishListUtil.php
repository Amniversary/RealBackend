<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/21
 * Time: 11:11
 */

namespace frontend\business;

use common\components\CaculateUtil;
use yii\db\Query;

class WishListUtil {


    /**
     * 获取推荐愿望信息
     * @param $page
     * @param $longitude
     * @param $latitude
     * @param int $page_size
     * @return array
     */
    public static function GetWishList($page,$longitude,$latitude,$page_size = 5)
    {

        $query = new Query();
        $list = $query->select(['wr.wish_id','wish_name','reward_num','wish_money','ai.nick_name','ai.pic','sex','longitude','latitude','finish_status','end_date','pic1','ifnull(other_id,0) as other_id'])
            ->from('my_wish ws')
            ->innerJoin('my_account_info ai','ws.publish_user_id=ai.account_id')
            ->innerJoin('my_wish_recommend wr','wr.wish_id=ws.wish_id')
            ->leftJoin('my_user_collection uc','ai.account_id=uc.user_id and ws.wish_id=uc.other_id and collection_type = 1 AND ws.publish_user_id=uc.other_id ')
            ->where('ws.status = 1 AND finish_status = 1')
            ->orderBy('order_no desc')
            ->offset(($page - 1) * $page_size)
            ->limit($page_size)
            ->all();

        $wishList = self::WishRemainingDate($list,$longitude,$latitude);

        return $wishList;
    }

    /**
     * 经纬度处理
     * @param $distance
     * @param $longitude
     * @param $latitude
     * @return array
     */
    public static function CountDistance($distance,$longitude,$latitude)
    {
        $lng = array_column($distance,'longitude');
        $ltd = array_column($distance,'latitude');
        $distances = [];
        for($i=0; $i< 5; $i++){
            if(empty($longitude) && empty($latitude)){
                $meter = '--';
            }
            else{
                $meter = CaculateUtil::GetDistance($longitude,$latitude,$lng[$i],$ltd[$i]);
                $meter = $meter >= 1000 ? intval($meter / 1000) .'km':intval($meter).'m';
            }
            $distances[$i] = $meter;
        }
        $out = [];
        $i = 0;
        foreach($distance as $one){
            unset($one['longitude']);
            unset($one['latitude']);
            $one['distance']= $distances[$i++];
            $out[] = $one;
        }
        return $out;
    }

    /**
     * 愿望状态处理
     * @param $distance
     * @param $longitude
     * @param $latitude
     * @return array
     */
    public static function WishRemainingDate($distance,$longitude,$latitude)
    {
        $wishList = self::CountDistance($distance,$longitude,$latitude);
        $status = [];

        foreach($wishList as $time){
            $date = date('Y-m-d');
            switch($time['finish_status'])
            {
                case 2:
                    $time['wish_status'] = '已实现'; break;
                case 4:
                    $time['wish_status'] = '已结束'; break;
                default:
                    $endTime = $time['end_date'];
                    $day = strtotime($endTime) - strtotime($date);
                    $day = $day/3600/24;
                    $time['wish_status'] ='剩余'. $day .'天'; break;
            }
            unset($time['end_date']);
            unset($time['finish_status']);
            $status[] = $time;
        }

        return $status;
    }


    /**
     * 已实现愿望列表
     * @param $page
     * @param $longitude
     * @param $latitude
     * @param int $page_size
     * @return array
     */
    public static function WishFinishList($page,$longitude,$latitude,$page_size = 5)
    {
        $query =new Query();
        $List = $query->select(['ws.wish_id','wish_name','reward_num','wish_money','ai.nick_name','ai.pic','pic1','sex','end_date','longitude','latitude','finish_status','ifnull(other_id,0) as other_id'])
            ->from('my_wish ws')
            ->innerJoin('my_account_info ai','ws.publish_user_id=ai.account_id')
            ->innerJoin('my_hot_order_extend hoe','hoe.wish_id=ws.wish_id')
            ->leftJoin('my_user_collection uc','ai.account_id=uc.user_id AND ws.wish_id=uc.other_id AND collection_type = 1 AND ws.publish_user_id=uc.other_id')
            ->where('finish_status = 2 AND ws.status = 1')
            ->orderBy('hoe.order_no DESC,ws.hot_num DESC')
            ->offset(($page - 1) * $page_size)
            ->limit($page_size)
            ->all();

        $finishList = self::WishRemainingDate($List,$longitude,$latitude);

        return $finishList;
    }

}
