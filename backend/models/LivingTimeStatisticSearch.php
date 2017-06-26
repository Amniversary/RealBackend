<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class LivingTimeStatisticSearch extends LivingMonthTimeForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valid_date','is_contract'], 'integer'],
            [['living_time'], 'integer'],
            [['client_no','nick_name','statistic_date'], 'safe'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }


    public function search($params)
    {
        $query = (new Query())
            ->select(['record_id','cl.is_contract','cl.client_no','lt.living_time','cl.nick_name','lt.valid_date','lt.statistic_date'])
            ->from('mb_statistic_living_time lt')
            ->innerJoin('mb_client cl','cl.client_no=lt.client_no')
            ->where('lt.statistic_type=2');

        $dataProvider = new ActiveDataProvider([
            'key'=>'record_id',
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
            'record_id' => $this->record_id,
            'lt.valid_date' => $this->valid_date,
            'cl.is_contract' => $this->is_contract,
            'lt.living_time'=>$this->living_time,
            'lt.statistic_date' => $this->statistic_date,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'lt.client_no', $this->client_no]);
        return $dataProvider;
    }
}