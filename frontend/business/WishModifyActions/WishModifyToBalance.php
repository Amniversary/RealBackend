<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:06
 */

namespace frontend\business\WishModifyActions;

use common\models\Wish;
use frontend\business\WishUtil;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyToBalance implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        if($wish->is_finish !== 2)
        {
            $error = '未完成愿望不能提取金额';
            return false;
        }
        //转余额进入审核状态，判断状态改变
        if($wish->to_balance === 2)//已转
        {
            $error = '已经转到余额，无需再转';
            return false;
        }
        $wish->to_balance = 2;//设置转移状态
        if($wish->finish_status === 1) //将进行中设置成已经完成
        {
            $wish->finish_status = 2;
        }
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望信息更新失败');
        }
        return true;
    }
} 