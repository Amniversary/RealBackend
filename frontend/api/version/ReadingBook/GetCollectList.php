<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/14
 * Time: 下午3:48
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;

class GetCollectList implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!isset($data['data']['user_id']) || empty($data['data']['user_id'])) {
            $error = '用户id , 不能为空';
            return false;
        }
        $User = ClientUtil::getBookUserById($data['data']['user_id']);
        if(empty($User)) {
            $error = '用户信息未授权';
            return false;
        }

        $list = DynamicUtil::getCollectList($User->client_id);
        if(empty($list)) {
            $list = [];
        }

        $rstData['code'] = 0;
        $rstData['data'] = $list;
        return true;
    }
}