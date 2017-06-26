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

class WishModifyChangeAcceptForWishMoneyToBalance implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        //设置成已完成，已打款
        $sql = '
        update my_wish set to_balance=2,finish_status=2,finish_time=now() where  wish_id=:wid and is_finish=2 and status=1 and to_balance = 3 and back_status = 1 and finish_status < 3
        ';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':wid'=>$wish->wish_id
        ])->execute();
        if($rst <= 0)
        {

            $error = '愿望状态错误，设置失败';
            \Yii::getLogger()->log($error.' sql:'.$sql,Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 