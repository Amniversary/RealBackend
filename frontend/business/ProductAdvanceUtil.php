<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 12:54
 */

namespace frontend\business;


use common\models\ProductAdvance;
use yii\log\Logger;

class ProductAdvanceUtil
{

    /**
     * 保存产品建议
     * @param $attrs
     * @param $error
     * @return bool
     */
    public static function AddProductAdvance($attrs, &$error)
    {
        $model = self::GetNewModel($attrs);
        if(!$model->save())
        {
            \Yii::getLogger()->log(var_export($model->getErrors(), true),Logger::LEVEL_ERROR);
            $error = '产品建议保存失败';
            return false;
        }
        return true;
    }

    /**
     * 获取产品建议新模型
     * @param $attrs
     * @return ProductAdvance
     */
    public static function GetNewModel($attrs)
    {
        $model = new ProductAdvance();
        $model->attributes = $attrs;
        return $model;
    }
} 