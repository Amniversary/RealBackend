<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\GoodsTicketToCash;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ToMoneyGoodsSearch extends GoodsTicketToCash
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'order_no','ticket_num','result_money'], 'integer'],
            [['remark1', 'remark2', 'remark3'], 'string', 'max' => 100],
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
        $query = GoodsTicketToCash::find();

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
            'status' => $this->status,
        ]);



        $query->andFilterWhere(['like', 'ticket_num', $this->ticket_num])
            ->andFilterWhere(['like','result_money', $this->result_money]);





        return $dataProvider;
    }
}