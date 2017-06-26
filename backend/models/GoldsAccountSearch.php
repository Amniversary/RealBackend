<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\GoldsAccount;
use common\models\Client;
/**
 * GoldsPrestoreSearch represents the model behind the search form about `common\models\GoldsPrestore`.
 */
class GoldsAccountSearch extends GoldsAccount
{   
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
              [['gold_account_id', 'user_id','client_no', 'gold_account_total', 'gold_account_expend', 'gold_account_balance', 'account_status'], 'integer'],
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
         $query = GoldsAccount::find()
                 ->select(['a.gold_account_id','a.user_id','c.client_id','c.client_no', 'c.nick_name','a.gold_account_total','a.gold_account_expend','a.gold_account_balance','a.account_status','a.create_time'])
                 ->from('mb_golds_account a')
                 ->innerJoin('mb_client c','a.user_id = c.client_id')
                 ->where(['and','client_id <> -1','client_id <> -2','client_id <> -3']);
        
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
            'gold_account_id'      => $this->gold_account_id,
            'user_id'              => $this->user_id,
            'client_no'            => $this->client_no,
            'gold_account_total'   => $this->gold_account_total,
            'gold_account_expend'  => $this->gold_account_expend,
            'gold_account_balance' => $this->gold_account_balance,
            'account_status'       => $this->account_status
        ]);
        
        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','mb_golds_account.create_time',$start_time, $end_time]);
        }
        
        return $dataProvider;
    }
    
    public static function getNickName($user_id){
          $model = Client::findOne(['client_id'=>$user_id]);
          return $model->nick_name;
    }
}
