<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\log\Logger;

/**
 * MyBillSearch represents the model behind the search form about `common\models\Bill`.
 */
class CommentRewardSearch extends WishCommentRewardForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        // username and password are both required
        return
            [
                [['wish_name', 'talk_user','remark2','create_time'], 'safe'],
                [['reward_id'], 'integer'],
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
        $query = new Query();
        $query->select(['wl.reward_id','wh.wish_name','ai.nick_name as talk_user','wl.remark2','wl.create_time','wl.remark4']);
        $query->from('my_reward_list wl')->innerJoin('my_wish wh','wl.wish_id=wh.wish_id');
        $query->innerJoin('my_account_info ai','wl.reward_user_id = ai.account_id');
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
        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','wc.create_time',$start_time, $end_time]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'wh.status' => 1,
            'wl.pay_status'=>2,
            'wc.reward_id'=>$this->reward_id,
        ]);

        $query->andFilterWhere(['between', 'wh.finish_status','1','2']);
        $query->andFilterWhere(['like','wh.wish_name',$this->wish_name])
            ->andFilterWhere(['like','ai.nick_name',$this->talk_user])
            ->andFilterWhere(['like','wl.remark2',$this->remark2]);
        return $dataProvider;
    }

}
