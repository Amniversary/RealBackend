<?php

namespace backend\models;

use common\models\Client;
use common\models\ClientBalanceLog;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;
use yii\db\Query;
use yii\log\Logger;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class ClientCalanceLogSearch extends ClientBalanceLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_type',  'log_type', 'operate_type', 'change_rate','pay_type','cash_type'], 'integer'],
            [['operate_value', 'result_value', 'before_balance', 'after_balance'], 'number'],
            [['create_time'], 'safe'],
            [['create_time'], 'validateDate'],
            [['unique_op_id', 'remark1', 'remark2', 'remark3', 'remark4','account_balance'], 'string', 'max' => 100],
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
     * 余额操作记录
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

//        $query = ClientBalanceLog::find()
//            ->select(['log_id','balance_id','user_id','device_type','operate_type','relate_id'])
//            ->where('operate_type in(1,3,6,12,15,17,18,19,20,21,27,28,30,31) and user_id='.$params['user_id'].' and remark1=\'bean_balance\'')->orderBy('create_time desc')->orderBy('log_id desc');
        $query = (new Query())
            ->select(['mcb.log_id','mcb.balance_id','mcb.user_id','mcb.device_type','mcb.operate_type','mcb.relate_id','mr.pay_type','before_balance','mcb.operate_value','after_balance','mcb.create_time','mub.account_balance'])
            ->from('mb_client_balance_log mcb')
            ->leftJoin('mb_recharge mr','mcb.user_id = mr.user_id and mcb.relate_id = mr.recharge_id')
            ->leftJoin('mb_ticket_to_cash mtt','mcb.user_id = mtt.user_id and mcb.relate_id = mtt.record_id')
            ->leftJoin('mb_update_balance_record mub','mcb.operate_type = mub.operate_type and mcb.create_time = mub.create_time')
            ->where('mcb.operate_type in(1,3,6,12,14,15,16,17,18,19,20,21,27,28) and mcb.user_id='.$params['user_id']. ' and mcb.remark1=\'bean_balance\'')
            ->orderBy('mcb.create_time,mcb.log_id desc');
        //echo $query->createCommand()->getRawSql();
        return $this->resultDataProvider($query,$params);
    }

    /**
     * 可提现票操作记录
     * @param $params
     * @return ActiveDataProvider
     */
    public function ticketSearch($params)
    {
//        $query = ClientBalanceLog::find()
//            //->select(['log_id','balance_id','user_id','device_type','operate_type','relate_id'])
//            ->where('operate_type in(2,4,7,10,13,29,30,31) and user_id='.$params['user_id'].' and remark1=\'ticket_count\'')->orderBy('create_time desc')->orderBy('log_id desc');

        $query = (new Query())
            ->select(['mcb.log_id','mcb.balance_id','mcb.user_id','mcb.device_type','mcb.operate_type','mcb.relate_id','mtt.cash_type','before_balance','mcb.operate_value','after_balance','mcb.create_time','mub.account_balance'])
            ->from('mb_client_balance_log mcb')
            ->leftJoin('mb_ticket_to_cash mtt','mcb.user_id = mtt.user_id and mcb.relate_id = mtt.record_id')
            ->leftJoin('mb_recharge mr','mcb.user_id = mr.user_id and mcb.relate_id = mr.recharge_id')
            ->leftJoin('mb_update_balance_record mub','mcb.operate_type = mub.operate_type and mcb.create_time = mub.create_time')
            ->where('mcb.operate_type in(2,4,7,10,13,29,30,31) and mcb.user_id='.$params['user_id'].' and mcb.remark1 = \'ticket_count\'')
            ->orderBy('mcb.create_time,mcb.log_id desc');

        return $this->resultDataProvider($query,$params);
    }

    public function resultDataProvider($query,$params)
    {
        $dataProvider = new ActiveDataProvider([
            'key'=>'log_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            $query->andFilterWhere([
                'log_id' => '-1',
            ]);
            return $dataProvider;
        }

        if(!empty($this->create_time))
        {

            $create_time = explode('|', $this->create_time);
            $start_time = date('Y-m-d',strtotime($create_time[0])).' 00:00:00';
            $end_time = date('Y-m-d',strtotime($create_time[1])).' 23:59:59';

            $query->andFilterWhere(['between','mcb.create_time',$start_time, $end_time]);
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'balance_id' => $this->balance_id,
            'user_id'=>$this->user_id,
            'device_type' => $this->device_type,
            'mcb.operate_type' =>$this->operate_type,
            'relate_id'=>$this->relate_id,
            'pay_type'=>$this->pay_type,
            'cash_type'=>$this->cash_type,
        ]);

        $query->andFilterWhere(['like', 'operate_value', $this->operate_value])
            ->andFilterWhere(['like', 'unique_op_id', $this->unique_op_id])
            ->andFilterWhere(['like', 'before_balance', $this->before_balance])
            ->andFilterWhere(['like', 'after_balance', $this->after_balance]);

        return $dataProvider;
    }
    public function validateDate($attribute){
//        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9])?$/";
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s?$/";
        if(!empty($this->create_time))
        {
            $create_time = explode('|',$this->create_time);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd|yyyy-mm-dd");
                return false;
            }
            $time= strtotime($create_time[1])-strtotime($create_time[0]);
            $day = floor($time/3600/24);

            if($day>30){
                $this->addError($attribute,"搜索时间间隔请不要超过30天");
                return false;
            }
        }
    }
}
