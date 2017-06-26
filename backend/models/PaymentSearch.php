<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\Payment;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class PaymentSearch extends Payment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'status','app_type'], 'integer'],
            [['title', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
            [['icon'], 'string', 'max' => 200],
            [['order_no'], 'string', 'max' => 10],
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
        $query = Payment::find();

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
            'app_type' => $this->app_type,
            'status' => $this->status,
            'code'=>$this->code,
            'order_no'=>$this->order_no
        ]);

//payment_id,code,status,title,order_no,icon

        $query->andFilterWhere(['like', 'title', $this->title]);


        return $dataProvider;
    }
}