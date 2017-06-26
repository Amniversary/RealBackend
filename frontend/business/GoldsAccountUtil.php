<?php

/**
 * Created by PhpStorm.
 * User: wangwei
 * Date: 2016/10/12
 * Time: 16:31
 */

namespace frontend\business;


use common\components\PhpLock;
use common\models\GoldsAccount; 
use common\components\UsualFunForStringHelper;
use common\models\GoldsAccountLog;
use yii\base\Exception;
use yii\db\Query;
use yii\log\Logger;
use yii\db\Transaction;

class GoldsAccountUtil {
     
     /*
     * 根据用户id获取金币帐户信息Model
     * @param $user_id
     * @return null|static   GoldsAccount model
     */
     public static function GetGoldsAccountModleByUserId($user_id){
          return GoldsAccount::findOne(['user_id'=>$user_id]);
     }

    /*
   * 根据用户id,判断帐户记录是否存在
   * @param $user_id
   * @return null|static   GoldsAccount model
   */
    public static function GetGoldsAccountModleIsExists($user_id){
        if(GoldsAccount::findOne(['user_id'=>$user_id]) ){
            return true;
        }
        return false;
    }

     /*
     * 根据帐户id获取金币帐户信息Model
     * @param $user_id
     * @return null|static   GoldsAccount model
     */
     public static function GetGoldsAccountModleByGoldAccountId($gold_account_id){
          return GoldsAccount::findOne(['gold_account_id'=>$gold_account_id]);
     }
    
    /**
     * 根据用户id获取金币帐户信息,返回有限的字段
     * @param $user_id
     * @return null|static   GoldsAccount model
     */
    public static function GetGoldsAccountInfoByUserId($user_id){
        return GoldsAccount::find()->asArray()
                ->select(['gold_account_id', 'gold_account_total', 'gold_account_expend', 'gold_account_balance', 'account_status'])
                ->where(['user_id'=>$user_id])
                ->one();
    }
    
    /**
     * 为新注册的用户开设一个金币帐户
     * @param $user_id
     * @return true or false
     */
    public static function CreateAnGoldsAccountForNewRegisUser($user_id){

            $phpLock = new PhpLock("create_golds_account_for_new_regisuser");
            $phpLock->lock();
            $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
            $accountSQL = "INSERT INTO mb_golds_account ( user_id,gold_account_total,gold_account_expend,gold_account_balance,rand_str,account_status,create_time ) VALUES ( :user_id,:gold_account_total,:gold_account_expend,:gold_account_balance,:rand_str,:account_status,:create_time)";
            $rand_str = UsualFunForStringHelper::mt_rand_str(40);
            $acctountRetVal = \Yii::$app->db->createCommand($accountSQL,[
                ":user_id"=>$user_id,
                ":gold_account_total"=>0,
                ":gold_account_expend"=>0,
                ":gold_account_balance"=>0,
                ":rand_str"=>$rand_str,
                ":account_status"=>1,
                ":create_time"   =>date('Y-m-d H:i:s')
            ])->execute();

            $accountSQLError = \Yii::$app->db->createCommand($accountSQL,[
                ":user_id"=>$user_id,
                ":gold_account_total"=>0,
                ":gold_account_expend"=>0,
                ":gold_account_balance"=>0,
                ":rand_str"=>$rand_str,
                ":account_status"=>1,
                ":create_time"   =>date('Y-m-d H:i:s')
            ])->rawSql;

            $accountSignSQL = 'update mb_golds_account set 
                sign=MD5(
                  CONCAT(
                  \'gold_account_id=\',gold_account_id,
                  \'&user_id=\',user_id,
                  \'&gold_account_total=\', REPLACE(FORMAT(gold_account_total,2),\',\',\'\'),
                  \'&gold_account_expend=\',REPLACE(FORMAT(gold_account_expend,2),\',\',\'\'),
                  \'&gold_account_balance=\',REPLACE(FORMAT(gold_account_balance,2),\',\',\'\'),
                  \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))
                  where   user_id=:user_id';



            $acctountSignRetVal = \Yii::$app->db->createCommand($accountSignSQL,[
                ':user_id'=>$user_id
            ])->execute();

            $accountSignSQLError = \Yii::$app->db->createCommand($accountSignSQL,[
                ':user_id'=>$user_id])->rawSql;

            if(  $acctountRetVal <=0 && $acctountSignRetVal <=0 ){

                $error = '创建用户金币帐户时';
                \Yii::getLogger()->log($error.'失败;'.'user_id:['.$user_id.'] sql:'.$accountSQLError,Logger::LEVEL_ERROR);
                \Yii::getLogger()->log($error.'更新签名时;' .'user_id:['.$user_id.'] sql:'.$accountSignSQLError,Logger::LEVEL_ERROR);
                $trans->rollBack();
                $phpLock->unlock();
                return false;
            }

            $error = '创建用户金币帐户成功';
            \Yii::getLogger()->log('user_id:['.$user_id.']'.$error,Logger::LEVEL_ERROR);
            $trans->commit();
            $phpLock->unlock();
            return TRUE;
    }
    
    /*
     * 添加金币到用户的金币帐户，同时产生帐户变动日记
     * @param $gold_account_id $user_id $device_type $operateType $operateValue
     * @return true or false
     */
    public static function UpdateGoldsAccountToAdd($gold_account_id,$user_id,$device_type,$operateType,$operateValue,&$error){
            $model = GoldsAccount::findOne(['gold_account_id'=>$gold_account_id,'user_id'=>$user_id]);
            if(!$model){
                $error = "户用id:".$user_id."的金币帐户不存在";
                \Yii::getLogger()->log("户用id:".$user_id."的金币帐户不存在",Logger::LEVEL_ERROR);
                return false;
            }
            if(!self::CheckBalance($model, $error)){
                $error = '签名验证不正确';
                \Yii::getLogger()->log('签名验证不正确:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
            $phpLock = new PhpLock("update_golds_account_to_add");
            $phpLock->lock();

            $old_gold_account_balance    = $model->gold_account_balance;

            $accountSQL = 'update mb_golds_account set gold_account_total = gold_account_total + :gold_account_total,
                           gold_account_balance = gold_account_balance + :gold_account_balance, sign=MD5(
                              CONCAT(
                              \'gold_account_id=\',gold_account_id,
                              \'&user_id=\',user_id,
                              \'&gold_account_total=\', REPLACE(FORMAT(gold_account_total,2),\',\',\'\'),
                              \'&gold_account_expend=\',REPLACE(FORMAT(gold_account_expend,2),\',\',\'\'),
                              \'&gold_account_balance=\',REPLACE(FORMAT(gold_account_balance,2),\',\',\'\'),
                              \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))  
                            WHERE  gold_account_id=:gold_account_id and user_id=:user_id';

            $GoldsAccountLogSQL = 'INSERT INTO mb_golds_account_log (`gold_account_id`,`user_id`,`device_type`,`operate_type`,`operate_value`,`before_balance`,`after_balance`,`create_time`)
                                                    VALUES (:gold_account_id,:user_id,:device_type,:operate_type,:operate_value,:before_balance,:after_balance,:create_time)';

            $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
            try {

                $rst = \Yii::$app->db->createCommand($accountSQL, [
                    ':gold_account_total' => intval($operateValue),
                    ':gold_account_balance' => intval($operateValue),
                    ':gold_account_id' => $gold_account_id,
                    ':user_id' => $user_id,
                ])->execute();

               $rstLog = \Yii::$app->db->createCommand($GoldsAccountLogSQL, [
                ':gold_account_id' => $gold_account_id,
                ':user_id' => $user_id,
                ':device_type' => $device_type,
                ':operate_type' => $operateType,
                ':operate_value' => $operateValue,
                ':before_balance' => $old_gold_account_balance,
                ':after_balance' => intval($old_gold_account_balance) + intval($operateValue),
                ':create_time' => date('Y-m-d H:i:s')
               ])->execute();

                if( $rst >0 &&  $rstLog >0 ){
                    $trans->commit();
                    $phpLock->unlock();
                    $error = '操作成功';
                    return true;
                }else{
                    $trans->rollBack();
                    $phpLock->unlock();
                    $error = '添加用户金币时发生了错误';
                    \Yii::getLogger()->log('gold_account==>添加用户金币时发生了错误',Logger::LEVEL_ERROR);
                    return false;
                }

            }catch (Exception $e){
                $trans->rollBack();
                $phpLock->unlock();
                $error = '添加用户金币时发生了错误: '.$e;
                \Yii::getLogger()->log('添加用户金币时发生了错误: '.$e,Logger::LEVEL_ERROR);
                return false;
            }

    }
    
    /*
     * 从用户的金币帐户中减少金币，同时产生帐户变动日记
     * @param $gold_account_id $user_id $device_type $operateType $operateValue
     * @return true or false
     */
    public static function UpdateGoldsAccountToLessen($gold_account_id,$user_id,$device_type,$operateType,$operateValue,&$error){
            $model = GoldsAccount::findOne(['gold_account_id'=>$gold_account_id,'user_id'=>$user_id]);
            if(!$model){
                 $error = "用户id:".$user_id."的金币帐户不存在";
                 \Yii::getLogger()->log("用户id:".$user_id."的金币帐户不存在",Logger::LEVEL_ERROR);
                 return false;
            }
            if(!self::CheckBalance($model, $error)){
                $error = '签名验证不正确';
               \Yii::getLogger()->log('签名验证不正确:'.$error,Logger::LEVEL_ERROR);
                return false;
            }
            
            if( $model->gold_account_balance < $operateValue ){
                 $error = $user_id.'的金币帐户余额不足';
                 \Yii::getLogger()->log($user_id.'的金币帐户余额不足',Logger::LEVEL_ERROR);
                 return false;
            }
            
            $phpLock = new PhpLock("update_golds_account_to_lessen");
            $phpLock->lock();
            $old_gold_account_balance    = $model->gold_account_balance;

            $accountSQL = 'update mb_golds_account set gold_account_expend = gold_account_expend + :gold_account_expend,
                           gold_account_balance = gold_account_balance - :gold_account_balance, sign=MD5(
                              CONCAT(
                              \'gold_account_id=\',gold_account_id,
                              \'&user_id=\',user_id,
                              \'&gold_account_total=\', REPLACE(FORMAT(gold_account_total,2),\',\',\'\'),
                              \'&gold_account_expend=\',REPLACE(FORMAT(gold_account_expend,2),\',\',\'\'),
                              \'&gold_account_balance=\',REPLACE(FORMAT(gold_account_balance,2),\',\',\'\'),
                              \'&rand_str=\',rand_str,\'&chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq\'))  
                            WHERE  gold_account_id=:gold_account_id and user_id=:user_id';

            $GoldsAccountLogSQL = 'INSERT INTO mb_golds_account_log (`gold_account_id`,`user_id`,`device_type`,`operate_type`,`operate_value`,`before_balance`,`after_balance`,`create_time`)
                                                    VALUES (:gold_account_id,:user_id,:device_type,:operate_type,:operate_value,:before_balance,:after_balance,:create_time)';

            $trans = \Yii::$app->db->beginTransaction(Transaction::REPEATABLE_READ);
            try {

                $rst = \Yii::$app->db->createCommand($accountSQL, [
                    ':gold_account_expend' => intval($operateValue),
                    ':gold_account_balance' => intval($operateValue),
                    ':gold_account_id' => $gold_account_id,
                    ':user_id' => $user_id,
                ])->execute();

                $rstLog = \Yii::$app->db->createCommand($GoldsAccountLogSQL, [
                    ':gold_account_id' => $gold_account_id,
                    ':user_id' => $user_id,
                    ':device_type' => $device_type,
                    ':operate_type' => $operateType,
                    ':operate_value' => $operateValue,
                    ':before_balance' => $old_gold_account_balance,
                    ':after_balance' => intval($old_gold_account_balance) - intval($operateValue),
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
                    $error = '减少用户金币时发生了错误 ';
                    \Yii::getLogger()->log('减少用户金币时发生了错误',Logger::LEVEL_ERROR);
                    return false;
                }

            }catch (Exception $e){
                $trans->rollBack();
                $phpLock->unlock();
                $error = '减少用户金币时发生了错误: '.var_export($e,true);
                \Yii::getLogger()->log('减少用户金币时发生了错误: '.var_export($e,true),Logger::LEVEL_ERROR);
                return false;
            }
    }
    
    
    /**
     * 验证账户是否正确
     * @param $goldsAccount
     * @param $error
     */
    public static function CheckBalance($goldsAccount,&$error){
        if(!($goldsAccount instanceof GoldsAccount)){
            $error = '不是金币帐户数据类型';
            return false;
        }

        $fileds = ['gold_account_id', 'user_id','gold_account_total', 'gold_account_expend','gold_account_balance','rand_str'];
        $numberFields = ['gold_account_total','gold_account_expend', 'gold_account_balance'];
        $len = count($fileds);
        $sourceStr = '';
        for($i=0; $i <$len; $i++){
            if(in_array($fileds[$i], $numberFields)){
                $sourceStr .= sprintf("$fileds[$i]=%0.2f&",$goldsAccount->$fileds[$i]);
            }
            else{
                $sourceStr .= sprintf("$fileds[$i]=%s&",$goldsAccount->$fileds[$i]);
            }
        }
        
        $sourceStr .= 'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq';
        if(!($goldsAccount->sign === md5($sourceStr))){
            \Yii::getLogger()->log('sign:'.$goldsAccount->sign,Logger::LEVEL_ERROR);
            \Yii::getLogger()->log('md5:'.md5($sourceStr),Logger::LEVEL_ERROR);
            $error = '金币账户信息异常，请与客服人员联系';
            return false;
        }
        return true;
    }
    
    private static function CreateAccountSign($goldsAccount){
          $fileds = ['gold_account_id', 'user_id','gold_account_total', 'gold_account_expend','gold_account_balance','rand_str'];
          $numberFields = ['gold_account_total','gold_account_expend', 'gold_account_balance'];
          $len = count($fileds);
          $sourceStr = '';
          for($i=0; $i <$len; $i++){
              if(in_array($fileds[$i], $numberFields)){
                  $sourceStr .= sprintf("$fileds[$i]=%0.2f&",$goldsAccount->$fileds[$i]);
              }
              else{
                  $sourceStr .= sprintf("$fileds[$i]=%s&",$goldsAccount->$fileds[$i]);
              }
          }

          $sourceStr .= 'chise1bht0z=lkc12i8xzh4wnmz90qnmxca2zqwdc9wqxxzjstlq';
          
          return  md5($sourceStr);
    }

}