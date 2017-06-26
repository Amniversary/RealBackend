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

class WishModifySomeAttrubutes implements IWishModify
{
    public function WishModify($wish,&$error,$params=[])
    {
        if(!($wish instanceof Wish))
        {
            $error='不是愿望对象';
            return false;
        }
        if(empty($params) || !is_array($params))
        {
            $error = '参数不能为空';
            return false;
        }
        $fields = ['wish_name','discribtion','wish_type_id','wish_type','pic1','pic2','pic3','pic4','pic5','pic6','publish_user_name','publish_user_phone'];
        foreach($params as $key => $value)
        {
            if(!in_array($key, $fields))
            {
                unset($params[$key]);//去除无关字段
            }
        }
        if(empty($params))
        {
            return true;//无需更新
        }
        $wish = WishUtil::GetWishRecordById($wish->wish_id);
        $wish->attributes =  $params;
        if(!$wish->save())
        {
            \Yii::getLogger()->log(var_export($wish->getErrors(), true),Logger::LEVEL_ERROR);
            throw new Exception('愿望更新属性失败');
        }
        return true;
    }
} 