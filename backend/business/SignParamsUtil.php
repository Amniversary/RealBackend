<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/21
 * Time: 下午2:29
 */

namespace backend\business;


use common\models\AuthSign;
use common\models\SignParams;

class SignParamsUtil
{
    public static $ary = ['周日','周一','周二','周三','周四','周五','周六'];

    public static function GetSignDayParams($app_id)
    {
        $signParams = SignParams::find()->select(['day_id'])->where(['app_id'=>$app_id, 'type'=>0])->all();
        foreach ($signParams as $param) {
            unset(self::$ary[$param->day_id]);
        }
        return self::$ary;
    }

    public static function GetBatchSignDayParams()
    {
        $signParams = SignParams::find()->select(['day_id'])->where(['type'=>1])->all();
        foreach ($signParams as $item) {
            unset(self::$ary[$item->day_id]);
        }
        return self::$ary;
    }

    /**
     * @param $appId
     * @param $user_id
     * @return null|AuthSign
     */

    public static function  getUserSignNum($appId, $user_id)
    {
        return AuthSign::findOne(['app_id'=>$appId, 'user_id'=> $user_id]);
    }
}