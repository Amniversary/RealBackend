<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/4
 * Time: 11:08
 */

namespace backend\models;


use yii\base\Model;

class UserGoldsAccountForm extends Model
{
    

    /**
     * 获取帐户状态
     */
    public static function GetGoldsAccountStatus($account_status){
        switch(intval($account_status))
        {
            case 1:
                $rst = '正常';
                break;
            case 2:
                $rst = '冻结';
                break;
            case 3:
                $rst = '异常';
                break;
        }
        return $rst;
    }
}