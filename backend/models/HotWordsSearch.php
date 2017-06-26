<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\HotWords;

/**
 * HotWordsSearch represents the model behind the search form about `common\models\HotWords`.
 */
class HotWordsSearch extends HotWords
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hot_words_id', 'words_type', 'status'], 'integer'],
            [['content', 'order_no', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = HotWords::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'hot_words_id' => $this->hot_words_id,
            'words_type' => $this->words_type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'order_no', $this->order_no]);
/*            ->andFilterWhere(['like', 'remark1', $this->remark1])
            ->andFilterWhere(['like', 'remark2', $this->remark2])
            ->andFilterWhere(['like', 'remark3', $this->remark3])
            ->andFilterWhere(['like', 'remark4', $this->remark4]);*/

        return $dataProvider;
    }
}
