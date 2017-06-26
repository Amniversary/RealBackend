<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Bill;
use yii\log\Logger;

/**
 * MyBillSearch represents the model behind the search form about `common\models\Bill`.
 */
class MyBillSearch extends Bill
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bill_id', 'borrow_fund_id', 'user_id', 'status', 'pay_type', 'by_stages_count', 'cur_stage', 'pay_times', 'is_cur_stage', 'breach_days', 'is_check_delay', 'is_delay', 'bad_mark_user_id'], 'integer'],
            [['back_fee', 'real_back_fee', 'breach_fee', 'last_breach_fee'], 'number'],
            [['back_date', 'pay_bill', 'other_pay_bill', 'create_time', 'back_time', 'bad_bill_remark', 'bad_mark_user_name', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = Bill::find();

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
        if(!empty($this->back_date))
        {
            $items = explode('#',$this->back_date);
            $itemLen = count($items);
            switch($itemLen)
            {
                case 1:
                    if($items[0] === 'delay_all')
                    {
                        $query->andFilterWhere(['<=','back_date',date('Y-m-d',strtotime('-1 days'))]);
                    }
                    else
                    {
                        $query->andFilterWhere(['back_date' => date('Y-m-d',strtotime(strval(intval($this->back_date)).' days'))]);
                    }
                    break;
                case 2:
                    $startDate =date('Y-m-d',strtotime(strval(intval($items[1])).' days'));
                    $endDate = date('Y-m-d',strtotime(strval(intval($items[0])).' days'));
                    if($items[1] === 'all')
                    {
                        $query->andFilterWhere(['<=','back_date',$endDate]);
                    }
                    else
                    {
                        $query->andFilterWhere(['between','back_date',$startDate,$endDate]);
                    }
                    break;
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'bill_id' => $this->bill_id,
            'borrow_fund_id' => $this->borrow_fund_id,
            'user_id' => $this->user_id,
            'back_fee' => $this->back_fee,
            'status' => 0,
            'pay_type' => $this->pay_type,
            'by_stages_count' => $this->by_stages_count,
            'cur_stage' => $this->cur_stage,
            'pay_times' => $this->pay_times,
            'create_time' => $this->create_time,
            'back_time' => $this->back_time,
            'is_cur_stage' => $this->is_cur_stage,
            'real_back_fee' => $this->real_back_fee,
            'breach_fee' => $this->breach_fee,
            'last_breach_fee' => $this->last_breach_fee,
            'breach_days' => $this->breach_days,
            'is_check_delay' => $this->is_check_delay,
            'is_delay' => $this->is_delay,
            'bad_mark_user_id' => $this->bad_mark_user_id,
        ]);

        $query->andFilterWhere(['like', 'pay_bill', $this->pay_bill])
            ->andFilterWhere(['like', 'other_pay_bill', $this->other_pay_bill])
            ->andFilterWhere(['like', 'bad_bill_remark', $this->bad_bill_remark])
            ->andFilterWhere(['like', 'bad_mark_user_name', $this->bad_mark_user_name]);
        //\Yii::getLogger()->log(var_export($query->where,true), Logger::LEVEL_ERROR);
        return $dataProvider;
    }

    public function searchHistory($params)
    {
        $query = Bill::find();

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
        if(empty($this->status))
        {
            $query->andFilterWhere(['between','status','1','2']);
        }
        else
        {
            $query->andFilterWhere(['status'=>$this->status]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bill_id' => $this->bill_id,
            'borrow_fund_id' => $this->borrow_fund_id,
            'user_id' => $this->user_id,
            'back_fee' => $this->back_fee,
            'back_date' => $this->back_date,
            'pay_type' => $this->pay_type,
            'by_stages_count' => $this->by_stages_count,
            'cur_stage' => $this->cur_stage,
            'pay_times' => $this->pay_times,
            'create_time' => $this->create_time,
            'back_time' => $this->back_time,
            'is_cur_stage' => $this->is_cur_stage,
            'real_back_fee' => $this->real_back_fee,
            'breach_fee' => $this->breach_fee,
            'last_breach_fee' => $this->last_breach_fee,
            'breach_days' => $this->breach_days,
            'is_check_delay' => $this->is_check_delay,
            'is_delay' => $this->is_delay,
            'bad_mark_user_id' => $this->bad_mark_user_id,
        ]);

        $query->andFilterWhere(['like', 'pay_bill', $this->pay_bill])
            ->andFilterWhere(['like', 'other_pay_bill', $this->other_pay_bill])
            ->andFilterWhere(['like', 'bad_bill_remark', $this->bad_bill_remark])
            ->andFilterWhere(['like', 'bad_mark_user_name', $this->bad_mark_user_name]);

        return $dataProvider;
    }
}
