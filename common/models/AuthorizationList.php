<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_authorization_list}}".
 *
 * @property integer $record_id
 * @property string $authorizer_appid
 * @property string $authorizer_access_token
 * @property string $authorizer_refresh_token
 * @property string $func_info
 * @property integer $status
 * @property integer $alarm_status
 * @property integer $user_id
 * @property string $nick_name
 * @property string $head_img
 * @property integer $service_type_info
 * @property integer $verify_type_info
 * @property string $user_name
 * @property string $alias
 * @property string $qrcode_url
 * @property string $business_info
 * @property integer $idc
 * @property string $principal_name
 * @property string $signature
 * @property string $authorization_info
 * @property string $create_time
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class AuthorizationList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_authorization_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['authorizer_appid', 'authorizer_access_token', 'authorizer_refresh_token', 'authorization_info'], 'required'],
            [['status', 'alarm_status', 'user_id', 'service_type_info', 'verify_type_info', 'idc'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['authorizer_appid', 'nick_name', 'user_name', 'alias', 'principal_name',  'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['authorizer_access_token', 'authorizer_refresh_token', 'head_img', 'qrcode_url', 'business_info'], 'string', 'max' => 300],
            [['func_info', 'authorization_info','signature'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => '记录 ID',
            'authorizer_appid' => '授权AppId',
            'authorizer_access_token' => '授权令牌',
            'authorizer_refresh_token' => '授权刷新令牌凭证',
            'func_info' => '授权权限集json格式',
            'status' => '状态',
            'alarm_status' => '告警状态',
            'user_id' => '操作人ID',
            'nick_name' => '授权方昵称',
            'head_img' => '授权方头像',
            'service_type_info' => '公众号类型',
            'verify_type_info' => '认证类型',
            'user_name' => '公众号原始ID',
            'alias' => '公众微信号',
            'qrcode_url' => '二维码Url',
            'business_info' => '功能开通信息',
            'idc' => 'Idc',
            'principal_name' => '公众号主体类型',
            'signature' => '功能签名',
            'authorization_info' => '授权信息主体',
            'create_time' => '授权时间',
            'update_time' => '更新时间',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }

    /**
     * 获取公众号类型
     * @param $status
     * @return string
     */
    public static function getServiceTypeInfo($status){
        switch (intval($status)){
            case 0: $rst = '订阅号';break;
            case 1: $rst = '订阅号';break;//代表老账号升级后都订阅号
            case 2: $rst = '服务号';break;
            default: $rst = '未知类型';break;
        }
        return $rst;
    }

    /**
     * 获取公众号认证类型
     * @param $status
     * @return string
     */
    public static function getVerifyTypeInfo($status){
        switch ($status){
            case '-1': $rst = '未认证';break; //未认证
            case '0': $rst = '已认证';break; //微信认证
            case '1': $rst = '未认证';break; //新浪微博认证
            case '2': $rst = '未认证';break; //腾讯微博认证
            case '3': $rst = '已认证';break; //资质认证,未名称认证
            case '4': $rst = '已认证';break; //资质认证,未名称认证,新浪微博认证
            case '5': $rst = '已认证';break; //资质认证,未名称认证,腾讯微博认证
            default: $rst = '未知类型';break;
        }
        return $rst;
    }
}
