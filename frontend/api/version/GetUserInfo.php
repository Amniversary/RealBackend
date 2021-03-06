<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/28
 * Time: 下午12:31
 */

namespace frontend\api\version;


use common\models\User;
use frontend\api\IApiExecute;

class GetUserInfo implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (empty($dataProtocol['data']['username']) || !isset($dataProtocol['data']['username'])) {
            $error = '用户名, 不能为空';
            return false;
        }
        $rst = [];
        $username = explode(',', $dataProtocol['data']['username']);
        foreach ($username as $item) {
            if (empty($item))
                continue;
            $User = User::findOne(['username' => $item]);
            if (empty($User)) {
                $error = "{{$item}}" . '该账号不存在';
                return false;
            }
            if (isset($User) && $User->status === 0) {
                $error = $User->username . ' 该账号已被管理员禁用';
                return false;
            }
            $rst[] = [
                'id' => intval($User->backend_user_id),
                'username' => $User->username,
                'email' => $User->email,
                'pic' => $User->pic,
                'phone' => $User->phone,
                'create_at' => $User->create_at,
                'update_at' => $User->update_at
            ];
        }
        $rstData['code'] = 0;
        $rstData['data'] = $rst;
        return true;
    }
}