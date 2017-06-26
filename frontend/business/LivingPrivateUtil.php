<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/17
 * Time: 16:31
 */

namespace frontend\business;

use common\models\LivingPrivate;
use yii\db\Query;
use yii\log\Logger;

/**
 * Class LivingPrivateUtil
 * @package frontend\business
 */
class LivingPrivateUtil {
    /**
     * 通过ID得到私密直播信息
     * @param $private_id
     * @return null|static
     */
    public static function GetLivingPrivateById($private_id)
    {
        return LivingPrivate::findOne(['private_id' => $private_id]);
    }

    /**
     * 通过living_master_id得到私密直播信息
     * @param $private_id
     * @return null|static
     */
    public static function GetLivingPrivateByLivingMasterId($living_master_id)
    {
        return LivingPrivate::findOne(['living_master_id' => $living_master_id]);
    }

    /**
     * 私密直播验证观众信息
     * @param $room_no
     * @param $user_id
     * @param $error
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function CheckPrivatePassword($room_no,$user_id,&$error)
    {
        $living_private_info = LivingPrivate::findOne(['room_no' => $room_no]);
        if(!isset($living_private_info) || empty($living_private_info))
        {
            $error = '直播信息不存在';
            return false;
        }

        $InsertSql = 'insert ignore into mb_living_private_views (private_id,user_id) VALUES (:pid,:uid)';
        $res_insert = \Yii::$app->db->createCommand($InsertSql,[
            ':pid' => intval($living_private_info->private_id),
            ':uid' => intval($user_id),
        ])->execute();
//        $cache_key = 'living_'.$living_id.'_before_'.$living_private_info->living_before_id.'_user_'.$user_id;
//        $cache = \Yii::$app->cache->set($cache_key,1,3600*24);
//        if(!$cache)
//        {
//            $error = '私密直播验证缓存写入失败';
//            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
//            return false;
//        }
        return true;
    }

    /**
     * 删除私密直播观众信息
     * @param $living_id
     * @param $living_before_id
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function DeleteLivingPrivate($living_id,$living_before_id,&$error)
    {
        $living_private_info = LivingPrivate::findOne(['living_id' => $living_id,'living_before_id' => $living_before_id]);
        if(!isset($living_private_info) || empty($living_private_info))
        {
            $error = '直播信息不存在';
            return false;
        }
        $private_views_info = (new Query())
            ->select(['views_id'])
            ->from('mb_living_private_views')
            ->where('private_id=:pid',[':pid' => intval($living_private_info->private_id)])
            ->one();
        if(!isset($private_views_info) || empty($private_views_info))
        {
//            \Yii::getLogger()->log('私密直播观众表信息表无数据',Logger::LEVEL_ERROR);
            return true;
        }

            $sql = 'delete from mb_living_private_views where private_id=:pid';
            $del_info = \Yii::$app->db->createCommand($sql,[':pid' => intval($living_private_info->private_id)])->execute();
            if($del_info <= 0)
            {
                $error = '私密直播观众信息删除失败';
                \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            }


        return true;
    }
} 