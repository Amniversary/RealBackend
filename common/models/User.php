<?php
namespace common\models;

use common\components\Des3Crypt;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\log\Logger;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $backend_user_id
 * @property string $username
 * @property string $pwd_hash
 * @property string $pwd_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property string $pic
 * @property string $phone
 * @property integer $create_at
 * @property integer $update_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CUSTOM = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_user}}';
    }

    public function attributeLabels()
    {
        return [
            'username' => '用户名',
            'password' => '密码',
            'email' => '邮箱',
            'status' => '状态',
            'phone' => '手机号',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password', 'phone'], 'required'],
            [['username', 'email'], 'unique'],
            ['phone', 'match', 'pattern' => '/^1[34578]\d{9}$/' , 'message' => '手机格式不正确'],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', 'message' => '用户名必须是汉字、字母、数字、下划线'],
            ['password', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => '密码必须是字母、数字、下划线', 'on' => 'create'],
            ['password', 'string', 'length' => [6, 20], 'message' => '密码至少6位', 'on' => 'create'],
            ['email', 'email'],
            [['pic'], 'safe'],
            [['status'], 'default', 'value' => '0'],
            [['status'], 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['backend_user_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @param int $status
     * @return User|null
     */
    public static function findByUsername($username, $status = self::STATUS_ACTIVE)
    {
        return static::findOne(['and', ['username' => $username]]);
    }

    /**
     * Finds backendMenu by user
     *
     * @param int $user_id
     * @param int $status
     * @return null|BackendMenu
     */
    public static function findByBackendUsername($user_id, $status)
    {
        return BackendMenu::findOne(['and', ['user_id'=>$user_id], ['or', ['backend_id'=>$status] , ['backend_id'=> 0]]]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'pwd_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function GetStatusName()
    {
        $rst = '';
        switch ($this->status) {
            case 0:
                $rst = '禁用';
                break;
            case 1:
                $rst = '正常';
                break;
        }
        return $rst;
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $len = strlen(strval(time()));
        $key = \Yii::$app->params['pwd_crypt_key'];
        $soucePwd = Des3Crypt::des_decrypt($this->password, $key);
        $soucePwd = substr($soucePwd, 0, strlen($soucePwd) - $len);
        if ($password !== $soucePwd) {
            \Yii::error('password:' . $password . '; soucePwd:' . $soucePwd);
            return false;
        }

        return Yii::$app->security->validatePassword($password, $this->pwd_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->pwd_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->pwd_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->pwd_reset_token = null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->status = 1;
                $this->setPassword($this->password);
                $crypt_key = \Yii::$app->params['pwd_crypt_key'];
                $pwd = $this->password . strval(time());
                $this->password = Des3Crypt::des_encrypt($pwd, $crypt_key);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 重置密码
     */
    public function ResetPwd()
    {
        $this->setPassword($this->password);
        $crypt_key = \Yii::$app->params['pwd_crypt_key'];
        $pwd = $this->password . strval(time());
        $this->password = Des3Crypt::des_encrypt($pwd, $crypt_key);
    }

    /**
     * 后台名称
     */
    public function BackendName($status)
    {
        return Yii::$app->params['backend_list'][$status];
    }
}
