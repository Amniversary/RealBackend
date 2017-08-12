<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/11
 * Time: 下午3:19
 */

namespace backend\models;


use common\models\User;
use yii\base\Model;

class RealtechLoginSearch extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $type;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function  attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'rememberMe'=>'记住我'
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword(&$error)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if(empty($user)){
                $error = '该账号不存在';
                return false;
            }
            if(isset($user) && $user->status === 0) {
                $error = '您已被管理员禁用';
                return false;
            }
            if (!$user->validatePassword($this->password)) {
                $error = '用户名或密码错误';
                return false;
            }
            $this->_user = $user;
        }
        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        return true;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $status = $this->type;
            $this->_user = User::findByUsername($this->username,$status);
        }
        return $this->_user;
    }
}