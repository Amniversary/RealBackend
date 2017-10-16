<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午3:13
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;

class GetDynamicList implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {

        if (!$this->check_params($dataProtocol, $error)) return false;
        $page_no = $dataProtocol['data']['page_no'];
        $page_size = $dataProtocol['data']['page_size'];
        $type = $dataProtocol['data']['type'];
        $user_id = $dataProtocol['data']['user_id'];
        if(empty($user_id)) {
            $user_id = 0;
        }
        if ($page_no <= 0) {
            $page_no = 1;
        }
        if ($page_size <= 0) {
            $page_size = 10;
        }
        if (!in_array($type, [1, 2])) {
            $error = '类型格式错误';
            return false;
        }
        $list = DynamicUtil::getDynamicList($user_id,$type, $page_no, $page_size);
        if(empty($list)) {
            $list = [];
        }
        $rstData['code'] = 0;
        $rstData['data'] = $list;
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['type', 'page_no', 'page_size'];
        $filesLabel = ['类型', '分页数', '记录数'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error = $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }

}