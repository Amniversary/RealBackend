<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/15
 * Time: 下午2:07
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;

class DelCollect implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) return false;
        $dynamic_id = $dataProtocol['data']['dynamic_id'];
        $user_id = $dataProtocol['data']['user_id'];
        unset($dataProtocol);
        $collect = DynamicUtil::getCollect($user_id, $dynamic_id);
        $collect->delete();
        $rstData['code'] = 0;
        return true;
    }

    private function check_params($dataTotal, &$error)
    {
        $files = ['user_id', 'dynamic_id'];
        $filesLabel = ['用户id', '动态id'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataTotal['data'][$files[$i]]) || empty($dataTotal['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}