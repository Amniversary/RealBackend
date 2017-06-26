<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/25
 * Time: 16:16
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class BalanceManageSearch extends FinanceSearchForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'freeze_status','bean_status','ticket_status' ], 'integer'],
            [['nick_name','client_no'], 'safe'],
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
            ->select(['client_id','client_no','nick_name','bean_balance','virtual_bean_balance','ticket_count','ticket_real_sum','ticket_count_sum','virtual_ticket_count','send_ticket_count','freeze_status','bean_status','ticket_status'])
            ->from('mb_client bc')
            ->innerJoin('mb_balance bb','bc.client_id = bb.user_id');
	    
        // 如果查询条件为空，则不显示
        if (empty($params) || !isset($params['BalanceManageSearch'])) {
            $query->andWhere(['<', 'client_id', -10]);
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'totalCount'=>0,
            'pagination' => [
                'pageSize' => 10,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            
            return $dataProvider;
        }

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'bean_balance'=>$this->bean_balance,
            'virtual_bean_balance'=>$this->virtual_bean_balance,
            'ticket_count'=>$this->ticket_count,
            'ticket_real_sum'=>$this->ticket_real_sum,
            'ticket_count_sum' =>$this->ticket_count_sum,
            'virtual_ticket_count'=>$this->virtual_ticket_count,
            'send_ticket_count'=>$this->send_ticket_count,
            'status' => $this->status,
            'freeze_status' => $this->freeze_status,
            'bean_status' => $this->bean_status,
            'ticket_status' => $this->ticket_status,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no]);

        return $dataProvider;
    }
} 