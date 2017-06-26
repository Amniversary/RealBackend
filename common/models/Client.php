<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%_client}}".
 *
 * @property integer $client_id
 * @property string $nick_name
 * @property string $unique_no
 * @property string $client_no
 * @property integer $register_type
 * @property string $phone_no
 * @property string $city
 * @property integer $age
 * @property string $pic
 * @property string $main_pic
 * @property string $middle_pic
 * @property string $icon_pic
 * @property integer $is_pic_deal
 * @property string $sign_name
 * @property string $device_no
 * @property integer $device_type
 * @property string $sex
 * @property integer $status
 * @property string $create_time
 * @property integer $is_bind_weixin
 * @property integer $is_bind_alipay
 * @property integer $is_inner
 * @property integer $is_contract
 * @property integer $is_centification
 * @property string $cash_rite
 * @property string $getui_id
 * @property integer $client_type
 * @property  string $modify_time
 * @property string $vcode
 * @property string $app_id
 * @property string $token
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class Client extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no'], 'required'],
            [['register_type','is_pic_deal','client_type', 'age', 'device_type', 'status', 'is_bind_weixin', 'is_bind_alipay', 'is_inner', 'is_contract', 'is_centification'], 'integer'],
            [['create_time','middle_pic','icon_pic','pic','main_pic'], 'safe'],
            [['nick_name','getui_id', 'unique_no', 'client_no', 'city',  'sign_name', 'device_no', 'cash_rite', 'vcode', 'app_id', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['phone_no', 'sex'], 'string', 'max' => 20],
            [['token'], 'string','max'=>300],
            [['client_no'], 'unique'],
            [['unique_no'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'client_id' => '用户 ID',
            'client_type'=>'用户类型',
            'client_no' => '用户蜜播 ID',
            'nick_name' => '昵称',
            'unique_no' => '唯一id',
            'register_type' => '注册类型',
            'city' => '城市',
            'age' => '年龄',
            'pic' => '头像',
            'main_pic' => '封面图片',
            'sign_name' => '签名',
            'phone_no' => '手机号码',
            'device_no' => '设备号',
            'device_type' => '设备类型',
            'sex' => '性别',
            'status' => '状态',
            'create_time' => '创建时间',
            'is_bind_weixin' => '绑定微信',
            'is_bind_alipay' => '绑定支付宝',
            'is_inner' => '是否内部',
            'is_contract' => '是否签约',
            'is_centification' => '是否认证',
            'cash_rite' => '签约提现率',
            'modify_time'=>'登陆修改时间',
            'vcode'=>'验证码',
            'app_id'=>'appId',
            'token'=>'融云token',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }

    /**
     * 获取认证类型
     * @return string
     */
    public function GetIsCentification()
    {
        switch(intval($this->is_centification))
        {
            case 1:
                $rst = '未认证';
                break;
            case 2:
                $rst = '已认证';
                break;
            case 3:
                $rst = '审核中';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }

    /**
     * 获取签约类型
     * @return string
     */
    public function GetIsContract()
    {
        switch(intval($this->is_contract))
        {
            case 1:
                $rst = '未签约';
                break;
            case 2:
                $rst = '已签约';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }

    /**
     * 获取用户状态类型
     * @return string
     */
    public function GetUserStatus()
    {
        switch(intval($this->status))
        {
            case 1:
                $rst = '正常';
                break;
            case 0:
                $rst = '禁用';
                break;
            default:
                $rst = '未知';
                break;
        }
        return $rst;
    }

    /**
     * 获取用户类型
     * @return string
     */
    public function GetUserTypeName()
    {
        switch(intval($this->client_type))
        {
            case 1:
                $rst = '普通';
                break;
            case 2:
                $rst = '超管';
                break;
            case 3:
                $rst = '机器人';
                break;
            default:
                $rst = '未知';
                break;
        }
        return $rst;
    }


    /**
     * 获取用户注册类型
     */
    public function GetRegisterType()
    {
        switch(intval($this->register_type))
        {
            case 1:
                $rst = '手机';
                break;
            case 2:
                $rst = '微信';
                break;
            case 3:
                $rst = '微博';
                break;
            case 4:
                $rst = 'QQ';
                break;
            default:
                $rst = '未知类型';
                break;
        }
        return $rst;
    }
}
