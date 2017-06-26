<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\GoldsGoods;
/**
 * GoldsGoodsSearch represents the model behind the search form about `common\models\GoldsGoods`.
 */
class GoldsGoodsSearch extends GoldsGoods
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['gold_goods_id','sale_type','status','gold_goods_type'],'integer'],
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
        $query =  GoldsGoods::find();
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
            'gold_goods_id'      => $this->gold_goods_id,
            'sale_type'          => $this->sale_type,
            'status'             => $this->status,
            'gold_goods_type'    => $this->gold_goods_type
        ]);
        
        return $dataProvider;
    }
}
