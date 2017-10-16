<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/10
 * Time: 下午1:57
 */

namespace frontend\business;


use common\models\CAppinfo;

class AppInfoUtil
{
    /**
     * 根据AppId获取小程序信息
     * @param $appId
     * @return null|CAppinfo
     */
    public static function GetAppInfo($appId)
    {
        return CAppinfo::findOne(['appid' => $appId]);
    }

    /**
     * 获取用户信息
     * @param $appId
     * @param $open_id
     * @return array|false
     */
    public static function GetUserByAppId($appId, $open_id)
    {
        $sql = 'select * from cClient' . $appId . ' where open_id = :openid;';
        return \Yii::$app->db->createCommand($sql, [':openid' => $open_id])->queryOne();
    }

    /**
     * 根据Appid 直接获取用户信息
     * @param $appId
     * @param $openId
     * @return array|false
     */
    public static function GetAppClientInfo($appId, $openId)
    {
        $AppInfo = self::GetAppInfo($appId);
        return self::GetUserByAppId($AppInfo->id, $openId);
    }
}