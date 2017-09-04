<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/14
 * Time: 上午10:42
 */

namespace frontend\api\version\BooksBackend;

use common\models\BookParamsMenu;
use frontend\api\IApiExecute;
use frontend\business\BookUtil;

class GetBook implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (empty($data['data']['book_id']) || !isset($data['data']['book_id'])) {
            $error = '书籍Id , 不能为空';
            return false;
        }
        $id = $data['data']['book_id'];
        $book = BookUtil::GetBook($id);
        if (empty($book)) {
            $error = '获取书籍失败 , 记录已删除或不存在';
            return false;
        }
        $BookParams = BookParamsMenu::findOne(['book_id'=>$id]);
        $rstData['code'] = 0;
        $rstData['data'] = [
            'id' => $book->book_id,
            'title' => $book->title,
            'config_id' => empty($BookParams) ? '': intval($BookParams->system_id),
            'status' => $book->status,
            'create_time' => $book->create_time,
            'update_time' => $book->update_time
        ];
        return true;
    }
}