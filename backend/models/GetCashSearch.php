<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\GetCash;
use yii\db\Query;

/**
 * GetCashSearch represents the model behind the search form about `common\models\GetCash`.
 */
class GetCashSearch extends GetCashForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['get_cash_id','user_id'], 'integer'],
            [['first_get_money','identity_no', 'real_name', 'card_no', 'bank_name', 'check_time'], 'safe'],
            [['cash_money','balance'], 'string', 'max'=>15],
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
        //$query = GetCash::find();
        $query = new Query();
        $query->select(['get_cash_id','gc.user_id','balance','cash_money','status','first_get_money','identity_no','real_name','card_no','bank_name','check_time']);
        $query->from('my_get_cash gc');
        $query->innerJoin('my_user_account_info uai','gc.user_id=uai.user_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'get_cash_id',
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
        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','check_time',$startTime,$endTime]);
        }
        //'cash_money','balance'
        if(!empty($this->cash_money))
        {
            $items = explode(' ',$this->cash_money);
            if(count($items)> 0 && count($items) % 2 === 0)
            {
                $op  = $items[0];
                $value = doubleval($items[1]);
                if(in_array($op,['>','<','=','>=','<=']))
                {
                    $query->andFilterWhere([$op,'cash_money',$value]);
                }
                if(count($items) > 2)
                {
                    $op=$items[2];
                    $value = doubleval($items[3]);
                    if(in_array($op,['>','<','=','>=','<=']))
                    {
                        $query->andFilterWhere([$op,'cash_money',$value]);
                    }
                }
            }
        }

        if(!empty($this->balance))
        {
            $items = explode(' ',$this->balance);
            if(count($items)> 0 && count($items) % 2 === 0)
            {
                $op  = $items[0];
                $value = doubleval($items[1]);
                if(in_array($op,['>','<','=','>=','<=']))
                {
                    $query->andFilterWhere([$op,'balance',$value]);
                }
                if(count($items) > 2)
                {
                    $op=$items[2];
                    $value = doubleval($items[3]);
                    if(in_array($op,['>','<','=','>=','<=']))
                    {
                        $query->andFilterWhere([$op,'balance',$value]);
                    }
                }
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'get_cash_id' => $this->get_cash_id,
            'user_id' => $this->user_id,
            'status' => '2',
            'first_get_money'=> $this->first_get_money,
        ]);

        $query->andFilterWhere(['like', 'identity_no', $this->identity_no])
            ->andFilterWhere(['like', 'real_name', $this->real_name])
            ->andFilterWhere(['like', 'card_no', $this->card_no])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name]);


        return $dataProvider;
    }

    public function searchHistory($params)
    {
        $query = new Query();
        $query->select(['get_cash_id','gc.check_time','gc.user_id','balance','cash_money','status','first_get_money','identity_no','real_name','card_no','bank_name','check_time']);
        $query->from('my_get_cash gc');
        $query->innerJoin('my_user_account_info uai','gc.user_id=uai.user_id');

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
            'get_cash_id' => $this->get_cash_id,
            'user_id' => $this->user_id,
            'cash_money' => $this->cash_money,
            'check_time' => $this->check_time,
            'first_get_money'=> $this->first_get_money,
        ]);
        if(empty($this->status))
        {
            $query->andFilterWhere(['between','status','3','4']);
        }
        else
        {
            $query->andFilterWhere(['status'=>$this->status]);
        }


        $query->andFilterWhere(['like', 'identity_no', $this->identity_no])
            ->andFilterWhere(['like', 'real_name', $this->real_name])
            ->andFilterWhere(['like', 'card_no', $this->card_no])
            ->andFilterWhere(['like', 'bank_name', $this->bank_name]);

        return $dataProvider;
    }
}
