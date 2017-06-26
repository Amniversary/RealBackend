<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BorrowFund;

/**
 * FundBorrowSearch represents the model behind the search form about `common\models\BorrowFund`.
 */
class FundBorrowSearch extends BorrowFund
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['borrow_fund_id', 'user_id', 'by_stages_count', 'is_back', 'status_result', 'finance_has_paid', 'borrow_type', 'reward_id'], 'integer'],
            [['borrow_money', 'stage_money', 'borrow_rate', 'breach_rate', 'breach_last_rate', 'half_delay_times'], 'number'],
            [['back_time', 'create_time', 'refused_reason', 'finance_remark', 'user_name', 'card_no', 'identity_no', 'bank_name', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = BorrowFund::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'borrow_fund_id' => $this->borrow_fund_id,
            'user_id' => $this->user_id,
            'borrow_money' => $this->borrow_money,
            'stage_money' => $this->stage_money,
            'by_stages_count' => $this->by_stages_count,
            'is_back' => $this->is_back,
            'back_time' => $this->back_time,
            'borrow_rate' => $this->borrow_rate,
            'breach_rate' => $this->breach_rate,
            'breach_last_rate' => $this->breach_last_rate,
            'half_delay_times' => $this->half_delay_times,
            'status_result' => '2',
            'finance_has_paid' => $this->finance_has_paid,
            'borrow_type' => $this->borrow_type,
            'reward_id' => $this->reward_id,
        ]);

        $query->andFilterWhere(['like', 'refused_reason', $this->refused_reason])
            ->andFilterWhere(['like', 'finance_remark', $this->finance_remark])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'card_no', $this->card_no])
            ->andFilterWhere(['like', 'identity_no', $this->identity_no])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'remark1', $this->remark1])
            ->andFilterWhere(['like', 'remark2', $this->remark2])
            ->andFilterWhere(['like', 'remark3', $this->remark3])
            ->andFilterWhere(['like', 'remark4', $this->remark4]);

        return $dataProvider;
    }

    public function searchHistory($params)
    {
        $query = BorrowFund::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'borrow_fund_id' => $this->borrow_fund_id,
            'user_id' => $this->user_id,
            'borrow_money' => $this->borrow_money,
            'stage_money' => $this->stage_money,
            'by_stages_count' => $this->by_stages_count,
            'is_back' => $this->is_back,
            'back_time' => $this->back_time,
            'borrow_rate' => $this->borrow_rate,
            'breach_rate' => $this->breach_rate,
            'breach_last_rate' => $this->breach_last_rate,
            'half_delay_times' => $this->half_delay_times,
            'finance_has_paid' => $this->finance_has_paid,
            'borrow_type' => $this->borrow_type,
            'reward_id' => $this->reward_id,
        ]);
        if(empty($this->status_result))
        {
            $query->andFilterWhere(['between','status_result','4','8']);
        }
        else
        {
            $query->andFilterWhere(['status_result'=>$this->status_result]);
        }
        $query->andFilterWhere(['like', 'refused_reason', $this->refused_reason])
            ->andFilterWhere(['like', 'finance_remark', $this->finance_remark])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'card_no', $this->card_no])
            ->andFilterWhere(['like', 'identity_no', $this->identity_no])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'remark1', $this->remark1])
            ->andFilterWhere(['like', 'remark2', $this->remark2])
            ->andFilterWhere(['like', 'remark3', $this->remark3])
            ->andFilterWhere(['like', 'remark4', $this->remark4]);

        return $dataProvider;
    }
}
