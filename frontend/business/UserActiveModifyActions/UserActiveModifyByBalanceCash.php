<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/18
 * Time: 13:25
 */

namespace frontend\business\UserActiveModifyActions;
use common\models\UserActive;
use frontend\business\UserActiveUtil;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 余额提现修改
 * Class UserActiveModifyBySign
 * @package frontend\business\UserActiveModifyActions
 */
class UserActiveModifyByBalanceCash implements IUserActiveModify
{
    public function UserActiveModify($userAcive,&$error,$params=[])
    {
        if(!($userAcive instanceof UserActive))
        {
            $error = '不是用户活跃记录对象9';
            return false;
        }
        if(!isset($params['balance_cash_money']) ||
            empty($params['balance_cash_money']) ||
            doubleval($params['balance_cash_money']) <= 0
        )
        {
            $error = '余额提现金额必须大于0';
            return false;
        }
        $userAcive = UserActiveUtil::GetUserActiveByUserId($userAcive->user_id);
        if(!isset($userAcive))
        {
            $error = '用户活跃度记录不存在';
            return false;
        }
        $userAcive->balance_cash_count += 1;
        $userAcive->balance_cash_money += doubleval($params['balance_cash_money']);
        if(!$userAcive->save())
        {
            \Yii::getLogger()->log('用户活跃度信息保存失败'.var_export($userAcive->getErrors(),true),Logger::LEVEL_ERROR);
            throw new Exception('余额提现时，用户活跃度信息保存失败');
        }
        return true;
    }
} 