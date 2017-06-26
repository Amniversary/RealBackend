<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:06
 */

namespace frontend\business\WishModifyActions;

use common\models\Wish;
use yii\base\Exception;
use yii\log\Logger;

class WishModifyToBalanceForCheck implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        //已实现、未提交申请、为取消、为退款才能申请
        $sql = 'update my_wish set to_balance=3, finish_status=2
where wish_id=:wid and is_finish=2 and back_status=1 and to_balance=1 and status=1 and publish_user_id=:uid';
        $rst = \Yii::$app->db->createCommand($sql,[
            ':wid'=>$wish->wish_id,
            ':uid'=>$params['user_id']
        ])->execute();
        if($rst <= 0)
        {
            $error = '愿望状态错误，提现申请失败';
            \Yii::getLogger()->log($error.' wish_id:'.strval($wish->wish_id),Logger::LEVEL_ERROR);
            throw new Exception($error);
        }
        return true;
    }
} 