<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/23
 * Time: 下午2:20
 */

namespace backend\models;


use yii\base\Model;

class SignUserFrom extends Model
{
    public $app_id;
    public $user_id;
    public $sign_num;
    public $update_time;
    public $user_name;


}