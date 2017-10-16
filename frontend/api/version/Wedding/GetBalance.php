<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/12
 * Time: 下午5:51
 */

namespace frontend\api\version\Wedding;


use frontend\api\IApiExecute;
use frontend\business\AppInfoUtil;
use frontend\business\BalanceUtil;

class GetBalance implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        $header = \Yii::$app->request->headers;
        $User = AppInfoUtil::GetAppClientInfo($header['appid'],  $header['openid']);
        if(empty($User) || ($User == false)) {
            $error = '用户未授权, 请授权小程序';
            return false;
        }
        $balance = BalanceUtil::GetUserBalanceById($User['id']);
        if(empty($balance) || !isset($balance)) {
            $error = '系统错误, 用户账户信息异常';
            return false;
        }
        if($balance->status == 0) {
            $error = '用户账户系统被冻结';
            return false;
        }

        $rstData['data'] = [
            'recharge' => $balance->recharge_num,
            'reward' => $balance->reward_num,
            'balance' => $balance->balance
        ];
        return true;
    }
}