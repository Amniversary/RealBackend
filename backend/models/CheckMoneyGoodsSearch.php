<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\TicketToCash;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\log\Logger;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class CheckMoneyGoodsSearch extends TickToCashForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_no','client_id','user_id','cash_rate','status','fail_status'], 'integer'],
            [['ticket_num', 'real_cash_money','cash_type'], 'number'],
            [['create_time', 'check_time', 'finace_ok_time','alipay_no','real_name','nick_name'], 'safe'],

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


    public function queryModel($query,$params){
        // add conditions that should always apply here

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
            't.record_id' => $this->record_id,
            't.status' => $this->status,
            't.user_id' => $this->user_id,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','t.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','t.check_time',$startTime,$endTime]);
        }

        if(!empty($this->finace_ok_time) && strtotime($this->finace_ok_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->finace_ok_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->finace_ok_time));
            $query->andFilterWhere(['between','t.finace_ok_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 't.ticket_num', $this->ticket_num])
            ->andFilterWhere(['like', 'cash_type', $this->cash_type])
            ->andFilterWhere(['like', 'u.client_id', $this->client_id])
            ->andFilterWhere(['like', 'u.client_no', $this->client_no])
            ->andFilterWhere(['like', 't.real_cash_money', $this->real_cash_money]);
        return $dataProvider;
    }

    public static function GetCheckNo()
    {
        $user_id = \Yii::$app->user->id;
        $check_no = \Yii::$app->cache->get('backend_user_check_no'.strval($user_id));
        $check_no = json_decode($check_no,true);
        if(empty($check_no['start_no']) && empty($check_no['end_no']))
        {
            $check_no = json_encode(['start_no'=>'0','end_no'=>'0']);
            $check_no = json_decode($check_no,true);
        }
        return $check_no;
    }

    /**
     * 未审核
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
//        $query = TicketToCash::find();
        $query = (new Query())
            ->select(['u.client_id','u.client_no','record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','c.check_time','finace_ok_time'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id and c.status=0')
            ->where('t.status =1 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
            ->groupBy('record_id');
        $sql = $query->createCommand()->getRawSql();
//        \Yii::getLogger()->log('search_sql=:'.$sql,Logger::LEVEL_ERROR);
        return $this->queryModel($query,$params);

    }

    //已审核、已拒绝
    public function aditedsearch($params)
    {
//        $query = TicketToCash::find();

        $query = (new Query())
            ->select(['u.client_id','u.client_no','record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id and c.status=1')
            ->where('(t.status = 2 || t.status=4) and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
        ->groupBy('record_id');
        return $this->queryModel($query,$params);
    }

    /**
     * 支付宝打款信息搜索
     * @param $query
     * @param $params
     * @return ActiveDataProvider
     */
    public function alipay_search($query,$params)
    {
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
            't.record_id' => $this->record_id,
            't.status' => $this->status,
            't.user_id' => $this->user_id,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','t.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','t.check_time',$startTime,$endTime]);
        }

        if(!empty($this->finace_ok_time) && strtotime($this->finace_ok_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->finace_ok_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->finace_ok_time));
            $query->andFilterWhere(['between','t.finace_ok_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 't.ticket_num', $this->ticket_num])
            ->andFilterWhere(['like', 'cash_type', $this->cash_type])
            ->andFilterWhere(['like', 'u.client_id', $this->client_id])
            ->andFilterWhere(['like', 'u.client_no', $this->client_no])
            ->andFilterWhere(['like', 'al.alipay_no', $this->alipay_no])
            ->andFilterWhere(['like', 'al.real_name', $this->real_name])
            ->andFilterWhere(['like', 'u.nick_name', $this->nick_name])
            ->andFilterWhere(['like', 't.real_cash_money', $this->real_cash_money]);
        return $dataProvider;
    }

    /**
     * 支付宝未打款用户信息
     * @param $params
     * @return ActiveDataProvider
     */
    public function alipay_unpaid($params)
    {
       $query = (new Query())
           ->select(['al.alipay_no','al.real_name','u.client_id','u.client_no','t.record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time'])
           ->from('mb_ticket_to_cash t')
           ->innerJoin('mb_client u','u.client_id=t.user_id')
           ->innerJoin('mb_alipay_for_cash al','al.user_id=u.client_id')
           ->innerJoin('mb_business_check c','c.relate_id=t.record_id')
           ->where('t.status in (2,5) and fail_status=0 and t.cash_type=2 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
           ->groupBy('record_id',SORT_DESC)
           ->orderBy('t.record_id desc ');
        return $this->alipay_search($query,$params);
    }

    /**
     * 支付宝已打款用户信息
     * @param $params
     * @return ActiveDataProvider
     */
    public function alipay_paid($params)
    {
        $query = (new Query())
            ->select(['al.alipay_no','al.real_name','u.client_id','u.client_no','t.record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_alipay_for_cash al','al.user_id=u.client_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id')
            ->where('t.status = 3 and t.cash_type=2 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
            ->groupBy('record_id',SORT_DESC)
            ->orderBy('t.check_time DESC');
        return $this->alipay_search($query,$params);
    }

    /**
     * 微信打款信息搜索
     * @param $query
     * @param $params
     * @return ActiveDataProvider
     */
    public function wechat_search($query,$params)
    {
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
            't.record_id' => $this->record_id,
            't.status' => $this->status,
            't.user_id' => $this->user_id,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','t.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','t.check_time',$startTime,$endTime]);
        }

        if(!empty($this->finace_ok_time) && strtotime($this->finace_ok_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->finace_ok_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->finace_ok_time));
            $query->andFilterWhere(['between','t.finace_ok_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 't.ticket_num', $this->ticket_num])
            ->andFilterWhere(['like', 'cash_type', $this->cash_type])
            ->andFilterWhere(['like', 'u.client_id', $this->client_id])
            ->andFilterWhere(['like', 'u.client_no', $this->client_no])
            ->andFilterWhere(['like', 'u.nick_name', $this->nick_name])
            ->andFilterWhere(['like', 't.real_cash_money', $this->real_cash_money]);
        return $dataProvider;
    }

    /**
     * 微信未打款用户信息
     * @param $params
     * @return ActiveDataProvider
     */
    public function wechat_unpaid($params)
    {
        $query = (new Query())
            ->select(['u.client_id','u.client_no','record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id')
            ->where('t.status in (2,5) and fail_status=0 and t.cash_type=1 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
            ->groupBy('t.record_id',SORT_DESC)
            ->orderBy('t.record_id desc ');
        return $this->wechat_search($query,$params);
    }

    /**
     * 微信已打款用户信息
     * @param $params
     * @return ActiveDataProvider
     */
    public function wechat_paid($params)
    {
        $query = (new Query())
            ->select(['u.client_id','u.client_no','record_id','u.nick_name','ticket_num','real_cash_money', 'cash_type','t.status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id')
            ->where('t.status = 3 and t.cash_type=1 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
            ->groupBy('record_id',SORT_DESC)
            ->orderBy('t.check_time DESC');
        return $this->wechat_search($query,$params);
    }

    /**
     * 打款失败记录
     * @param $params
     * @return ActiveDataProvider
     */
    public function cash_fail($params)
    {
        $query = (new Query())
            ->select(['t.cash_type','u.client_id','u.client_no','t.record_id','u.nick_name','ticket_num','real_cash_money','t.fail_status','cash_rate','refuesd_reason','finance_remark','t.create_time','t.check_time','finace_ok_time','t.remark2'])
            ->from('mb_ticket_to_cash t')
            ->innerJoin('mb_client u','u.client_id=t.user_id')
            ->innerJoin('mb_business_check c','c.relate_id=t.record_id')
            ->where('t.fail_status=1 and c.check_no BETWEEN '.self::GetCheckNo()['start_no'].' and '.self::GetCheckNo()['end_no'])
            ->groupBy('record_id',SORT_DESC)
            ->orderBy('t.record_id desc ');
        return $this->cash_fail_search($query,$params);
    }

    /**
    * 打款失败搜索
    * @param $query
    * @param $params
    * @return ActiveDataProvider
    */
    public function cash_fail_search($query,$params)
    {
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
            't.record_id' => $this->record_id,
            't.fail_status' => $this->fail_status,
            't.user_id' => $this->user_id,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','t.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','t.check_time',$startTime,$endTime]);
        }

        if(!empty($this->finace_ok_time) && strtotime($this->finace_ok_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->finace_ok_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->finace_ok_time));
            $query->andFilterWhere(['between','t.finace_ok_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 't.ticket_num', $this->ticket_num])
            ->andFilterWhere(['like', 'cash_type', $this->cash_type])
            ->andFilterWhere(['like', 'u.client_id', $this->client_id])
            ->andFilterWhere(['like', 'u.client_no', $this->client_no])
            ->andFilterWhere(['like', 'u.nick_name', $this->nick_name])
            ->andFilterWhere(['like', 't.real_cash_money', $this->real_cash_money]);
        return $dataProvider;
    }
}