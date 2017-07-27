<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/27
 * Time: 下午3:14
 */

namespace backend\business;


use common\models\Resource;

class ResourceUtil
{
    /**
     * 根据APPid 获取对应资源
     * @param $app_id
     * @param $msg_id
     * @return null|Resource
     */
    public static function GetResource($app_id,$msg_id)
    {
        return Resource::findOne(['app_id'=>$app_id,'msg_id'=>$msg_id]);
    }
}