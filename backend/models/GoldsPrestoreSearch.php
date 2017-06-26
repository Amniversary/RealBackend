<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GoldsPrestore;

/**
 * GoldsPrestoreSearch represents the model behind the search form about `common\models\GoldsPrestore`.
 */
class GoldsPrestoreSearch extends GoldsPrestore
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
              [['prestore_id', 'user_id', 'gold_goods_id', 'gold_goods_num', 'status_result', 'pay_type', 'pay_times'], 'integer'],
              [['gold_goods_price', 'extra_integral_num', 'pay_money'], 'number'],
              [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(){
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
    public function search($params){
        $query = GoldsPrestore::find();

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
            'prestore_id'      => $this->prestore_id,
            'user_id'          => $this->user_id,
            'gold_goods_id'    => $this->gold_goods_id,
            'gold_goods_num'   => $this->gold_goods_num,
            'pay_money'        => $this->pay_money,
            'status_result'    => $this->status_result,
            'pay_type'         => $this->pay_type
        ]);
        if (!isset($params['sort'])) {
            $query->orderBy(['prestore_id' => SORT_DESC]);
        }
        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }
        
        return $dataProvider;
    }
}
