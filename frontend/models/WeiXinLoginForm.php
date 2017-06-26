<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class WeiXinLoginForm extends Model
{
    public $phone_no;
    public $vcode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['phone_no','vcode'], 'required'],
            ['phone_no', 'filter', 'filter' => 'trim'],
            ['phone_no','match','pattern'=>'/^1\d{10}$/u','message'=>'手机号不正确'],
            ['phone_no', 'string', 'min' => 11, 'max' => 11],
            ['vcode', 'string', 'min' => 4],

        ];
    }

    public function  attributeLabels()
    {
        return [
            'phone_no'=>'手机号',
            'vcode'=>'验证码',
        ];
    }
}
