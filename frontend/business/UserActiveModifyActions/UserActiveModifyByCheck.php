<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/18
 * Time: 13:25
 */

namespace frontend\business\UserActiveModifyActions;
use common\models\BusinessCheck;
use common\models\UserActive;
use frontend\business\UserActiveUtil;
use yii\base\Exception;
use yii\log\Logger;

/**
 * 审核修改
 * Class UserActiveModifyBySign
 * @package frontend\business\UserActiveModifyActions
 */
class UserActiveModifyByCheck implements IUserActiveModify
{
    public function UserActiveModify($userAcive,&$error,$params=[])
    {
        if(!($userAcive instanceof UserActive))
        {
            $error = '不是用户活跃记录对象10';
            return false;
        }
        if(!isset($params['check_refused_content']) ||
            empty($params['check_refused_content']) ||
            !($params['check_refused_content'] instanceof BusinessCheck)
        )
        {
            $error = '不是审核记录对象';
            return false;
        }
        $businessCheck = $params['check_refused_content'];
        if($businessCheck->check_result_status != 0)
        {
            $error = '不是拒绝的审核';
            return false;
        }
        if(empty($businessCheck->refused_reason))
        {
            $error = '审核记录拒绝理由不能为空';
            return false;
        }
        $userAcive = UserActiveUtil::GetUserActiveByUserId($userAcive->user_id);
        if(!isset($userAcive))
        {
            $error = '用户活跃度记录不存在';
            return false;
        }

        $refusedInfo = [
            'check_id'=>$businessCheck->business_check_id,
            'check_type'=>$businessCheck->GetCheckTypeName(),
            'check_time'=>$businessCheck->check_time,
            'check_user'=>$businessCheck->check_user_name,
            'refused_reason'=>empty($businessCheck->refused_reason)?'无':$businessCheck->refused_reason,
        ];
        $cnt = $userAcive->check_refused_content;
        if(empty($cnt))
        {
            $aryCnt = [];
        }
        else
        {
            $aryCnt = unserialize($cnt);
            if(!isset($aryCnt) || !is_array($aryCnt))
            {
                $aryCnt = [];
            }
            if(count($aryCnt) >= 5)
            {
                array_pop($aryCnt);
            }
        }
        array_unshift($aryCnt,$refusedInfo);
        $userAcive->check_refused_content = serialize($aryCnt);
        $userAcive->check_refused_count += 1;
        if(!$userAcive->save())
        {
            \Yii::getLogger()->log('用户活跃度信息保存失败'.var_export($userAcive->getErrors(),true),Logger::LEVEL_ERROR);
            throw new Exception('审核记录时，用户活跃度信息保存失败');
        }
        return true;
    }
} 