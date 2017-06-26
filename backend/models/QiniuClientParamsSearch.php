<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 15:40
 */

namespace backend\models;


use common\models\ClientLivingParameters;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class QiniuClientParamsSearch extends QiniuClientParamsForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id' ], 'integer'],
            [['user_id','client_no'], 'safe'],
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
            ->select(['relate_id','user_id','client_no','parameters_more'])
            ->from('mb_client bc')
            ->innerJoin('mb_client_living_parameters clp','bc.client_id = clp.user_id');

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
            'relate_id' => $this->relate_id,
            'user_id'=>$this->user_id,
            'client_no'=>$this->client_no,
            'parameters_more'=> $this->parameters_more
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
              ->andFilterWhere(['like', 'client_no', $this->client_no]);


        return $dataProvider;
    }
} 