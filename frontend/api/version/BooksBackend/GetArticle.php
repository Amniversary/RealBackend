<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/14
 * Time: 上午10:45
 */

namespace frontend\api\version\BooksBackend;


use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;

class GetArticle implements IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(empty($data['data']['article_id']) || !isset($data['data']['article_id'])){
            $error = '章节id , 不能为空';
            return false;
        }
        $id = $data['data']['article_id'];
        $article = ArticlesUtil::GetArticleById($id);
        if (empty($article)) {
            $error = '获取章节失败, 章节已删除或不存在';
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = [
            'id'         => intval($article->id),
            'title'      => $article->title,
            'pic'        => $article->pic,
            'description'=> $article->description,
            'url'        => $article->url,
            'status'     => intval($article->status),
            'create_time'=> $article->create_time,
            'update_time'=> $article->update_time
        ];
        return true;
    }
}