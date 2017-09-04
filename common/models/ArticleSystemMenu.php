<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_article_system_menu}}".
 *
 * @property integer $id
 * @property integer $system_id
 * @property integer $carousel_id
 * @property string $remark1
 * @property string $remark2
 */
class ArticleSystemMenu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_article_system_menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['system_id', 'carousel_id'], 'integer'],
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
            'system_id' => 'System ID',
            'carousel_id' => 'Carousel ID',
            'remark1' => 'Remark1',
            'remark2' => 'Remark2',
        ];
    }
}
