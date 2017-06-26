<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\IntegralAccount;
use common\models\Client;
/**
 * IntegralAccountSearch represents the model behind the search form about `common\models\IntegralAccount`.
 */
class IntegralAccountSearch extends IntegralAccount
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
              [['integral_account_id', 'user_id','client_no', 'integral_account_total', 'integral_account_spend', 'integral_account_balance', 'account_status'], 'integer'],
              [['create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(){
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
    public function search($params){
        $query = IntegralAccount::find()
            ->select(['a.integral_account_id','a.user_id','c.client_id','c.client_no', 'c.nick_name','a.integral_account_total','a.integral_account_spend','a.integral_account_balance','a.account_status','a.create_time'])
            ->from('mb_integral_account a')
            ->innerJoin('mb_client c','a.user_id = c.client_id')
            ->where('c.client_id not in(-1,-2,-3)');

        $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'totalCount'=>0,
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

        // grid filtering conditions
        $query->andFilterWhere([
            'integral_account_id'      => $this->integral_account_id,
            'user_id'                   => $this->user_id,
            'client_no'                 => $this->client_no,
            'integral_account_total'   => $this->integral_account_total,
            'integral_account_spend'   => $this->integral_account_spend,
            'integral_account_balance' => $this->integral_account_balance,
            'account_status'            => $this->account_status
        ]);
        
        if(!empty($this->create_time)){
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','mb_integral_account.create_time',$start_time, $end_time]);
        }
        
        return $dataProvider;
    }
    
     public static function getNickName($user_id){
          $model = Client::findOne(['client_id'=>$user_id]);
          return $model->nick_name;
    }
}