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

class WishModifyByView implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        $wish->view_num += 1;
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望阅读量更新失败');
        }
        return true;
    }
} 