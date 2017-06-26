<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/6/18
 * Time: 15:24
 */

namespace frontend\business;


use common\models\MultiVersionInfo;

class MultiVersionInfoUtil
{
    /**
     * 获取所有版本信息
     * @return array
     */
    public static function GetAllVersions()
    {
        $datas = MultiVersionInfo::find()
            ->select(['app_id','status','forbid_words'])->all();
        $rst = [];
        foreach($datas as $one)
        {
            $rst[$one->app_id]=[
                'status'=>$one['status'],
                'forbid_words'=>$one['forbid_words']
            ];
        }
        return $rst;
    }


    public static function GetVersionById($record_id)
    {
        return MultiVersionInfo::findOne('record_id='.$record_id);
    }
} 