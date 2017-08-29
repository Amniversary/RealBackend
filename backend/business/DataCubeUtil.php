<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/26
 * Time: 下午2:47
 */

namespace backend\business;


use common\components\UsualFunForNetWorkHelper;

class DataCubeUtil
{
    /**
     * 获取图文群发总数据
     * @param $accessToken
     * @param $day
     * @return mixed
     */
    public static function getArticleTotal($accessToken, $day = 1)
    {
        $url = "https://api.weixin.qq.com/datacube/getarticletotal?access_token=$accessToken";
        $date['begin_date'] = date('Y-m-d', strtotime('-'.$day.' day'));
        $date['end_date'] = date('Y-m-d', strtotime('-'.$day.' day'));
        $json = json_encode($date);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        return $rst;
    }

    /**
     * 获取图文统计数据
     * @param $accessToken
     * @return mixed
     */
    public static function getUserRead($accessToken)
    {
        $url = "https://api.weixin.qq.com/datacube/getuserread?access_token=$accessToken";
        $date['begin_date'] = date('Y-m-d', strtotime('-5 day'));
        $date['end_date'] = date('Y-m-d', strtotime('-5 day'));
        $json = json_encode($date);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        return $rst;
    }

    /**
     * 获取图文群发每日数据
     * @param $accessToken
     * @return mixed
     */
    public static function getArticleSummary($accessToken)
    {
        $url = "https://api.weixin.qq.com/datacube/getarticlesummary?access_token=$accessToken";
        $date['begin_date'] = date('Y-m-d', strtotime('-1 day'));
        $date['end_date'] = date('Y-m-d', strtotime('-1 day'));
        $json = json_encode($date);
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json), true);
        return $rst;
    }
}