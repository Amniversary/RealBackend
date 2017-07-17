<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_batch_keyword_list}}".
 *
 * @property integer $record_id
 * @property integer $app_id
 * @property integer $key_id
 * @property string $remark1
 * @property string $remark2
 */
class BatchKeywordList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_batch_keyword_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'key_id'], 'integer'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'record_id' => 'Record ID',
            'app_id' => 'App ID',
            'key_id' => 'Key ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
