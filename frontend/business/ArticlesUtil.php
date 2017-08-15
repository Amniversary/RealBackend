<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午5:58
 */

namespace frontend\business;


use common\models\Articles;
use yii\db\Query;

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

    /**
     * 获取章节列表
     * @param $bookId
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
    public static function GetArticles($bookId, $page_no = 1, $page_size = 20)
    {
        $query = (new Query())
            ->from('wc_articles')
            ->select(['book_id as id', 'title', 'pic', 'description', 'url', 'status', 'create_time', 'update_time'])
            ->where(['book_id'=>$bookId])
            ->offset(($page_no - 1) * $page_size)
            ->limit($page_size)
            ->all();

       return $query;
    }

    /**
     * 返回章节总数
     * @param $bookId
     * @return bool|string
     */
    public static function GetArticleCount($bookId)
    {
        return Articles::find()->select(['count(1) as num'])->where(['book_id'=>$bookId])->limit(1)->scalar();
    }
}