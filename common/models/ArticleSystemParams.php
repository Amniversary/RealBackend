<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_article_system_params}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $qrcode_url
 * @property string $create_time
 * @property string $update_time
 * @property string $remark1
 * @property string $remark2
 */
class ArticleSystemParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article_system_params}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'safe'],
            [['title', 'remark1', 'remark2'], 'string', 'max' => 100],
            [['qrcode_url'], 'string', 'max' => 300],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'qrcode_url' => 'Qrcode Url',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
