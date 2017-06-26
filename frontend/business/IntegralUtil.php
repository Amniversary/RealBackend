<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 14:17
 */

namespace frontend\business;


use backend\business\UserUtil;
use common\models\ChangeRecord;
use common\models\IntegralMall;
use yii\log\Logger;


class IntegralUtil
{
    /**
     * 根据记录ID获取到礼品的真实信息
     * @param $record_id
     * @return null|static
     */
    public static function GetGiftMoneyById($record_id)
    {
        return IntegralMall::findOne(['record_id'=>$record_id]);
    }

    /**
     * *根据记录ID获取到交换记录的数据
     * @param $record_id
     * @return null|static
     */
    public static function GetRecordById($record_id)
    {
        return ChangeRecord::findOne(['record_id'=>$record_id]);
    }


    /**
     * 保存修改后的礼品的真实金钱
     * @param $score
     * @param $error
     * @return bool
     */
    public static function SaveGiftMoney($money,&$error)
    {
        if(!($money instanceof IntegralMall))
        {
            $error = '不是礼品的真实金钱';
            return false;
        }
        if(!$money->save())
        {
            $error = $money->getFirstError('score');
            \Yii::getLogger()->log($error.' :'.var_export($money->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


    /**
     * 保存修改后的礼品的真实积分
     * @param $score
     * @param $error
     * @return bool
     */
    public static function SaveGiftIntegral($integral,&$error)
    {
        if(!($integral instanceof IntegralMall))
        {
            $error = '不是礼品的真实积分';
            return false;
        }
        if(!$integral->save())
        {
            $error = $integral->getFirstError('score');
            \Yii::getLogger()->log($error.' :'.var_export($integral->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 保存修改后交换状态
     * @param $money
     * @param $error
     * @return bool
     */
    public static function SaveChangeState($money,&$error)
    {
        if(!($money instanceof ChangeRecord))
        {
            $error = '不是商品交换状态';
            return false;
        }
        if(!$money->save())
        {
            $error = $money->getFirstError('change_state');
            \Yii::getLogger()->log($error.' :'.var_export($money->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }


    /**
     * 保存修改后地址
     * @param $money
     * @param $error
     * @return bool
     */
    public static function SaveAddress($money,&$error)
    {
        if(!($money instanceof ChangeRecord))
        {
            $error = '不是联系地址';
            return false;
        }
        if(!$money->save())
        {
            $error = $money->getFirstError('address');
            \Yii::getLogger()->log($error.' :'.var_export($money->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

}