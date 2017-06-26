<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\BusinessCheck;
use yii\db\Query;

/**
 * BusinessCheckSearch represents the model behind the search form about `common\models\BusinessCheck`.
 */
class BusinessCheckForWishMoneyToBalanceSearch extends WishMoneyToBalanceSearchForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'wish_id','business_check_id','check_no','is_inner'], 'integer'],
            [['ready_reward_money','red_packets_money','wish_money'],'safe'],
            [[ 'wish_name','user_name','phone_no', 'create_time', 'nick_name', 'identity_no'], 'safe'],
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
        $query->select(['is_inner','business_check_id','bc.create_time','check_no','wh.wish_id','wh.wish_name','wh.wish_money','ready_reward_money','red_packets_money','ai.phone_no','ai.nick_name','user_name','identity_no'])
            ->from('my_business_check bc')
            ->innerJoin('my_wish wh','bc.relate_id=wh.wish_id and bc.business_type=5')
            ->innerJoin('my_account_info ai','ai.account_id=bc.create_user_id')
            ->leftJoin('my_base_centification bac','ai.account_id=bac.user_id');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'business_check_id',
            'query' => $query,
          'pagination' => [
              'pageSize' => 15,
          ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $user_id = \Yii::$app->user->id;
        $data = \Yii::$app->cache->get('backend_user_check_no'.strval($user_id));

        if($data !== false)
        {
            $checkNoAry = json_decode($data,true);
            $query->andFilterWhere(['between','check_no',$checkNoAry['start_no'],$checkNoAry['end_no']]);
        }
        else
        {
            $query->andFilterWhere(['check_no'=>'-1']);
        }

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','bc.create_time',$startTime,$endTime]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'wish_id' => $this->wish_id,
            'business_check_id' => $this->business_check_id,
            'bc.status' => 0,
            'ai.nick_name' => $this->nick_name,
            'wh.wish_name'=>$this->wish_name,
            'bac.user_name'=>$this->user_name,
            'bac.identity_no'=>$this->identity_no,
            'ai.phone_no'=>$this->phone_no
        ]);
        if(!empty($this->ready_reward_money))
        {
            $items = explode(' ',$this->ready_reward_money);
            if(count($items)> 0 && count($items) % 2 === 0)
            {
                $op  = $items[0];
                $value = doubleval($items[1]);
                if(in_array($op,['>','<','=','>=','<=']))
                {
                    $query->andFilterWhere([$op,'ready_reward_money',$value]);
                }
                if(count($items) > 2)
                {
                    $op=$items[2];
                    $value = doubleval($items[3]);
                    if(in_array($op,['>','<','=','>=','<=']))
                    {
                        $query->andFilterWhere([$op,'ready_reward_money',$value]);
                    }
                }
            }
        }

        if(!empty($this->red_packets_money))
        {
            $items = explode(' ',$this->red_packets_money);
            if(count($items)> 0 && count($items) % 2 === 0)
            {
                $op  = $items[0];
                $value = doubleval($items[1]);
                if(in_array($op,['>','<','=','>=','<=']))
                {
                    $query->andFilterWhere([$op,'red_packets_money',$value]);
                }
                if(count($items) > 2)
                {
                    $op=$items[2];
                    $value = doubleval($items[3]);
                    if(in_array($op,['>','<','=','>=','<=']))
                    {
                        $query->andFilterWhere([$op,'red_packets_money',$value]);
                    }
                }
            }
        }
        return $dataProvider;
    }
}
