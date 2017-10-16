<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午6:02
 */
namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;

class UpdateArticle implements IApiExecute
{

    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $Article = ArticlesUtil::GetArticleById($id);
        if (empty($Article) || !isset($Article)) {
            $error = '章节信息记录不存在';
            return false;
        }

        $Article->title = $dataProtocol['data']['title'];
        $Article->pic = $dataProtocol['data']['pic'];
        $Article->description = $dataProtocol['data']['description'];
        $Article->url = $dataProtocol['data']['url'];
        $Article->status = $dataProtocol['data']['status'];
        if (!ArticlesUtil::SaveArticles($Article, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocal, &$error)
    {
        $fields = ['id', 'title', 'pic', 'description', 'url'];
        $fieldLabels = ['文章id', '文章名称', '文章图片', '文章描述', '跳转链接'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        if (!isset($dataProtocal['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}