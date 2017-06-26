<?php

/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/10/13
 * Time: 13:31
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\IntegralAccount;
use \common\models\IntegralAccountLog;
use common\components\UsualFunForStringHelper;
use Pheanstalk\Exception;
use yii\db\Query;
use yii\log\Logger;
use yii\db\Transaction;

class IntegralAccountUtil {
    
    /*
     * 根据用户id获取积分帐户信息
     * @return null|static   IntegralAccount model
     */
    public static function GetIntegralAccountModle($user_id){
         return IntegralAccount::findOne(['user_id'=>$user_id]);
    }

    /*
     * 根据用户id,判断用户积分帐户是否存在
     */
    public static function GetIntegralAccountModleIsExists($user_id){
        if( IntegralAccount::findOne(['user_id'=>$user_id]) ){
            return true;
        }
        return  false;
    }

     /*
     * 根据用户id获取积分帐户信息
     * @return null|static   IntegralAccount model
     */
    public static function GetIntegralAccountModleByIntergralId($integral_account_id){
         return IntegralAccount::findOne(['integral_account_id'=>$integral_account_id]);
    }

    /**
     * 根据用户id获取积分帐户信息,返回部分字段
     * @param $user_id
     * @return null|static   IntegralAccount model
     */
    public static function GetIntegralAccountInfoByUserId($user_id){
        return IntegralAccount::find()->asArray()
                ->select(['integral_account_id','integral_account_total','integral_account_spend','integral_account_balance','account_status'])
                ->where(['user_id'=>$user_id])
                ->one();
    }
    
    /*
     * 为新注册的用户开设一个积分帐户
     * @param $user_id
     * @return true or false
     */
    public static function CreateAnIntegralAccountForNewRegisUser($user_id){

        $phpLock = new PhpLock("create_integral_account_for_new_regisuser");
        $phpLock->lock();
        $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);

        $accountSQL = "INSERT INTO mb_integral_account ( user_id,integral_account_total,integral_account_spend,integral_account_balance,rand_str,account_status,create_time ) VALUES ( :user_id,:integral_account_total,:integral_account_spend,:integral_account_balance,:rand_str,:account_status,:create_time )";
        $rand_str = UsualFunForStringHelper::mt_rand_str(40);
        $acctountRetVal = \Yii::$app->db->createCommand($accountSQL,[
            ":user_id"=>$user_id,
            ":integral_account_total"=>0,
            ":integral_account_spend"=>0,
            ":integral_account_balance"=>0,
            ":rand_str"=>$rand_str,
            ":account_status"=>1,
            ":create_time"   =>date('Y-m-d H:i:s')
        ])->execute();

        $accountSQLError = \Yii::$app->db->createCommand($accountSQL,[
            ":user_id"=>$user_id,
            ":integral_account_total"=>0,
            ":integral_account_spend"=>0,
            ":integral_account_balance"=>0,
            ":rand_str"=>$rand_str,
            ":account_status"=>1,
            ":create_time"   =>date('Y-m-d H:i:s')
        ])->rawSql;

        $accountSignSQL = 'update mb_integral_account set 
                sign=MD5(
                  CONCAT(
                  \'integral_account_id=\',integral_account_id,
                  \'&user_id=\',user_id,
                  \'&integral_account_total=\', REPLACE(FORMAT(integral_account_total,2),\',\',\'\'),
                  \'&integral_account_spend=\',REPLACE(FORMAT(integral_account_spend,2),\',\',\'\'),
                  \'&integral_account_balance=\',REPLACE(FORMAT(integral_account_balance,2),\',\',\'\'),
                  \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))
                  where   user_id=:user_id';



        $acctountSignRetVal = \Yii::$app->db->createCommand($accountSignSQL,[
            ':user_id'=>$user_id
        ])->execute();

        $accountSignSQLError = \Yii::$app->db->createCommand($accountSignSQL,[
            ':user_id'=>$user_id])->rawSql;

        if(  $acctountRetVal <=0 && $acctountSignRetVal <=0 ){

            $error = '创建积分帐户时';
            \Yii::getLogger()->log($error.'失败;'.'user_id:['.$user_id.'] sql:'.$accountSQLError,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log($error.'更新签名时;' .'user_id:['.$user_id.'] sql:'.$accountSignSQLError,Logger::LEVEL_ERROR);
            $trans->rollBack();
            $phpLock->unlock();
            return false;
        }

        $error = '创建积分帐户成功';
        \Yii::getLogger()->log('user_id:['.$user_id.']'.$error,Logger::LEVEL_ERROR);
        $trans->commit();
        $phpLock->unlock();
        return TRUE;
    }
    
    /*
     * 添加积分到用户的积分帐户，同时产生帐户变动日记
     * @param $integral_account_id $user_id $device_type $operateType $operateValue
     * @return true or false
     */
    public static function UpdateIntegralAccountToAdd($integral_account_id,$user_id,$device_type,$operateType,$operateValue,&$error){
            $model = IntegralAccount::findOne(['integral_account_id'=>$integral_account_id,'user_id'=>$user_id]);
            if(!$model){
                 $error = "户用id:".$user_id."的积分帐户不存在";
                 \Yii::getLogger()->log("户用id:".$user_id."的积分帐户不存在",Logger::LEVEL_ERROR);
                 return false;
            }
            if(!self::CheckBalance($model, $error)){
                 $error = '签名验证不正确';
                 \Yii::getLogger()->log('签名验证不正确:'.$error,Logger::LEVEL_ERROR);
                 return false;
            }
            $phpLock = new PhpLock("update_integral_account_to_add");
            $phpLock->lock();

            $old_integral_account_balance    = $model->integral_account_balance;

            $accountSQL = 'update mb_integral_account set integral_account_total = integral_account_total + :integral_account_total,
                           integral_account_balance = integral_account_balance + :integral_account_balance, sign=MD5(
                              CONCAT(
                              \'integral_account_id=\',integral_account_id,
                              \'&user_id=\',user_id,
                              \'&integral_account_total=\', REPLACE(FORMAT(integral_account_total,2),\',\',\'\'),
                              \'&integral_account_spend=\',REPLACE(FORMAT(integral_account_spend,2),\',\',\'\'),
                              \'&integral_account_balance=\',REPLACE(FORMAT(integral_account_balance,2),\',\',\'\'),
                              \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))  
                            WHERE  integral_account_id=:integral_account_id and user_id=:user_id';

            $GoldsAccountLogSQL = 'INSERT INTO mb_integral_account_log (`integral_account_id`,`user_id`,`device_type`,`operate_type`,`operate_value`,`before_balance`,`after_balance`,`create_time`)
                                                    VALUES (:integral_account_id,:user_id,:device_type,:operate_type,:operate_value,:before_balance,:after_balance,:create_time)';

            try {
                $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);

                $rst = \Yii::$app->db->createCommand($accountSQL, [
                    ':integral_account_total' => intval($operateValue),
                    ':integral_account_balance' => intval($operateValue),
                    ':integral_account_id' => $integral_account_id,
                    ':user_id' => $user_id,
                ])->execute();

                $rstLog = \Yii::$app->db->createCommand($GoldsAccountLogSQL, [
                    ':integral_account_id' => $integral_account_id,
                    ':user_id' => $user_id,
                    ':device_type' => $device_type,
                    ':operate_type' => $operateType,
                    ':operate_value' => $operateValue,
                    ':before_balance' => $old_integral_account_balance,
                    ':after_balance' => intval($old_integral_account_balance) + intval($operateValue),
                    ':create_time' => date('Y-m-d H:i:s')
                ])->execute();

                if( $rst >0 && $rstLog>0 ){
                    $trans->commit();
                    $phpLock->unlock();
                    $error = "操作成功";
                    return true;
                }else{
                    $trans->rollBack();
                    $phpLock->unlock();
                    $error = '添加用户积分时发生了错误 ';
                    \Yii::getLogger()->log('添加用户积分时发生了错误',Logger::LEVEL_ERROR);
                    return false;
                }

        }catch (Exception $e){
            $phpLock->unlock();
            $error = '添加用户积分时发生了错误: '.$e;
            \Yii::getLogger()->log('添加用户积分时发生了错误: '.$e,Logger::LEVEL_ERROR);
            return false;
        }
    }
    
    /*
     * 从用户的积分帐户中减少金币，同时产生帐户变动日记
     * @param $integral_account_id $user_id $device_type $operateType $operateValue
     * @return true or false
     */
    public static function UpdateIntegralAccountToLessen($integral_account_id,$user_id,$device_type,$operateType,$operateValue,&$error){
            $model = IntegralAccount::findOne(['integral_account_id'=>$integral_account_id,'user_id'=>$user_id]);
            if(!$model){
                 $error = "用户id:".$user_id."的积分帐户不存在";
                 \Yii::getLogger()->log("用户id:".$user_id."的积分帐户不存在",Logger::LEVEL_ERROR);
                 return false;
            }
            if(!self::CheckBalance($model, $error)){
                $error = '签名验证不正确';
               \Yii::getLogger()->log('签名验证不正确:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
            
            if( $model->integral_account_balance < $operateValue ){
                 $error = $user_id.'的积分帐户余额不足';
                 \Yii::getLogger()->log($user_id.'的积分帐户余额不足',Logger::LEVEL_ERROR);
                 return false;
            }
            
            $phpLock = new PhpLock("update_integral_account_to_lessen");
            $phpLock->lock();
            $old_integral_account_balance    = $model->integral_account_balance;

            $accountSQL = 'update mb_integral_account set integral_account_spend = integral_account_spend + :integral_account_spend,
                               integral_account_balance = integral_account_balance - :integral_account_balance, sign=MD5(
                                  CONCAT(
                                  \'integral_account_id=\',integral_account_id,
                                  \'&user_id=\',user_id,
                                  \'&integral_account_total=\', REPLACE(FORMAT(integral_account_total,2),\',\',\'\'),
                                  \'&integral_account_spend=\',REPLACE(FORMAT(integral_account_spend,2),\',\',\'\'),
                                  \'&integral_account_balance=\',REPLACE(FORMAT(integral_account_balance,2),\',\',\'\'),
                                  \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))  
                                WHERE  integral_account_id=:integral_account_id and user_id=:user_id';

        $GoldsAccountLogSQL = 'INSERT INTO mb_integral_account_log (`integral_account_id`,`user_id`,`device_type`,`operate_type`,`operate_value`,`before_balance`,`after_balance`,`create_time`)
                                                    VALUES (:integral_account_id,:user_id,:device_type,:operate_type,:operate_value,:before_balance,:after_balance,:create_time)';

        try {
            $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
            $rst = \Yii::$app->db->createCommand($accountSQL, [
                ':integral_account_spend' => intval($operateValue),
                ':integral_account_balance' => intval($operateValue),
                ':integral_account_id' => $integral_account_id,
                ':user_id' => $user_id,
            ])->execute();

            $rstLog = \Yii::$app->db->createCommand($GoldsAccountLogSQL, [
                ':integral_account_id' => $integral_account_id,
                ':user_id' => $user_id,
                ':device_type' => $device_type,
                ':operate_type' => $operateType,
                ':operate_value' => $operateValue,
                ':before_balance' => $old_integral_account_balance,
                ':after_balance' => intval($old_integral_account_balance) - intval($operateValue),
                ':create_time' => date('Y-m-d H:i:s')
            ])->execute();

            if( $rst >0 && $rstLog>0 ){
                $trans->commit();
                $phpLock->unlock();
                $error = "操作成功";
                return true;
            }else{
                $trans->rollBack();
                $phpLock->unlock();
                $error = '减少用户积分时发生了错误 ';
                \Yii::getLogger()->log('减少用户积分时发生了错误',Logger::LEVEL_ERROR);
                return false;
            }

        }catch (Exception $e){
            $trans->rollBack();
            $phpLock->unlock();
            $error = '减少用户积分时发生了错误: '.var_export($e,true);
            \Yii::getLogger()->log('减少用户积分时发生了错误: '.var_export($e,true),Logger::LEVEL_ERROR);
            return false;
        }
    }

    /*
     *退回用户的积分
     * @param $integral_account_id $user_id $device_type $operateType $operateValue $createTime
     * @return true or false
     */
    public static function RollBackUserIntegral($integral_account_id,$user_id,$device_type,$operateType,$operateValue,$createTime,&$error)
    {
        $IntegralAccountLogModel = IntegralAccountLog::findOne(['integral_account_id'=>$integral_account_id,'user_id'=>$user_id,'device_type'=>$device_type,'operate_type'=>$operateType,'operate_value'=>$operateValue,'create_time'=>$createTime]);
        if($IntegralAccountLogModel){
            $model    = IntegralAccount::findOne(['integral_account_id'=>$integral_account_id,'user_id'=>$user_id]);
            if(!self::CheckBalance($model, $error)){
                $error = '积分帐户签名验证不正确:'.$error;
                \Yii::getLogger()->log('积分帐户签名验证不正确:'.$error,Logger::LEVEL_ERROR);
                return false;
            }

            $phpLock = new PhpLock("integral_account_to_roll_back");
            $phpLock->lock();
            $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
            $old_integral_account_balance      = $model->integral_account_balance;
            $model->integral_account_spend   = $model->integral_account_spend - intval($operateValue);
            $model->integral_account_balance = $model->integral_account_balance + intval($operateValue);
            $model->sign                       = self::CreateAccountSign($model);

            $IntegralAccountLogInfoModel = new IntegralAccountLog();
            $IntegralAccountLogInfoModel->integral_account_id  = $model->integral_account_id;
            $IntegralAccountLogInfoModel->user_id               = $user_id;
            $IntegralAccountLogInfoModel->device_type          = $device_type;
            $IntegralAccountLogInfoModel->operate_type         = 3;
            $IntegralAccountLogInfoModel->operate_value        = $operateValue;
            $IntegralAccountLogInfoModel->before_balance       = $old_integral_account_balance;
            $IntegralAccountLogInfoModel->after_balance        = intval($old_integral_account_balance) + intval($operateValue);

            if( $model->save()  &&  $IntegralAccountLogInfoModel->save() ){
                $trans->commit();
                $phpLock->unlock();
                $error = "回退操作成功";
                return true;
            }else{
                $trans->rollBack();
                $phpLock->unlock();
                $error = '回退用户积分时发生了错误: '.var_export($model->getErrors().'-'.$error,true);
                \Yii::getLogger()->log('回退用户积分时发生了错误: '.var_export($model->getErrors().'-'.$error,true),Logger::LEVEL_ERROR);
                return false;
            }

        }else
        {
            $error = '积分帐户日记没有相应的记录';
            \Yii::getLogger()->log('积分帐户日记没有相应的记录',Logger::LEVEL_ERROR);
            return false;
        }
    }

    /**
     * 验证账户是否正确
     * @param $goldsAccount
     * @param $error
     */
    public static function CheckBalance($IntegralAccount,&$error){
        if( !($IntegralAccount instanceof IntegralAccount )){
            $error = '不是积分帐户数据类型';
            return false;
        }

        $fileds = ['integral_account_id', 'user_id','integral_account_total', 'integral_account_spend','integral_account_balance','rand_str'];
        $numberFields = ['integral_account_total','integral_account_spend', 'integral_account_balance'];
        $len = count($fileds);
        $sourceStr = '';
        for($i=0; $i <$len; $i++){
            if(in_array($fileds[$i], $numberFields)){
                $sourceStr .= sprintf("$fileds[$i]=%0.2f&",$IntegralAccount->$fileds[$i]);
            }
            else{
                $sourceStr .= sprintf("$fileds[$i]=%s&",$IntegralAccount->$fileds[$i]);
            }
        }
        
        $sourceStr .= 'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq';
        if(!($IntegralAccount->sign === md5($sourceStr))){
            \Yii::getLogger()->log('sign:'.$IntegralAccount->sign,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('md5:'.md5($sourceStr),Logger::LEVEL_ERROR);
            $error = '金币账户信息异常，请与客服人员联系';
            return false;
        }
        return true;
    }
    
    private static function CreateAccountSign($integralAccount){
          $fileds = ['integral_account_id', 'user_id','integral_account_total', 'integral_account_spend','integral_account_balance','rand_str'];
          $numberFields = ['integral_account_total','integral_account_spend', 'integral_account_balance'];
          $len = count($fileds);
          $sourceStr = '';
          for($i=0; $i <$len; $i++){
              if(in_array($fileds[$i], $numberFields)){
                  $sourceStr .= sprintf("$fileds[$i]=%0.2f&",$integralAccount->$fileds[$i]);
              }
              else{
                  $sourceStr .= sprintf("$fileds[$i]=%s&",$integralAccount->$fileds[$i]);
              }
          }

          $sourceStr .= 'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq';
          
          return  md5($sourceStr);
    }

}