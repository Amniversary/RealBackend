<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/27
 * Time: 13:30
 */

namespace backend\models;


use common\models\Recharge;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class UserRechargeSearch extends UserRechargeForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','goods_id','recharge_id','status_result','pay_type'], 'integer'],
            [['goods_name', 'create_time','client_no' ], 'safe'],
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
            ->select(['recharge_id','user_id','goods_id','goods_price','goods_num','bean_num','pay_money','client_no','status_result','pay_type','other_pay_bill','pay_times','op_unique_no','fail_reason','pay_bill','goods_name','br.create_time'])
            ->from('mb_recharge br')
            ->innerJoin('mb_client bc','br.user_id = bc.client_id');

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
            $query->andFilterWhere(['between','br.create_time',$start_time, $end_time]);
        }

        $query->andFilterWhere([
            'recharge_id' => $this->recharge_id,
            'user_id' => $this->user_id,
            'goods_id'=>$this->goods_id,
            'goods_price' =>$this->goods_price,
            'goods_num' =>$this->goods_num,
            'bean_num'=>$this->bean_num,
            'pay_money'=>$this->pay_money,
            'status_result'=>$this->status_result,
            'pay_type' =>$this->pay_type,
            'other_pay_bill' =>$this->other_pay_bill,
            'pay_times' => $this->pay_times,
            'op_unique_no' =>$this->op_unique_no,
            'fail_reason' =>$this->fail_reason,
        ]);

        $query->andFilterWhere(['like', 'goods_name', $this->goods_name])
            ->andFilterWhere(['like', 'pay_bill',$this->pay_bill])
            ->andFilterWhere(['like', 'client_no' ,$this->client_no]);

        $query->orderBy('br.create_time desc');
        return $dataProvider;
    }
} 