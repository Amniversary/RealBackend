<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午4:05
 */

namespace frontend\business;


use common\models\Books;
use yii\db\Query;

class BookUtil
{
    /**
     * 根据id 获取书籍信息
     * @param $id
     * @return null|Books
     */
    public static function GetBook($id)
    {
        return Books::findOne(['book_id'=>$id]);
    }


    /**
     * 保存书籍记录信息
     * @param $model
     * @param $error
     * @return bool
     */
    public static function SaveBooks($model, &$error)
    {
        if(!($model instanceof Books)) {
            $error = '不是书籍记录对象';
            return false;
        }
        if(!$model->save()) {
            $error = '保存书籍记录失败';
            \Yii::error($error.' :'.var_export($model->getErrors(),true));
            return false;
        }
        return true;
    }

    /**
     * 获取书籍列表
     * @param $id
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
    public static function GetBookList($id, $page_no = 1, $page_size = 20)
    {
        $query = (new Query())
            ->select(['book_id as id', 'title', 'status', 'create_time', 'update_time'])
            ->from('wc_books')
            ->where(['weekly_id'=>$id])
            ->offset(($page_no - 1) * $page_size)
            ->limit($page_size)
            ->all();
        return $query;
    }
}