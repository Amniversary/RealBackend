<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 14:08
 */

namespace frontend\business\UserAccountBalanceActions;


use common\models\UserAccountInfo;
use frontend\business\PersonalUserUtil;
use yii\base\Exception;
use yii\log\Logger;

class ModifyUserBillAddBalance implements IModifyUserAccountInfo
{
    public function  ModifyUserBillInfo($params,&$billInfo, &$error)
    {
        if(!($billInfo instanceof UserAccountInfo))
        {
            $error = '不是用户账户余额记录';
            return false;
        }
        if(!isset($params['add_money']) || empty($params['add_money']) || doubleval($params['add_money'])<= 0.0)
        {
            $error = '修改金额必须大于0';
            return false;
        }
        $sign = $billInfo->sign;
        $nowSign = PersonalUserUtil::GetUserAccountInfoSign($billInfo->attributes);
        if($sign !== $nowSign)
        {
            $error = '金额数据不一致，请与客服人员联系';
            return false;
        }
        $sql = 'update my_user_account_info set balance = balance + :am ,
sign=MD5(CONCAT(\'account_info_id=\',account_info_id,\'&user_id=\',user_id,\'&balance=\', REPLACE(FORMAT(balance,2),\',\',\'\'),\'&pay_pwd=\',pay_pwd,\'&out_money_sum=\',REPLACE(FORMAT(out_money_sum,2),\',\',\'\'),\'&recharge_money_sum=\',REPLACE(FORMAT(recharge_money_sum,2),\',\',\'\'),\'&rand_str=\',rand_str,\'&dowj0sew0fs=f02hs0e02u0ur20rurue\'))
where user_id=:uid
';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':am'=>$params['add_money'],
            ':uid'=>$billInfo->user_id
        ])->execute();
        if($rst <= 0)
        {
            $error = '账户余额更新失败';
            \Yii::getLogger()->log($error.' sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 