<?php

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ClientRobotSearch extends RobotInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no','audience_robot_no','create_robot_no','user_id','record_id'], 'integer'],
            [['nick_name'], 'string', 'max' => 100]
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
            ->select(['create_robot_no','audience_robot_no','cb.client_no','cb.nick_name','record_id','user_id','cb.client_id'])
            ->from('mb_client_robotinfo cri')
            ->rightJoin('mb_client cb','cb.client_id=cri.user_id');



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
            ->andFilterWhere(['like','nick_name', $this->nick_name])
            ->andFilterWhere(['like','create_robot_no', $this->create_robot_no])
            ->andFilterWhere(['like', 'audience_robot_no', $this->audience_robot_no]);


        return $dataProvider;
    }
} 