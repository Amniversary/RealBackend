<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%_keywords}}".
 *
 * @property integer $key_id
 * @property string $keyword
 * @property integer $app_id
 * @property integer $rule
 * @property string $remark1
 * @property string $remark2
 * @property string $remark3
 * @property string $remark4
 */
class Keywords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%_keywords}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id','rule'], 'integer'],
            [['keyword', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'key_id' => 'Key ID',
            'keyword' => '关键词',
            'rule' => '匹配规则',
            'app_id' => '公众号',
            'remark1' => '备用字段1',
            'remark2' => '备用字段2',
            'remark3' => '备用字段3',
            'remark4' => '备用字段4',
        ];
    }

    /**
     * 获取公众号关键字
     * @param $appid
     * @return array
     */
    public static function getKeyWord($appid)
    {
        $query = (new Query())
            ->select(['key_id','keyword'])
            ->from('wc_keywords')
            ->where(['app_id'=>$appid])->all();
        $rst = [];
        foreach ($query as $item){
            $rst[$item['key_id']] = $item['keyword'];
        }
        return $rst;
    }


    public static function getKeyName($key_id)
    {
        $query = (new Query())
            ->select(['keyword'])
            ->from('wc_keywords')
            ->where(['key_id'=>$key_id])->one();
        return $query['keyword'];
    }
}
