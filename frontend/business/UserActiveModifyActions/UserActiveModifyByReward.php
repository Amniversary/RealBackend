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
 * 打赏修改
 * Class UserActiveModifyBySign
 * @package frontend\business\UserActiveModifyActions
 */
class UserActiveModifyByReward implements IUserActiveModify
{
    public function UserActiveModify($userAcive,&$error,$params=[])
    {
        if(!($userAcive instanceof UserActive))
        {
            $error = '不是用户活跃记录对象13';
            return false;
        }
        if(!isset($params['reward_money']) ||
            empty($params['reward_money']) ||
            doubleval($params['reward_money']) <= 0
        )
        {
            $error = '打赏金额必须大于0';
            return false;
        }
        $userAcive = UserActiveUtil::GetUserActiveByUserId($userAcive->user_id);
        if(!isset($userAcive))
        {
            $error = '用户活跃度记录不存在';
            return false;
        }
        $userAcive->reward_count += 1;
        $userAcive->reward_money_sum +=  doubleval($params['reward_money']);
        if(!$userAcive->save())
        {
            \Yii::getLogger()->log('用户活跃度信息保存失败'.var_export($userAcive->getErrors(),true),Logger::LEVEL_ERROR);
            throw new Exception('打赏时，用户活跃度信息保存失败');
        }
        return true;
    }
} 