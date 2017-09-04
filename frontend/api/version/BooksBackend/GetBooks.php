<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:13
 */

namespace frontend\api\version\BooksBackend;

use frontend\api\IApiExecute;
use frontend\business\BookUtil;
use frontend\business\WeeklyUtil;

class GetBooks implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($data, $error)) {
            return false;
        }
        $page_no = $data['data']['page_no'];
        $page_size = $data['data']['page_size'];
        if ($page_no <= 0) {
            $page_no = 1;
        }
        if ($page_size <= 0) {
            $page_size = 1;
        }
        $id = $data['data']['weekly_id'];
        $Weekly = WeeklyUtil::GetWeekly($id);
        if (empty($Weekly)) {
            $error = '周刊记录信息不存在';
            return false;
        }
        $bookList = BookUtil::GetBookList($Weekly->weekly_id, $page_no, $page_size);
        if (empty($bookList)) {
            $bookList = [];
        }
        $count = BookUtil::GetBookCount($Weekly->weekly_id);

        $rstData['code'] = 0;
        $rstData['count'] = intval($count);
        $rstData['data']['list'] = $bookList;
        return true;
    }

    private function check_params($dataProtocal, &$error)
    {
        $fields = ['weekly_id', 'page_no', 'page_size'];
        $fieldLabels = ['周刊id', '分页数', '记录数'];
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