<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午6:02
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\ArticlesUtil;

class UpdateArticle implements IApiExecute
{

    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)) {
            return false;
        }
        $id = $data['data']['id'];
        $Article = ArticlesUtil::GetArticleById($id);
        if(empty($Article) || !isset($Article)) {
            $error = '章节信息记录不存在';
            return false;
        }

        $Article->title = $data['data']['title'];
        $Article->pic = $data['data']['pic'];
        $Article->description = $data['data']['description'];
        $Article->url = $data['data']['url'];
        $Article->status = $data['data']['status'];
        if(!ArticlesUtil::SaveArticles($Article, $error)) {
           return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocal,&$error){
        $fields = ['id','title', 'pic','description', 'url','status'];
        $fieldLabels = ['文章id', '文章名称' ,'文章图片', '文章描述', '跳转链接', '状态值'];
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