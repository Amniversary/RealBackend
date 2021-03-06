<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/15
 * Time: 下午5:40
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class WebGetIssue implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $page_no = $dataProtocol['data']['page_no'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        $page_size = $dataProtocol['data']['page_size'];
        if ($page_size <= 0) {
            $page_size = 20;
        }
        $res = WeeklyUtil::GetWeeklyCacheData(false, $page_no, $page_size);

        $rstData['code'] = 0;
        $rstData['data'] = $res;
        return true;
    }


    private function check_params($dataProtocol, &$error)
    {
        $fields = ['page_no', 'page_size'];
        $fieldLabels = ['分页数', '记录数'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}