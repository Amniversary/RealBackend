<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/10
 * Time: 下午5:10
 */

namespace frontend\api\version\Wedding;


use frontend\api\IApiExecute;
use frontend\business\AppInfoUtil;
use frontend\business\InvitationUtil;

class SetAdmin implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if(!isset($dataProtocol['data']['card_id']) || empty($dataProtocol['data']['card_id'])) {
            $error = '请帖 Id 不能为空';
            return false;
        }
        if(!isset($dataProtocol['data']['user_id']) || empty($dataProtocol['data']['user_id'])) {
            $error = '用户 Id 不能为空';
            return false;
        }
        $header = \Yii::$app->request->headers;
        $appId = $header['appid'];
        $openId = $header['openid'];
        $card_id = $dataProtocol['data']['card_id'];
        $user_id = $dataProtocol['data']['user_id'];
        $User = AppInfoUtil::GetAppClientInfo($appId, $openId);
        if (empty($User)) {
            $error = '用户未授权, 请授权小程序';
            return false;
        }
        $Guest = InvitationUtil::GetGuestUserById($card_id, $user_id);
        if(empty($Guest)) {
            $error = '设置的用户不存在';
            return false;
        }
        if(in_array($Guest->user_status, [1,2])) {
            $error = '该用户已经是管理员';
            return false;
        }
        $Guest->user_status = 2;
        if(!$Guest->save()) {
            $error = '系统错误, 设置管理员失败';
            \Yii::error($error . ' ' . var_export($Guest->getErrors(),true));
            return false;
        }
        return true;
    }
}