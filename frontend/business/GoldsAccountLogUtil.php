<?php

/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/10/12
 * Time: 16:31
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\GoldsAccountLog;
use common\components\UsualFunForStringHelper;
use yii\db\Query;
use yii\log\Logger;

class GoldsAccountLogUtil {
    
    /*
     * 根据金币帐户的id,及用户id获取金币操作日记的当天的赠送金币记录
     * @param $gold_account_id $user_id
     * @return null|static   GoldsAccountLog model
     */
     public static function GetGoldsAccountLogModelByOneDayOneTime($gold_account_id,$user_id){
          return GoldsAccountLog::find()
                  ->andWhere(['gold_account_id'=>$gold_account_id])
                  ->andWhere(['user_id'=>$user_id])
                  ->andWhere(['operate_type'=>5])
                  ->andWhere(['DATE_FORMAT(create_time,\'%Y-%m-%d\')'=>date('Y-m-d')])
                  ->one();
     }
     
     /*
      * 添加金币日记
      * @param $GoldsAccountLogInfo 为GoldsAccountLog model
      * @return true or false
      */
     public static function AddGoldsAccountLog($model,&$error){
          if(!$model instanceof GoldsAccountLog){
                return false;
          }
          if(!$model->save()){
              $error = $model->getErrors();
              \Yii::getLogger()->log('添加金币日记时发生了错误: '.var_export($error,true),Logger::LEVEL_ERROR);
              return false;
          }
          return TRUE;
     }
     
     /*
      * 获取帐户下的明细
      */
     public static function GetGoldAccountLogList($gold_account_id){
           return GoldsAccountLog::find()
                  ->andWhere(['gold_account_id'=>$gold_account_id])
                  ->all();
     }
}