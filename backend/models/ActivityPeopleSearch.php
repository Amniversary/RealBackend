<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/26
 * Time: 21:11
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ActivityPeopleSearch extends ActivityPeopleForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no', 'nick_name','title'], 'safe',],
            [['activity_id'], 'integer'],
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
            ->select(['ap.record_id','client_no','nick_name','ai.activity_id','title'])
            ->from('mb_activity_people ap')
            ->innerJoin('mb_client bc','ap.living_master_id = bc.client_id')
            ->innerJoin('mb_activity_info ai','ai.activity_id = ap.activity_id');

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

        $query->andFilterWhere([
            'ai.activity_id'=>$this->activity_id
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
              ->andFilterWhere(['like', 'client_no', $this->client_no])
              ->andFilterWhere(['like', 'title', $this->title]);
        return $dataProvider;
    }
} 