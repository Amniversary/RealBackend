<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_later_image}}".
 *
 * @property integer $id
 * @property integer $later_id
 * @property string $pic_url
 * @property string $remark1
 * @property string $remark2
 */
class LaterImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_later_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['later_id'], 'integer'],
            [['pic_url'], 'string', 'max' => 300],
            [['remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'later_id' => 'Later ID',
            'pic_url' => '图片',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
