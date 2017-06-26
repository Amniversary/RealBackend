<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/16
 * Time: 10:09
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\log\Logger;

class ClientHotLivingSearch extends ClientHotLivingForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['living_id','client_no','status','s1','living_type','is_contract','living_type'], 'integer'],
            [['nick_name','living_title','living_title'], 'safe'],
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
            ->select(['client_id','client_no','lb.order_no','bl.device_type','living_num','nick_name','living_title','bl.city','lb.hot_num','bl.living_id','bc.status as s1','bl.status','is_official','is_contract','bl.living_type','bl.limit_num'])
            ->from('mb_client bc')
            ->innerJoin('mb_living bl','bl.living_master_id = bc.client_id')
            ->innerJoin('mb_living_hot lb','lb.living_id = bl.living_id')
            ->leftJoin('mb_living_private lp','lp.living_id=bl.living_id and lp.living_before_id=bl.living_before_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        // 如果查询条件为空，则不显示
        if (empty($params) || !isset($params['ClientHotLivingSearch'])) {
            $query->andWhere(['=','bl.status',2]);
        }

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'bl.living_id'=>$this->living_id,
            'nick_name'=>$this->nick_name,
            'is_official'=>$this->is_official,
            'bl.status' => $this->status,
            'bc.status' => $this->s1,
            'lb.order_no' =>$this->order_no,
            'hot_num'=>$this->hot_num,
            'living_num'=>$this->living_num,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no])
            ->andFilterWhere(['like' ,'is_contract',$this->is_contract])
            ->andFilterWhere(['like' ,'living_type',$this->living_type])
            ->andFilterWhere(['like','living_title',$this->living_title]);

        $query->orderBy('lb.order_no asc,hot_num desc');
        return $dataProvider;
    }
} 