<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_client}}".
 *
 * @property integer $client_id
 * @property string $open_id
 * @property string $nick_name
 * @property integer $subscribe
 * @property integer $sex
 * @property string $city
 * @property string $language
 * @property string $province
 * @property integer $country
 * @property string $headimgurl
 * @property integer $subscribe_time
 * @property string $unionid
 * @property string $groupid
 * @property integer $app_id
 * @property string $create_time
 * @property string $update_time
 * @property string $remark
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class Client extends \yii\db\ActiveRecord
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
            [[ 'app_id','subscribe', 'sex',  'subscribe_time'], 'integer'],
            [['create_time','groupid', 'update_time'], 'safe'],
            [['unionid', ], 'string', 'max' => 200],
            [['open_id', 'nick_name','city','country', 'language', 'province',  'remark', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['headimgurl'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        'client_id' => '用户 ID',
        'open_id' => 'Open ID',
        'nick_name' => '昵称',
        'subscribe' => '是否关注',
        'sex' => '性别',
        'city' => '城市',
        'language' => '语言',
        'province' => '省份',
        'country' => '国家',
        'headimgurl' => '头像',
        'subscribe_time' => '关注时间',
        'unionid' => '唯一 ID',
        'groupid' => '群组 ID',
        'app_id' => 'App ID',
        'create_time' => '创建时间',
        'update_time' => '更新时间',
        'remark' => '备注信息',
        'remark1' => '备用字段1',
        'remark2' => '备用字段2',
        'remark3' => '备用字段3',
        'remark4' => '备用字段4',
    ];
    }
}
