<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 11:03
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class SignActhorSearch extends SignActhor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anchor_time'], 'safe'],
            [['client_no'], 'integer'],
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
            ->select(['mc.client_no','mc.nick_name','anchor_salary','anchor_time','is_del','user_id','salary_id'])
            ->from('mb_client_salary mcs')
            ->innerJoin('mb_client mc','mcs.user_id=mc.client_id')
            ->where(['is_del'=>0])
            ->orderBy('anchor_time desc');



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


        $query->andFilterWhere(['like', 'client_no', $this->client_no])
            ->andFilterWhere(['like','anchor_time', $this->anchor_time]);


        return $dataProvider;
    }
}