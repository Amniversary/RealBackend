<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/15
 * Time: 下午3:04
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\ClientUtil;

class GetUser implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!isset($dataProtocol['data']['user_id']) || empty($dataProtocol['data']['user_id'])) {
            $error = '用户Id, 不能为空';
            return false;
        }
        $user_id = $dataProtocol['data']['user_id'];
        $User = ClientUtil::getBookUserById($user_id);
        if(empty($User)) {
            $error = '用户信息不存在';
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = [
            'user_id'=> $User->client_id,
            'nick_name'=> $User->nick_name,
            'pic'=>$User->pic,
            'sex'=>$User->sex
        ];
        return true;
    }

}