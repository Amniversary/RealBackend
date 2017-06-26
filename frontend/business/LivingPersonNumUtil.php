<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/4/28
 * Time: 21:59
 */

namespace frontend\business;


use common\models\LivingPersonnum;

class LivingPersonNumUtil
{
    /**
     * 根据id获取记录信息
     * @param $living_id
     */
    public static function GetRecordByLivingId($living_id)
    {
        return LivingPersonnum::findOne(['living_id'=>$living_id]);
    }
} 