<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/29
 * Time: 下午4:37
 */

namespace frontend\api\version\BooksBackend;


use common\models\ArticleSystemMenu;
use common\models\ArticleSystemParams;
use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;

class DeleteArticleParams implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (empty($dataProtocol['data']['id']) || !isset($dataProtocol['data']['id'])) {
            $error = '配置id , 不能为空';
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $params = ArticlesUtil::GetArticleParamsById($id);
        if (empty($params)) {
            $error = '删除失败, 配置信息不存在';
            return false;
        }

        (new ArticleSystemParams())->deleteAll(['id' => $id]);
        (new ArticleSystemMenu())->deleteAll(['system_id'=> $id]);
        $rstData['code'] = 0;
        return true;
    }
}