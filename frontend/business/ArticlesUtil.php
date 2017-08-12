<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午5:58
 */

namespace frontend\business;


use common\models\Articles;

class ArticlesUtil
{
    /**
     * 根据id 获取文章信息
     * @param $id
     * @return null|Articles
     */
    public static function GetArticleById($id)
    {
        return Articles::findOne(['id'=>$id]);
    }


    /**
     * 保存文章记录信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveArticles($model , &$error)
    {
        if(!($model instanceof Articles)) {
            $error = '不是文章记录对象';
            return false;
        }

        if(!$model->save()) {
            $error = '保存文章记录失败';
            \Yii::error($error .' :'.var_export($model->getErrors(),true));
            return false;
        }

        return true;
    }

    public static function GetArticles()
    {

    }
}