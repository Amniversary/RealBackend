<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/5/12
 * Time: 18:52
 */

namespace backend\models;


use yii\base\Model;

class PushMessageForm extends Model
{
    public $message='';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['message'], 'required'],
            ['message','string','max'=>150]
        ];
    }

    public function  attributeLabels()
    {
        return [
            'message'=>'将下面消息推送给所有蜜播用户'
        ];
    }
} 