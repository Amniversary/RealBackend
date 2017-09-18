<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/15
 * Time: 下午6:01
 */

namespace backend\models;


use yii\base\Model;



class CompareForm extends Model
{
    public $compare_one;
    public $compare_two;

    public function rules()
    {
        return [
            [['compare_one', 'compare_two'] , 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'compare_one' => '公众号1',
            'compare_two' => '公众号2',
        ];
    }
}