<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午3:08
 */

namespace frontend\business;


use common\models\Weekly;
use yii\db\Query;

class WeeklyUtil
{
    /**
     * 保存周刊模型记录
     */
    public static function SaveWeekly($model, &$error){
        if(!($model instanceof Weekly)){
            $error = '不是周刊模型记录对象';
            return false;
        }
        if(!$model->save()) {
            $error = '保存周刊记录失败';
            \Yii::error($error. ' :'.var_export($model->getErrors(),true));
            return false;
        }
         return true;
    }

    /**
     * 根据id 获取周刊记录
     * @param $id
     * @return null|Weekly
     */
    public static function GetWeekly($id){
        return Weekly::findOne(['weekly_id'=>$id]);
    }

    /**
     * 获取周刊列表
     * @param int $pageNo
     * @param int $page_size
     * @return array
     */
    public static function GetWeeklyList($pageNo = 1, $page_size = 20)
    {
        $query = (new Query())
            ->select(['weekly_id as id', 'title', 'weeks', 'status', 'create_time', 'update_time'])
            ->from('wc_weekly')
            ->offset(($pageNo - 1) * $page_size)
            ->limit($page_size)
            ->all();

        return $query;
    }
}