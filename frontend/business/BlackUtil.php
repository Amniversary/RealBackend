<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/24
 * Time: 13:36
 */

namespace frontend\business;


use common\models\BlackList;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;

class BlackUtil
{
    /**
     * 取消黑名单
     * @param $user_id
     * @param $black_user_id
     * @param $error
     */
    public static function CancelBlack($user_id,$black_user_id,&$error)
    {
        $blackInfo = self::GetBlackOne($user_id,$black_user_id);
        if(!($blackInfo instanceof BlackList))
        {
            $error = '不是黑名单对象，设置失败';
            return false;
        }
        if($blackInfo->hide_msg === 1)
        {
            $error = '用户不在黑名单，无需取消';
            return false;
        }
        $blackInfo->hide_msg = 1;
        if(!$blackInfo->save())
        {
            $error = '取消黑名单失败';
            \Yii::getLogger()->log($error.' :'.var_export($blackInfo->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    /**
     * 获取一个拉黑信息
     * @param $user_id
     * @param $black_user_id
     * @return null|static
     */
    public static function GetBlackOne($user_id, $black_user_id)
    {
        return BlackList::findOne([
            'user_id'=>$user_id,
            'black_user_id'=>$black_user_id
        ]);
    }


    /**
     * 设置黑名单
     * @param $user_id
     * @param $black_user_id
     */
    public static function SetBlack($user_id,$black_user_id,&$error)
    {
        $blackInfo = self::GetBlackOne($user_id,$black_user_id);

        //解除关注关系
        $attention = AttentionUtil::GetFriendOne($user_id,$black_user_id);
        if(isset($attention))
        {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($attention->delete() === false)
                {
                    $error = '解除关注信息失败，系统错误!';
                    \Yii::getLogger()->log($error.' :'.var_export($attention->getErrors(),true),Logger::LEVEL_ERROR);
                    throw new Exception($error);
                }
                //关注数量减少
                $sql = 'update mb_client_active set attention_num = attention_num - 1 where user_id=:uid and attention_num > 0';
                $query = \Yii::$app->db->createCommand($sql,[
                    ':uid'=>$user_id
                ])->execute();
                if($query <= 0)
                {
                    throw new Exception('更新关注数信息失败!');
                }
                $trans->commit();
            }
            catch(Exception $e)
            {
                $trans->rollBack();
                $error = $e->getMessage();
                return false;
            }
        }
        //创建黑名单模型
        if(!isset($blackInfo))
        {
            $model = new BlackList();
            $model->user_id = $user_id;
            $model->black_user_id = $black_user_id;
            $model->hide_msg = 0;

            if(!$model->save())
            {
                $error = '设置黑名单失败';
                \Yii::getLogger()->log($error.' :'.var_export($model->getErrors(),true),Logger::LEVEL_ERROR);
                return false;
            }

            return true;
        }

        if(!($blackInfo instanceof BlackList))
        {
            $error = '不是黑名单对象';
            return false;
        }
        if($blackInfo->hide_msg === 0)
        {
            \Yii::getLogger()->log(var_export($blackInfo->attributes,true).' user_id:'.$user_id,' black_user_id:'.$black_user_id,Logger::LEVEL_ERROR);
            $error = '已经设置成黑名单';
            return false;
        }
        $blackInfo->hide_msg = 0;
        if(!$blackInfo->save())
        {
            $error = '修改黑名单状态失败';
            \Yii::getLogger()->log($error.' :'.var_export($blackInfo->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }


        return true;
    }

    /**
     * 获取黑名单
     * @param $user_id
     * @param $page_no
     * @param $page_size
     */
    public static function GetBlacks($user_id,$page_no,$page_size)
    {
        $offset = ($page_no -1)* $page_size;
        $query = new Query();
        $query->select(['ct.client_id as user_id','ct.nick_name','ct.sex','ct.pic','ct.sign_name'])
            ->from('mb_black_list bl')
            ->innerJoin('mb_client ct','bl.black_user_id = ct.client_id and hide_msg = 0 and bl.user_id=:uid',[':uid'=>$user_id])
            ->orderBy('bl.black_no asc')
            ->offset($offset)
            ->limit($page_size);
        $blacks = $query->all();

        return $blacks;
    }
} 