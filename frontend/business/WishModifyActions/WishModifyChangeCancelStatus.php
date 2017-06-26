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

class WishModifyChangeCancelStatus implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['status']))
        {
            $error = '取消状态不能为空';
            return false;
        }
        $status = $params['status'];
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if($status === 0 && $wish->status  === 0)
        {
            $error = '该愿望已经取消，无需再取消';
            return false;
        }
        if($status === 1 && $wish->status === 1)
        {
            $error = '愿望并没取消，无需撤销取消';
            return false;
        }
        $wish->status = $status;

        //已经支持过的进入退款状态  审核通过后在进入退款状态
//        if(empty($wish->back_status) || $wish->back_status === 1)
//        {
//            $wish->back_status = 2;
//        }

        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望取消状态更新失败');
        }
        return true;
    }
} 