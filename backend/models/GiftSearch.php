<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\Gift;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class GiftSearch extends Gift
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_value'], 'number'],
            [['special_effects','world_gift','lucky_gift'],'integer'],
            [['gift_name', 'pic', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query = Gift::find();

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
            'gift_id' => $this->gift_id,
            'world_gift'=>$this->world_gift,
            'lucky_gift'=>$this->lucky_gift,
        ]);



        $query->andFilterWhere(['like', 'gift_name', $this->gift_name])
            ->andFilterWhere(['like','gift_value', $this->gift_value])
            ->andFilterWhere(['like','special_effects', $this->special_effects]);



        $query->orderBy('order_no asc');

        return $dataProvider;
    }
}