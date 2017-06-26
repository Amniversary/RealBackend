<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\RedPackets;

/**
 * RedPacketsSearch represents the model behind the search form about `common\models\RedPackets`.
 */
class RedPacketsSearch extends RedPackets
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['red_packets_id', 'get_type', 'overtime_days', 'packets_type'], 'integer'],
            [['packets_name', 'discribtion', 'pic', 'start_time', 'end_time', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
            [['packets_money'], 'number'],
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
        $query = RedPackets::find();

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
        $query->orderBy('red_packets_id desc');
        // grid filtering conditions
        $query->andFilterWhere([
            'red_packets_id' => $this->red_packets_id,
            'packets_money' => $this->packets_money,
            'get_type' => $this->get_type,
            'overtime_days' => $this->overtime_days,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'packets_type' => $this->packets_type,
        ]);

        $query->andFilterWhere(['like', 'packets_name', $this->packets_name])
            ->andFilterWhere(['like', 'discribtion', $this->discribtion])
            ->andFilterWhere(['like', 'pic', $this->pic])
            ->andFilterWhere(['like', 'remark1', $this->remark1])
            ->andFilterWhere(['like', 'remark2', $this->remark2])
            ->andFilterWhere(['like', 'remark3', $this->remark3])
            ->andFilterWhere(['like', 'remark4', $this->remark4]);

        return $dataProvider;
    }
}
