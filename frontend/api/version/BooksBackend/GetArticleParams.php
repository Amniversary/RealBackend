<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/29
 * Time: 下午5:43
 */

namespace frontend\api\version\BooksBackend;


use common\models\ArticleSystemMenu;
use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;

class GetArticleParams implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (empty($dataProtocol['data']['id']) || !isset($dataProtocol['data']['id'])) {
            $error = '配置id, 不能为空';
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $Params = ArticlesUtil::GetArticleParamsById($id);
        if (empty($Params)) {
            $error = '配置信息不存在';
            return false;
        }
        $carousel = ArticlesUtil::GetArticleMenu($id);

        $rst = [];
        foreach($carousel as $item) {
            $rst[] = $item['pic_url'];
        }
        $data = [
            'id' => $Params->id,
            'title' => $Params->title,
            'carousel' => $rst,
            'qrcode_url' => $Params->qrcode_url,
            'create_time' => $Params->create_time,
            'update_time' => $Params->update_time
        ];
        $rstData['code'] = 0;
        $rstData['data'] = $data;
        return true;
    }
}