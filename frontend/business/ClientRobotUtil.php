<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/13
 * Time: 10:58
 */
namespace frontend\business;
use \common\models\ClientRobotinfo;
use yii\log\Logger;

class ClientRobotUtil{
    /**
     * 更具用户id获取机器人信息
     * @param $user_id
     */
    public static function GetClientRobot($user_id)
    {
        $ac = ClientRobotinfo::findOne([
            'and',['user_id'=>$user_id]
        ]);
        return $ac;
    }

    /**
     * @param $clientrobot
     * @param $error
     * @return bool
     */

    public static function SaveClientRobot($clientrobot, &$error)
    {
        if(!($clientrobot instanceof ClientRobotinfo))
        {
            $error = '不是用户记录';
            return false;
        }

        if(!$clientrobot->save())
        {
            $error = '用户记录保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($clientrobot->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    /**
     * 当用户设置机器人数量时将该主播插入到表  mb_client_robotinfo
     */

    public static function AddRobotInfo($user_id){
        $sql = 'insert into mb_client_robotinfo (user_id) VALUE (:id)';

        \yii::$app->db->createCommand($sql,[
            ':id' => $user_id
        ])->execute();
    }
}

