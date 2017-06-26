<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%_shortmessage_list}}".
 *
 * @property integer $id
 * @property string $createtime
 * @property string $tel
 * @property string $templateid
 * @property string $content
 * @property string $res
 * @property integer $status
 */
class ShortmessageList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_shortmessage_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','status'], 'integer'],
            [['createtime', 'tel', 'templateid'], 'string', 'max' => 32],
            [['content'], 'string', 'max' => 512],
            [['res'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增主键',
            'createtime' => '创建时间',
            'tel' => '手机号',
            'templateid' => '短信模板id',
            'content' => '短信参数',
            'res' => '发送结果',
        ];
    }
}
