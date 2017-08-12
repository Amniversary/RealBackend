<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_carousel}}".
 *
 * @property integer $carousel_id
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $pic_url
 * @property integer $action_type
 * @property integer $order_no
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 */
class Carousel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_carousel}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action_type', 'order_no', 'status'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['title', 'description', 'pic_url', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carousel_id' => 'Carousel ID',
            'title' => 'Title',
            'description' => 'Description',
            'url' => 'Url',
            'pic_url' => 'Pic Url',
            'action_type' => 'Action Type',
            'order_no' => 'Order No',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
