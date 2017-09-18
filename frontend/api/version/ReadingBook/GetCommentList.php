<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午3:45
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;

class GetCommentList implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)) {
            \Yii::error($error);
            return false;
        }
        $page_no = $data['data']['page_no'];
        $page_size = $data['data']['page_size'];
        $dynamic_id = $data['data']['dynamic_id'];
        if($page_no <= 0) $page_no = 1;
        if($page_size <= 0) $page_size = 10;
        $Dynamic = DynamicUtil::getDynamicById($dynamic_id);
        if(empty($Dynamic)) {
            $error = '动态记录不存在或已删除';
            return false;
        }
        $Comment_list = DynamicUtil::getCommentList($Dynamic->dynamic_id, 0, $page_no, $page_size);
        $rstData['code'] = 0;
        $rstData['data'] = $Comment_list;

        return true;
    }

    private function check_params($dataTotal, &$error)
    {
        $files = ['dynamic_id', 'page_no', 'page_size'];
        $filesLabel = ['动态Id', '分页数', '记录数'];
        $len = count($files);
        for($i = 0; $i < $len; $i++ ){
            if(!isset($dataTotal['data'][$files[$i]]) || empty($dataTotal['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}