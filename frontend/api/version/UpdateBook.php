<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:09
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\BookUtil;

class UpdateBook implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)){
           return false;
        }
        $id = $data['data']['id'];
        $book = BookUtil::GetBook($id);
        $book->title = $data['data']['title'];
        $book->status = $data['data']['status'];
        $book->update_time = date('Y-m-d H:i:s');
        if(!BookUtil::SaveBooks($book, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocal,&$error){
        $fields = ['id','title'];
        $fieldLabels = ['书籍id', '书籍标题'];
        $len = count($fields);
        for($i = 0; $i < $len; $i++)
        {
            if(!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        if(!isset($dataProtocal['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}