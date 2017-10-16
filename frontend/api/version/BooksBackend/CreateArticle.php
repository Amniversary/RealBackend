<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午5:51
 */

namespace frontend\api\version\BooksBackend;


use common\models\Articles;
use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;
use frontend\business\BookUtil;

class CreateArticle implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $book_id = $dataProtocol['data']['book_id'];
        $book = BookUtil::GetBook($book_id);
        if (empty($book) || !isset($book)) {
            $error = '书籍记录信息不存在';
            return false;
        }
        $model = new Articles();
        $model->book_id = $book->book_id;
        $model->title = $dataProtocol['data']['title'];
        $model->pic = $dataProtocol['data']['pic'];
        $model->description = $dataProtocol['data']['description'];
        $model->url = $dataProtocol['data']['url'];
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        $model->status = 1;
        if (!ArticlesUtil::SaveArticles($model, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }


    private function check_params($dataProtocol, &$error)
    {
        $fields = ['book_id', 'title', 'pic', 'description', 'url'];
        $fieldLabels = ['书籍id', '文章名称', '文章图片', '文章描述', '文章跳转链接'];
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