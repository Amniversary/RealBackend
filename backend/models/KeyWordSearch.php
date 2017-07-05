<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/4
 * Time: 下午2:08
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\Keywords;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class KeyWordSearch extends Keywords
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id','rule'], 'integer'],
            [['keyword', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = Keywords::find()->where(['app_id'=>$cacheInfo['record_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'rule'=>$this->rule,
        ]);

        return $dataProvider;
    }
}