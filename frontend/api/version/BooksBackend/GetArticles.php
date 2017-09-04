<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午6:06
 */

namespace frontend\api\version\BooksBackend;

use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;
use frontend\business\BookUtil;

class GetArticles implements IApiExecute
{

    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($data, $error)) {
            return false;
        }
        $book_id = $data['data']['book_id'];
        $page_no = $data['data']['page_no'];
        $page_size = $data['data']['page_size'];
        $book = BookUtil::GetBook($book_id);
        if (empty($book) || !isset($book)) {
            $error = '书籍记录信息不存在';
            return false;
        }
        if ($page_no <= 0) {
            $page_no = 1;
        }
        if ($page_size <= 0) {
            $page_size = 1;
        }
        $articles = ArticlesUtil::GetArticles($book->book_id, $page_no, $page_size);
        if (empty($articles)) {
            $articles = [];
        }
        $count = ArticlesUtil::GetArticleCount($book_id);

        $rstData['code'] = 0;
        $rstData['count'] = intval($count);
        $rstData['data']['list'] = $articles;
        return true;
    }


    private function check_params($dataProtocal, &$error)
    {
        $fields = ['book_id', 'page_no', 'page_size'];
        $fieldLabels = ['书籍id', '分页数', '记录数'];
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