<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 13:57
 */
namespace backend\models;


use common\models\IntegralMall;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class IntegralMallSearch extends IntegralMall
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gift_order','gift_type'], 'integer'],
            [['gift_name'], 'string', 'max' => 100],
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
        $query = (new Query())
            ->select(['gift_pic','gift_name','gift_money','gift_integral','gift_order','gift_num','gift_details','gift_type','record_id','gift_accept','gift_grant','gift_send_num'])
            ->from('mb_integral_mall')
            ->orderBy('gift_order asc');



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


        $query->andFilterWhere(['like','gift_name', $this->gift_name])
            ->andFilterWhere(['like', 'gift_order', $this->gift_order])
            ->andFilterWhere(['like', 'gift_type', $this->gift_type]);


        return $dataProvider;
    }
}