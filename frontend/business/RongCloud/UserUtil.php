<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/14
 * Time: 14:09
 */

namespace frontend\business\RongCloud;

use common\models\UserToken;
use yii\db\Query;

class UserUtil {

    /**
     * 融云 -- 获取用户token
     * @param $userId
     * @param $rstData
     * @param $error
     * @return bool
     */
    public static function getUserToken($userId, &$rstData, &$error)
    {
        // TODO: 业务逻辑层
        $User = self::selUserToken($userId);
        $rstData['token'] = $User['token'];
        $User['nick_name'] = empty($User['nick_name']) ? ' ':$User['nick_name'];
        $User['pic'] = empty($User['pic']) ? ' ' : $User['pic'];
        if(empty($User['token']))
        {
            // TODO: 融云协议层
            $RongCloud = \Yii::$app->im->User();
            $rst = $RongCloud->getToken($userId, $User['nick_name'], $User['pic']);
            if(!$rst)
            {
                $error = $RongCloud->getErrorMessage();
                return false;
            }
            $rstData['user_id'] = $User['user_id'];
            $rstData['token'] = $rst['token'];

            // TODO: 修改数据库信息(保存用户融云token)
            if(!self::saveUserToken($rstData, $error))
            {
                return false;
            }
            unset($rstData['user_id']);
        }

        return true;
    }

    /**
     * TODO:查询用户token
     * @param $userId
     * @return array|bool
     */
    public static function selUserToken($userId)
    {
        $query = (new Query())
            ->select(['client_id as user_id','nick_name','pic','ifnull(token,\'\') as token'])
            ->from('mb_client c')
            ->where('client_id =:ud',[':ud'=>$userId])
            ->one();

        return $query;
    }

    /**
     * TODO:保存用户融云token
     * @param $rstData
     * @param $error
     * @return bool
     */
    public static function saveUserToken($rstData, &$error)
    {
        $sql = 'update mb_client set token = :token WHERE client_id = :ud';

        $rst = \Yii::$app->db->createCommand($sql,[
            ':token'=>$rstData['token'],
            ':ud'=>$rstData['user_id'],
        ])->execute();

        if($rst <= 0)
        {
            $error = ' 保存用户Im token信息失败';
            \Yii::error($error.' '.\Yii::$app->db->createCommand($sql,[
                    ':token'=>$rstData['token'],
                    ':ud'=>$rstData['user_id'],
                ])->rawSql);
            return false;
        }

        return true;
    }

    /**
     * TODO:刷新用户融云方法
     * @param $userId
     * @param $error
     * @return bool
     */
    public static function refreshUserInfo($data, &$error)
    {
        // TODO: 融云协议层
        $files = ['userId','nick_name','pic'];
        for($i = 0; $i < 3; $i++ ){
            if(empty($data[$files[$i]])){
                $data[$files[$i]] = ' ';
            }
        }
        $RongCloud = \Yii::$app->im->User();
        $rst = $RongCloud->refresh($data['userId'], $data['nick_name'], $data['pic']);
        if(!$rst)
        {
            $error = $RongCloud->getErrorMessage();
            return false;
        }
        return true;
    }
} 