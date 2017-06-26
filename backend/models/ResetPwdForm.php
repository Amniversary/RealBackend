<?php
namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ResetPwdForm extends Model
{
    public $newpwd;
    public $repeatpwd;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['newpwd', 'repeatpwd'], 'required'],
            // password is validated by validatePassword()
            ['newpwd', 'validatePassword'],
            [['newpwd','repeatpwd'],'match','pattern'=>'/^[a-zA-Z0-9_]+$/','message'=>'密码必须是字母、数字、下划线'],
            [['newpwd','repeatpwd'],'string','length'=>[6,20],'message'=>'密码至少6位'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'newpwd'=>'新密码',
            'repeatpwd'=>'重复密码',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->newpwd !== $this->repeatpwd) {
                $this->addError($attribute, '两次输入密码不一致');
                return;
            }
        }
    }

}
