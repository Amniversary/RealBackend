<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/7/19
 * Time: 13:10
 */

namespace frontend\business;


use common\models\ClientLivingParameters;
use common\models\ClientQiniu;
use yii\db\Query;

class ClientQiNiuUtil
{
    /**
     * 根据七牛id获取客户信息
     * @param $client_id
     */
     public static function GetQiNiuInfoByClientId($client_id)
     {
            return ClientQiniu::findOne(['user_id'=>$client_id]);
     }

    /**
     * 根据参数id 获取用户信息
     * @param $relate_id
     * @return null|static
     */
    public static function GetClientLivingParams($relate_id)
    {
        //根据条件查询数据库里的单个行
        return ClientLivingParameters::findOne(['relate_id'=>$relate_id]);
    }

    public static function GetClientNoLivingParams($relate_id)
    {
        $query = (new Query())
            ->select(['client_no'])
            ->from('mb_client bc')
            ->innerJoin('mb_client_living_parameters clp','bc.client_id = clp.user_id')
            ->where('relate_id=:red',[':red' => $relate_id])
            ->one();
        return $query;
    }

    public static function GetLivingParameters()
    {
        $test = (new Query())
            ->select(['quality_id','quality'])
            ->from('mb_living_parameters')
            ->all();
        $tests = [];
        foreach($test as $q)
        {
            $tests[$q['quality_id']] = $q['quality'];
        }

        return $tests;
    }
}