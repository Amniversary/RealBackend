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
class BalanceLogSearch extends ClientBalanceLog
{
    private $client_no;
    private $nick_name;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['device_type',  'log_type', 'operate_type', 'change_rate','pay_type','cash_type'], 'integer'],
            // [['operate_value', 'result_value', 'before_balance', 'after_balance'], 'number'],
            [['create_time'], 'safe'],
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

    public function isAttributeActive($attribute)
    {
        $attributes = $this->activeAttributes();
        $attributes[] = 'client_no';
        $attributes[] = 'nick_name';
        return in_array($attribute, $attributes, true);
    }

    public function getClient_no()
    {
        return $this->client_no;
    }

    public function getNick_name()
    {
        return $this->nick_name;
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
            ->select(['mcb.log_id','client_no','nick_name','mcb.balance_id','mcb.user_id','mcb.device_type','mcb.operate_type','mcb.relate_id','mr.pay_type','before_balance','mcb.operate_value','after_balance','mcb.create_time','mub.account_balance'])
            ->from('mb_client_balance_log mcb')
            ->leftJoin('mb_recharge mr','mcb.user_id = mr.user_id and mcb.relate_id = mr.recharge_id')
            ->leftJoin('mb_ticket_to_cash mtt','mcb.user_id = mtt.user_id and mcb.relate_id = mtt.record_id')
            ->leftJoin('mb_update_balance_record mub','mcb.operate_type = mub.operate_type and mcb.create_time = mub.create_time')
            ->leftJoin('mb_client bc','mcb.user_id = bc.client_id')
            ->where('mcb.operate_type in(1,3,6,12,14,15,16,17,18,19,20,21,27,28) and mcb.remark1=\'bean_balance\'')
            ->orderBy('mcb.create_time desc');

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
            ->select(['mcb.log_id','client_no','nick_name','mcb.balance_id','mcb.user_id','mcb.device_type','mcb.operate_type','mcb.relate_id','mtt.cash_type','before_balance','mcb.operate_value','after_balance','mcb.create_time','mub.account_balance'])
            ->from('mb_client_balance_log mcb')
            ->leftJoin('mb_ticket_to_cash mtt','mcb.user_id = mtt.user_id and mcb.relate_id = mtt.record_id')
            ->leftJoin('mb_recharge mr','mcb.user_id = mr.user_id and mcb.relate_id = mr.recharge_id')
            ->leftJoin('mb_update_balance_record mub','mcb.operate_type = mub.operate_type and mcb.create_time = mub.create_time')
            ->leftJoin('mb_client bc','mcb.user_id = bc.client_id')
            ->where('mcb.operate_type in(2,4,7,10,13,29,30,31) and mcb.remark1 = \'ticket_count\'')
            ->orderBy('mcb.create_time desc');

        return $this->resultDataProvider($query,$params);
    }

    public function resultDataProvider($query,$params)
    {
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount' => 30,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->client_no = isset($params['BalanceLogSearch']['client_no']) ? $params['BalanceLogSearch']['client_no'] : null;
        $this->nick_name = isset($params['BalanceLogSearch']['nick_name']) ? $params['BalanceLogSearch']['nick_name'] : null;

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
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
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
            'client_no'=>$this->client_no,
        ]);

        $query->andFilterWhere(['like', 'operate_value', $this->operate_value])
            ->andFilterWhere(['like', 'unique_op_id', $this->unique_op_id])
            ->andFilterWhere(['like', 'before_balance', $this->before_balance])
            ->andFilterWhere(['like', 'after_balance', $this->after_balance])
            ->andFilterWhere(['like', 'nick_name', $this->nick_name]);

        return $dataProvider;
    }
}
