<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/10
 * Time: 下午3:30
 */

namespace frontend\api\version\Wedding;


use frontend\api\IApiExecute;
use frontend\business\AppInfoUtil;
use frontend\business\InvitationUtil;

class GetInvitationList implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!isset($dataProtocol['data']['type']) || empty($dataProtocol['data']['type'])) {
            $error = '列表类型不能为空';
            return false;
        }
        $header = \Yii::$app->request->headers;
        $appId = $header['appid'];
        $openId = $header['openid'];
        $type = $dataProtocol['data']['type'];
        if (!in_array($type, [1, 2])) {
            $error = '列表类型错误';
            return false;
        }
        $User = AppInfoUtil::GetAppClientInfo($appId, $openId);
        if (empty($User)) {
            $error = '用户未授权, 请授权小程序';
            return false;
        }
        switch($type) {
            case 1: $list = InvitationUtil::GetMyWeddingInfo($User['id']);break;
            case 2: $list = InvitationUtil::GetOtherWeddingInfo($User['id']); break;
        }
        $rstData['data'] = empty($list) ? [] : $list;
        $rstData['code'] = 0;
        return true;
    }
}