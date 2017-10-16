<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cInvitationCard".
 *
 * @property integer $card_id
 * @property string $bride
 * @property string $bridegroom
 * @property integer $phone
 * @property string $wedding_time
 * @property string $site
 * @property integer $status
 * @property string $pic
 * @property string $latitude
 * @property string $longitude
 * @property integer $create_time
 * @property string $remark1
 * @property string $remark2
 */
class CInvitationCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cInvitationCard';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bride', 'bridegroom', 'wedding_time', 'site', 'status', 'latitude', 'longitude', 'create_time'], 'required'],
            [['status', 'create_time'], 'integer'],
            ['phone', 'match', 'pattern' => '/^1\d{10}$/u', 'message' =>'手机号不正确'],
            ['phone', 'string', 'min' => 11, 'max' => 11],
            [['latitude', 'longitude'], 'number'],
            [['bride', 'bridegroom'], 'string', 'max' => 50],
            [['wedding_time', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['site'], 'string', 'max' => 200],
            [['pic'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'card_id' => 'Card ID',
            'bride' => 'Bride',
            'bridegroom' => 'Bridegroom',
            'phone' => 'Phone',
            'wedding_time' => 'Wedding Time',
            'site' => 'Site',
            'status' => 'Status',
            'pic' => 'Pic',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'create_time' => 'Create Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
