<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午3:19
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class GetWeekly implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $page_no = $dataProtocol['data']['page_no'];
        $page_size = $dataProtocol['data']['page_size'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        if ($page_size <= 0) {
            $page_size = 20;
        }
        $data = WeeklyUtil::GetWeeklyList($page_no, $page_size);
        if (empty($data)) {
            $data = [];
        }
        $count = WeeklyUtil::GetWeeklyCount();

        $rstData['code'] = 0;
        $rstData['data']['count'] = intval($count);
        $rstData['data']['list'] = $data;
        return true;
    }

    private function check_params($dataProtocal, &$error)
    {
        $fields = ['page_no', 'page_size'];
        $fieldLabels = ['分页数', '记录数'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}