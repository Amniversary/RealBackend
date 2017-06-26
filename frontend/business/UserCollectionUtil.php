<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-27
 * Time: 下午11:36
 */

namespace frontend\business;


use common\models\UserCollection;
use yii\base\Exception;
use yii\log\Logger;

class UserCollectionUtil
{

    /**
     * 获取收藏列表
     * @param $collection_type
     * @param $user_id
     */
    public static function GetCollectionDataList($flag,$start_id,$collection_type, $user_id)
    {
        $dataList = null;
        switch($collection_type)
        {
            case '1':
                $dataList = self::GetCollectionWishList($flag, $start_id, $user_id);
                break;
            default:
                $dataList = self::GetCollectionPersonal($flag,$start_id,$user_id);
                break;
        }
        return $dataList;
    }

    /**
     * 获取愿望列表收藏
     * @param $user_id
     */
    public static function GetCollectionWishList($flag,$start_id,$user_id)
    {
        $sql = '
SELECT user_collection_id as row_id,is_finish,finish_status, wish_id,publish_user_id,publish_user_name,wish_name,discribtion,wish_type_id,wish_money,
pic1,pic2,pic3,pic4,pic5,pic6,end_date,back_type,back_dis,ready_reward_money,red_packets_money,
reward_num,collect_num,view_num,comment_num,sex,\'--\' as distance,pic as user_pic
FROM `my_user_collection`  uc
inner join my_wish wh on uc.other_id = wh.wish_id and uc.collection_type = 1 and uc.user_id=:uid
inner join my_account_info ai on wh.publish_user_id = ai.account_id
        ';
        $condition = ' where 1 = 1';
        $params = [':uid'=>$user_id];
        switch($flag)
        {
            case 'up':
                $condition .=  ' and user_collection_id>:sid';
                $params[':sid']=$start_id;
                break;
            case 'down':
                $condition .=  ' and user_collection_id<:sid';
                $params[':sid']=$start_id;
                break;
            default:

                break;
        }
        $sql .= $condition . ' order by uc.user_collection_id desc limit 10;';
        //\Yii::getLogger()->log('flag'.$flag, Logger::LEVEL_ERROR);
        //\Yii::getLogger()->log($sql, Logger::LEVEL_ERROR);
        $dataList = \Yii::$app->db->createCommand($sql, $params)->queryAll();
        $rst = [];
        foreach($dataList as $dataOne)
        {
            $leftDays = intval((strtotime($dataOne['end_date']) - strtotime(date('Y-m-d'))) / (3600 * 24));
            if($dataOne['is_finish'] == 2)
            {
                if($dataOne['finish_status'] != 1 || $leftDays < 0)
                {
                    $leftDays = '已结束';
                }
            }
            else if($dataOne['finish_status'] != 1 || $leftDays < 0)
            {
                $leftDays = '已经过期';
            }
            if($leftDays === 0)
            {
                $leftDays = 1;
            }
            unset($dataOne['is_finish']);
            unset($dataOne['finish_status']);
            $dataOne['wish_over_left_days'] = strval($leftDays);
            $dataOne['reward_max_list'] = WishUtil::GetRewardMaxList($dataOne['wish_id']);
            $rst[] = $dataOne;
        }
        //\Yii::getLogger()->log(var_export($rst,true), Logger::LEVEL_ERROR);
        return $rst;
    }

    /**
     * 获取个人主页收藏
     * @param $user_id
     */
    public static function GetCollectionPersonal($flag,$start_id,$user_id)
    {
        $sql = 'SELECT user_collection_id as row_id,ai.account_id as user_id,ai.pic,ai.nick_name,ai.sex,ai.sign_name  FROM `my_user_collection`  uc inner join my_account_info ai on uc.other_id = ai.account_id and collection_type = 2 and uc.user_id=:uid';
        $condition = ' where 1 = 1';
        $params = [':uid'=>$user_id];
        switch($flag)
        {
            case 'up':
                $condition .=  ' and user_collection_id>:sid';
                $params[':sid']=$start_id;
                break;
            case 'down':
                $condition .=  ' and user_collection_id<:sid';
                $params[':sid']=$start_id;
                break;
            default:

                break;
        }
        $sql .= $condition . ' order by uc.user_collection_id desc limit 10;';
        $dataList = \Yii::$app->db->createCommand($sql, $params)->queryAll();
        return $dataList;
        /*
user_id
pic
nick_name
sex
sign_name
         */
    }

    /**
     * 新增收藏
     * @param $passParams
     * @param $user_id
     * @param $error
     */
    public static function AddCollection($passParams,$user_id,&$error)
    {
        $error = '';
        $collection_type = $passParams['collection_type'];
        $other_id = $passParams['other_id'];
        $model=self::FindCollectionOnlyOne($user_id,$collection_type,$other_id);
        if(isset($model))
        {
            $error = '该收藏已经存在';
            return false;
        }
        $passParams['user_id'] = $user_id;
        $passParams['create_time'] = date('Y-m-d H:i:s');
        $model = new UserCollection();
        $model->attributes = $passParams;
        if(!$model->save())
        {
            //$error = '保存收藏信息失败';
            \Yii::getLogger()->log(var_export($model->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('保存收藏信息失败');
        }
        return true;
    }

    /**
     * 查找收藏
     * @param $user_id
     * @param $collection_type
     * @param $other_id
     * @return null|static
     */
    public static function FindCollectionOnlyOne($user_id,$collection_type,$other_id)
    {
        return UserCollection::findOne([
            'user_id'=>$user_id,
            'collection_type'=>$collection_type,
            'other_id'=>$other_id
        ]);
    }

    /**
     * 删除个人收藏
     * @param $passParams
     * @param $user_id
     * @param $error
     * @return bool
     */
    public static function CancelCollection($passParams,$user_id,&$error)
    {
        $collection_type = $passParams['collection_type'];
        $other_id = $passParams['other_id'];
        $model=self::FindCollectionOnlyOne($user_id,$collection_type,$other_id);
        if(!isset($model))
        {
            $error = '找不到个人收藏';
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }
        if(!$model->delete())
        {
            $error = '删除失败';
            \Yii::getLogger()->log(var_export($model->getErrors(), true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }
} 