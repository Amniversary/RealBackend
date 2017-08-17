<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/15
 * Time: 下午5:40
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class WebGetIssue implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if (!$this->check_params($data, $error)) {
            return false;
        }
        $page_no = $data['data']['page_no'];
        if($page_no <= 0) {
            $page_no = 1;
        }
        $page_size = $data['data']['page_size'];
        if($page_size <= 0) {
            $page_size = 20;
        }
        $res = WeeklyUtil::GetWeeklyCacheData(false, $page_no, $page_size);

        $rstData['code'] = 0;
        $rstData['data'] = $res;
        return true;
    }


    private function check_params($dataProtocal,&$error){
        $fields = ['page_no','page_size'];
        $fieldLabels = ['分页数', '记录数'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}