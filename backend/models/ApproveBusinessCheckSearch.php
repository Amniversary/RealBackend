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

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ApproveBusinessCheckSearch extends ApproveBusinessCheckForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['approve_id','check_user_id', 'create_user_id'], 'integer'],
            [['status','create_time','client_no','actual_name','bank_account', 'phone_num', 'id_card','create_user_name','wechat','qq','account_name','check_time'], 'string'],
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
            'key'=>'approve_id',
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
            'a.approve_id' => $this->approve_id,
            'a.status' => $this->status,
            'u.client_no' => $this->client_no,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','a.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','a.check_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 'c.create_user_name', $this->create_user_name])
            ->andFilterWhere(['like', 'a.phone_num', $this->phone_num])
            ->andFilterWhere(['like', 'a.wechat', $this->wechat])
            ->andFilterWhere(['like', 'a.qq', $this->qq])
            ->andFilterWhere(['like', 'a.account_name', $this->account_name])
            ->andFilterWhere(['like', 'a.actual_name', $this->actual_name])
            ->andFilterWhere(['like', 'a.bank_account', $this->bank_account])
        ->andFilterWhere(['like', 'a.id_card', $this->id_card]);
        return $dataProvider;
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
            ->select(['c.check_result_status','u.client_no','a.wechat','a.qq','a.account_name','approve_id','actual_name','bank_account','a.phone_num','a.id_card','a.create_time','id_card_pic_all','id_card_pic_main','id_card_pic_turn',
                'c.check_time','c.check_user_name','c.create_user_name','refused_reason','c.create_user_name','c.status','u.nick_name'])
            ->from('mb_approve a')
            ->innerJoin('mb_client u','u.client_id=a.client_id')
            ->innerJoin('mb_business_check c','c.relate_id=a.approve_id and c.status=0')
            ->where('c.status =0 and business_type=3 and c.check_no BETWEEN '.CheckMoneyGoodsSearch::GetCheckNo()['start_no'].' and '.CheckMoneyGoodsSearch::GetCheckNo()['end_no'])
            ->orderBy(['c.create_time'=>SORT_DESC]);
        return $this->queryModel($query,$params);

    }

    //已审核、已拒绝
    public function aditedsearch($params)
    {
//        $query = TicketToCash::find();

        $query = (new Query())
            ->select(['c.check_result_status','u.client_no','a.wechat','a.qq','a.account_name','approve_id','actual_name','bank_account','a.phone_num','a.id_card','a.create_time','id_card_pic_all','id_card_pic_main','id_card_pic_turn',
                'c.check_time','c.check_user_name','c.create_user_name','refused_reason','c.create_user_name','c.status','u.nick_name'])
            ->from('mb_approve a')
            ->innerJoin('mb_client u','u.client_id=a.client_id')
            ->innerJoin('mb_business_check c','c.relate_id=a.approve_id and c.status=1')
            ->where('c.status = 1 and business_type=3 and c.check_no BETWEEN '.CheckMoneyGoodsSearch::GetCheckNo()['start_no'].' and '.CheckMoneyGoodsSearch::GetCheckNo()['end_no'])
            ->orderBy(['c.create_time'=>SORT_DESC]);

        return $this->queryModel($query,$params);
    }


}