<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-4-29
 * Time: 下午5:00
 */

namespace frontend\business;


use backend\business\UserUtil;

use common\models\Gift;
use common\models\GiftScore;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CheckRecordSaveForReward;
use yii\db\Query;
use yii\log\Logger;

class GiftUtil
{
    /**
     * 根据礼物ID获取礼物详情
     * @param $gift_id
     * @return Gift|null
     */
    public static function GetGiftById($gift_id){
        return Gift::findOne(['gift_id'=>$gift_id]);
    }

    /**
     * 根据礼物积分Id获取有积分的礼物
     */
    public static function GetGiftScoreById($record_id)
    {
        return GiftScore::findOne(['record_id'=>$record_id]);
    }

    /**
     * 保存礼物积分
     * @param $score
     * @param $error
     * @return bool
     */
    public static function SaveGiftScore($score,&$error)
    {
        if(!($score instanceof GiftScore))
        {
            $error = '不是礼物积分记录';
            return false;
        }
        if(!$score->save())
        {
            $error = $score->getFirstError('score');//'礼物积分记录保存失败!';
            \Yii::getLogger()->log($error.' :'.var_export($score->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }

        return true;
    }

    /**
     * 获取礼物类型
     * @return array
     */
    public static function GetGiftScoreType()
    {
        $query = (new Query())
            ->select(['gift_id','gift_name'])
            ->from('mb_gift')
            ->all();
        $test = [];
        foreach($query as $q)
        {
            $test[$q['gift_id']] = $q['gift_name'];
        }

        return $test;
    }
}