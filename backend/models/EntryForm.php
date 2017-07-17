<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/14
 * Time: 下午3:18
 */

namespace backend\models;


use yii\base\Model;

class EntryForm extends Model
{
    public $name;
    public $email;
    public function rules()
    {
        return [
            [['name','email'], 'required'],
            ['email','email'],
        ];
    }
}