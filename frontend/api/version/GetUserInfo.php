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
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (empty($data['data']['username']) || !isset($data['data']['username'])) {
            $error = '用户名, 不能为空';
            return false;
        }
        $username = explode(',', $data['data']['username']);
        foreach($username as $item) {
            if(empty($item))
                continue;
            $User = User::findOne(['username' =>$item]);
            if (empty($User)) {
                $error = "{{$item}}".'该账号不存在';
                return false;
            }
            if (isset($User) && $User->status === 0) {
                $error = $User->username.' 该账号已被管理员禁用';
                return false;
            }
            $data[] = [
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
        $rstData['data'] = $data;
        return true;
    }
}