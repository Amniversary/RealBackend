<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_article_order}}".
 *
 * @property integer $id
 * @property integer $app_id
 * @property string $remark1
 * @property string $remark2
 */
class ArticleOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article_order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'integer'],
            [['remark1', 'remark2'], 'string', 'max' => 100],
            [['app_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app_id' => '公众号',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
