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

class WishModifyChangeBackStatus implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['back_status']) || empty($params['back_status']))
        {
            $error = '参数错误，back_status不能为空';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if($wish->back_status > 2)
        {
            $error = '退款状态错误，已经处理过';
            return false;
        }
        $back_status = $params['back_status'];
        $wish->back_status = $back_status;
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望结束状态更新失败');
        }
        return true;
    }
} 