<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/6/18
 * Time: 16:10
 */

namespace frontend\business;


use common\models\MultiUpdateContent;
use common\models\UpdateContent;
use yii\log\Logger;

class MultiUpdateContentUtil
{
    /**
     * 根据app_id和module_id获取更新模块信息
     * @param $app_id
     * @param $module_id
     */
    public static function GetInfoByAppIdAndMoudleId($app_id,$module_id)
    {
        return MultiUpdateContent::findOne(['app_id'=>$app_id,'module_id'=>$module_id]);
    }

    /**
     * 检测是否审核中
     * @param $app_id
     * @param $module_id
     * @param $version_id
     * @param $error
     */
    public static function CheckVersionInCheck($app_id,$module_id,$version_id)
    {
        $record = MultiUpdateContent::findOne(['app_id'=>$app_id,'module_id'=>$module_id]);
        if(!isset($record))
        {
            $error = '找不到版本记录';
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }
        if($record->app_version_inner > intval($version_id))
        {
            return false;
        }
        if($record->app_version_inner < intval($version_id))
        {
            return true;
        }
        if($record->status === 2)
        {
            return false;
        }
        return true;
    }

    /**
     * 获取模块更新所有记录
     */
    public static function GetAllUpdateContent($app_id)
    {
        $data = self::GetUpdateContentAll($app_id);
        $rst = [];
        foreach($data as $one)
        {
            $rst[$one->module_id]=[
                'version'=>$one->app_version_inner,
                'discribtion'=>$one->discribtion,
                'link'=>$one->link,
                'update_content'=>$one->update_content,
                'force_update'=>$one->force_update,
                'is_register'=>$one->is_register,
            ];
        }
        return $rst;
    }

    public static function GetUpdateContentById($update_id)
    {
        return MultiUpdateContent::findOne('update_id='.$update_id);
    }

    public static function SaveMultiUpdateContent($MultiUpdateContent,&$error)
    {
        if(!($MultiUpdateContent instanceof MultiUpdateContent))
        {
            $error = '不是多版本模块更新表记录';
            return false;
        }
        if(!$MultiUpdateContent->save())
        {
            $error = '多版本模块更新表保存失败';
            \Yii::getLogger()->log($error.' :'.var_export($MultiUpdateContent->getErrors(),true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }


    public static function CheckAppIdIsContent($app_id)
    {
        return MultiUpdateContent::findOne(['app_id'=>$app_id]);
    }


    /**
     * @param $app_id
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function GetUpdateContentAll($app_id)
    {
        $data = MultiUpdateContent::find()->select(['app_id','module_id','discribtion','app_version_inner','link','update_content','force_update','is_register'])
            ->where(['app_id'=>$app_id])
            ->all();

        return $data;
    }
}