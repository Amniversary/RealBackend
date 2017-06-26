<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\Goods;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class GoodsSearch extends Goods
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['goods_id', 'status', 'sale_type', 'goods_type', 'high_led'], 'integer'],
            [['goods_name', 'pic','remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = Goods::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'goods_id' => $this->goods_id,
            'goods_type'=>$this->goods_type,
            'status' => $this->status,
            'sale_type' => $this->sale_type,
            'extra_bean_num' => $this->extra_bean_num,
            'high_led'=>$this->high_led,
        ]);

        $query->andFilterWhere(['like', 'goods_name', $this->goods_name])
            ->andFilterWhere(['like','goods_price', $this->goods_price])
            ->andFilterWhere(['like', 'pic', $this->pic]);

        $query->orderBy('order_no asc');
        return $dataProvider;
    }
} 