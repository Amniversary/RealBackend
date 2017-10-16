<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:09
 */

namespace frontend\api\version\BooksBackend;


use common\models\BookParamsMenu;
use frontend\api\IApiExecute;
use frontend\business\BookUtil;

class UpdateBook implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $book = BookUtil::GetBook($id);
        $book->title = $dataProtocol['data']['title'];
        $book->status = $dataProtocol['data']['status'];
        $book->update_time = date('Y-m-d H:i:s');
        if (!BookUtil::SaveBooks($book, $error)) {
            return false;
        }
        (new BookParamsMenu())->deleteAll(['book_id'=>$id]);
        if(!empty($dataProtocol['data']['config_id']) || isset($dataProtocol['data']['config_id'])) {
            $BookParams = new BookParamsMenu();
            $BookParams->book_id = $id;
            $BookParams->system_id = $dataProtocol['data']['config_id'];
            $BookParams->save();
        }
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $fields = ['id', 'title'];
        $fieldLabels = ['书籍id', '书籍标题'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        if (!isset($dataProtocol['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}