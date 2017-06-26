<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:07
 */

namespace frontend\business\WishModifyActions;


use common\models\Wish;
use frontend\business\WishUtil;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyChangeFinishStatus implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['finish_status']) || empty($params['finish_status']))
        {
            $error = '参数错误，finish_status不能为空';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if($wish->finish_status > 1)
        {
            $error = '已修改过状态不允许再修改';
            return false;
        }
        $finish_status = $params['finish_status'];
        $wish->finish_status = $finish_status;
        $wish->finish_time = date('Y-m-d H:i:s');
        if($wish->finish_status == 4)
        {
            if(empty($wish->back_status) || $wish->back_status === 1)
            {
                $wish->back_status = 2;
            }
        }
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望结束状态更新失败');
        }
        return true;
    }
} 