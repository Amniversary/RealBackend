<?php
namespace common\models;

use backend\business\SetUserCheckNoUtil;
use backend\business\UserMenuUtil;
use common\components\PhpLock;
use Yii;
use yii\base\Model;
use yii\log\Logger;

/**
 * Login form
 */
class  LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

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

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'rememberMe' => '记住我'
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
            $user = $this->getUser();
            $backend = User::findByBackendUsername($user->backend_user_id, 1);
            if (empty($user)) {
                $this->addError($attribute, '该账号不存在');
                return;
            }
            if (empty($backend)) {
                $this->addError($attribute, '该账号没有登录权限');
                return;
            }
            if (isset($user) && $user->status === 0) {
                $this->addError($attribute, '您已被管理员禁用');
                return;
            }
            if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或密码错误');
                return;
            }
            $this->_user = $user;
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            //TODO：登录成功后保存权限到memcache
            $user_id = $this->_user->backend_user_id;
            $key = 'user_menu_' . strval($user_id);
            $cnt = \Yii::$app->cache->get($key);
            if ($cnt === false || empty(json_decode($cnt, true))) {
                $phpLock = new PhpLock('get_user_menu_' . $user_id);
                $phpLock->lock();
                $cnt = \Yii::$app->cache->get($key);
                if ($cnt === false || empty(json_decode($cnt, true))) {
                    $innerMenu = [];
                    $menus = UserMenuUtil::GetUserMenu($user_id, 0, $innerMenu);
                    $menuStr = json_encode($menus);
                    $powerStr = json_encode($innerMenu);
                    \Yii::$app->cache->set($key, $menuStr);
                    $key_power = 'user_power_' . $user_id;
                    \Yii::$app->cache->set($key_power, $powerStr);
                }
                $phpLock->unlock();
            }
            $this->_user->update_at = time();
            $this->_user->save();

            //TODO: session系统头像
            \Yii::$app->session['pic_' . $user_id] = empty($this->_user->pic) ? 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png' : $this->_user->pic;
            return Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }
}
