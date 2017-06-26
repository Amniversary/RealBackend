<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/3/14
 * Time: 15:39
 */

namespace frontend\business;


use common\models\PersonalNewStatistic;
use yii\log\Logger;

class PersonalNewStatisticUtil
{
    /**
     * 更新个人动态信息
     * @param $wish
     * @param $relate_user_id
     * @param $content
     * @param $type  1 打赏  2 评论  3 发布愿望
     */
    public static function UpdatePersonalNewInfo($wish, $relate_user_id, $content,$talk_to_user_id=null,$type=1)
    {
        if(empty($content))
        {
            return;
        }
        if(empty($talk_to_user_id))
        {
            $talk_to_user_id = null;
        }
        $sql = 'update my_personal_new_statistic set modify_time=now(),relate_user_id=:ruid,content=:cnt,new_type=:typ , talk_to_user_id=:tuid where user_id=:uid';
        $params = [
            ':ruid'=>$relate_user_id,
            ':cnt'=>$content,
            ':typ'=>$type,
            ':tuid'=>$talk_to_user_id,
            ':uid'=>$wish->publish_user_id
        ];
        return (\Yii::$app->db->createCommand($sql,$params)->execute() > 0);
    }

    /**
     * 新增个人动态记录，注册时使用
     * @param $user_id
     */
    public static function CreatePersonalNewInfo($user_id,&$error)
    {
        $model = new PersonalNewStatistic();
        $model->user_id = $user_id;
        if(!$model->save())
        {
            $error = '创建个人动态信息失败';
            \Yii::getLogger()->log($error.' '.var_export($model->getErrors(),true), Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 获取新模型
     * @param $user_id
     */
    public static function GetNewModel($user_id)
    {
        $model = new PersonalNewStatistic();
        $model->user_id = $user_id;
        $model->content = '';
        $model->relate_user_id = $user_id;
        $model->modify_time = date('Y-m-d H:i:s');
        $model->new_type = 3;
        return $model;
    }
} 