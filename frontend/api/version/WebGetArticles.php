<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/16
 * Time: 上午10:24
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;
use frontend\business\BookUtil;

class WebGetArticles implements  IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if (!$this->check_params($data, $error )) {
            return false;
        }
        $page_no = $data['data']['page_no'];
        $page_size = $data['data']['page_size'];
        if($page_no <= 0) {
            $page_no = 1;
        }
        if($page_size <= 0 ) {
            $page_size = 20;
        }
        $id = $data['data']['book_id'];
        $book = BookUtil::GetBook($id);
        if(empty($book)) {
            $error = '书籍信息不存在';
            return false;
        }
        $Articles = ArticlesUtil::GetWebArticleList($id, $page_no, $page_size);
        $rst['book_id'] = intval($book->book_id);
        $rst['book_name'] = $book->title;
        $rst['list'] = $Articles;
        $rstData['code'] = 0;
        $rstData['data'] = $rst;
        \Yii::error(var_export($data,true));
        return true;
     }

    private function check_params($dataProtocal,&$error){
        $fields = ['book_id','page_no','page_size'];
        $fieldLabels = ['书籍id','分页数', '记录数'];
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