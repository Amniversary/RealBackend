<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/7
 * Time: 11:20
 */

namespace frontend\business\UserAccountBalanceActions;


use common\models\UserAccountInfo;
use yii\base\Exception;

class ModifyUserBillPwd implements IModifyUserAccountInfo
{
    public function  ModifyUserBillInfo($params,&$billInfo, &$error)
    {
        if(!($billInfo instanceof UserAccountInfo))
        {
            $error = '不是用户账户余额记录';
            return false;
        }
        if(empty($params['new_pwd']))
        {
            $error = '密码不能为空';
            return false;
        }
        $new_pwd = $params['new_pwd'];
        $billInfo->SetPassword($new_pwd);
        if(!$billInfo->save())
        {
            throw new Exception('修改用户支付密码失败');
        }
        return true;
    }

} 