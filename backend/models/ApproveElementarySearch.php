<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19
 * Time: 13:48
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ApproveElementarySearch extends ApproveElementaryForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['approve_id','register_type','device_type','is_centification','phone_no','status'], 'integer'],
            [['nick_name','client_no','create_time','check_time'], 'string'],
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
            'mbc.status' => $this->status,
            'mc.client_no' => $this->client_no,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','mbc.create_time',$startTime,$endTime]);
        }

        if(!empty($this->check_time) && strtotime($this->check_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->check_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->check_time));
            $query->andFilterWhere(['between','mbc.check_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 'mc.register_type', $this->register_type])
            ->andFilterWhere(['like', 'mc.nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'mc.client_no', $this->client_no])
            ->andFilterWhere(['like', 'mbc.status', $this->status])
            ->andFilterWhere(['like', 'mc.phone_no', $this->phone_no]);
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
            ->select(['mc.register_type','mc.nick_name','mc.device_type','mc.client_no','mc.phone_no','mbc.create_time','mbc.status','ma.approve_id'])
            ->from('mb_client mc')
            ->innerJoin('mb_approve ma','mc.client_no=ma.client_no')
            ->innerJoin('mb_business_check mbc','mbc.relate_id=ma.approve_id')
            ->where('mc.status = 1 and mc.is_centification = 5 and mbc.business_type = 4 and mbc.status = 0')
            ->orderBy('ma.create_time desc');
        return $this->queryModel($query,$params);

    }

    //已审核、已拒绝
    public function aditedsearch($params)
    {
//        $query = TicketToCash::find();

        $query = (new Query())
            ->select(['mc.register_type','mc.nick_name','mc.client_no','mc.phone_no','mbc.create_time','mbc.status','mbc.check_time','mbc.check_user_name','ma.approve_id','mbc.check_result_status'])
            ->from('mb_client mc')
            ->innerJoin('mb_approve ma','mc.client_no=ma.client_no')
            ->innerJoin('mb_business_check mbc','mbc.relate_id=ma.approve_id')
            ->where('mc.status = 1 and mc.is_centification != 5 and mbc.status = 1 and mbc.business_type = 4')
            ->orderBy('ma.create_time desc');

        return $this->queryModel($query,$params);
    }


}