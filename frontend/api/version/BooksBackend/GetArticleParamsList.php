<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/29
 * Time: 下午4:44
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;
use yii\db\Query;

class GetArticleParamsList implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        $query = (new Query())
            ->select(['id', 'title', 'qrcode_url', 'create_time', 'update_time'])
            ->from('wc_article_system_params')
            ->all();
        if (empty($query)) {
            $query = [];
        }
        $res = [];

        foreach ($query as $item) {
            $carousel = [];
            $Params = ArticlesUtil::GetArticleMenu($item['id']);
            foreach($Params as $list) {
                $carousel[] = $list['pic_url'];
            }
            $res[] = [
                'id' => $item['id'],
                'title'=> $item['title'],
                'qrcode_url' => $item['qrcode_url'],
                'carousel' =>$carousel,
                'create_time'=>$item['create_time'],
                'update_time'=>$item['update_time'],
            ];
            unset($carousel);
        }

        $rstData['code'] = 0;
        $rstData['data'] = $res;
        return true;
    }
}