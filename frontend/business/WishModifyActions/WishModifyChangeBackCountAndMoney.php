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

class WishModifyChangeBackCountAndMoney implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(!isset($params['back_money']) || empty($params['back_money']))
        {
            $error = '参数错误，back_money不能为空';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if($wish->back_status > 2)
        {
            $error = '已修退款过不允许再修改';
            return false;
        }

        $back_money = $params['back_money'];
        $wish->back_money += $back_money;
        $wish->back_count += 1;
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望退款金额状态更新失败');
        }
        return true;
    }
} 