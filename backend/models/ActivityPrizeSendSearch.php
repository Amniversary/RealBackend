<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/26
 * Time: 9:54
 */

namespace backend\models;


use common\models\LuckyDrawRecord;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class ActivityPrizeSendSearch extends ActivityPrizeSendForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['activity_id', 'client_no', 'prize_type',  'is_send', 'is_direct_send','express_num'], 'integer'],
            [['prize_user_name','reward_name','prize_user_site','create_time','nick_name'], 'safe'],
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
            ->select(['ldr.record_id','client_no','nick_name','title','reward_name','prize_name','prize_type','is_winning','is_send','is_direct_send','prize_user_name','prize_user_site','express_num','ldr.create_time'])
            ->from('mb_client c')
            ->innerJoin('mb_lucky_draw_record ldr','c.client_id = ldr.user_id')
            ->innerJoin('mb_activity_info ai','ai.activity_id = ldr.activity_id');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','ldr.create_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'activity_id'=>$this->activity_id,
            'reward_name'=>$this->reward_name,
            'prize_type'=>$this->prize_type,
            'is_winning'=>$this->is_winning,
            'is_send'=>$this->is_send,
            'is_direct_send'=>$this->is_direct_send,
            'express_num'=>$this->express_num,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no])
            ->andFilterWhere(['like', 'prize_user_name', $this->prize_user_name])
            ->andFilterWhere(['like', 'prize_user_site', $this->prize_user_site]);

        $query->orderBy('create_time desc');
        return $dataProvider;
    }
} 