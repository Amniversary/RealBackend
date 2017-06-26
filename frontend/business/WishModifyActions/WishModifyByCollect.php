<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:07
 */

namespace frontend\business\WishModifyActions;


use yii\base\Exception;
use common\models\Wish;
use frontend\business\WishUtil;
use yii\log\Logger;

class WishModifyByCollect implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        $wish->collect_num += 1;
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望收藏数量更新失败');
        }
        return true;
    }
} 