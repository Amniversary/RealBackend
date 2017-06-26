<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/8
 * Time: 20:42
 */

namespace common\components;


use common\models\Client;

class UserNameUtil
{
    /**
     * 随机获取客户名称
     */
    public static function GetClientUserName()
    {
/*        $nameList = require(__DIR__.'/../config/ClientList.php');
        $len = count($nameList);
        $randIndex = rand(0,$len -1);*/
        $user_name = 'mw_'.WaterNumUtil::GenWaterNum('RegisterMeiYuanUser',false,false,'2016-03-23',4);
        //return $nameList[$randIndex];
        return $user_name;
    }

    /**
     * TODO: 获取用户头像信息
     * @param $userId
     */
    public static function getUserPic($userId)
    {
        $ac = Client::findOne(['and',['client_id'=>$userId]]);
        $pic = $ac['icon_pic'];
        if(!isset($pic)||
            empty($pic))
        {
            $pic = $ac['main_pic'];
            if(!isset($pic) ||
                empty($pic))
            {
                $pic = $ac['pic'];
            }
        }

        return $pic;
    }
} 