<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 13:47
 */
namespace backend\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class CloseLivingSearch extends CloseLivingForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id','living_id','living_before_id','backend_user_id'], 'integer'],
            [['backend_user_name','close_time','living_master_name','living_master_no'], 'safe'],
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
            ->select(['log_id','li.living_id','backend_user_id','backend_user_name','cll.living_before_id','close_time','cl.nick_name as living_master_name','cl.client_no as living_master_no'])
            ->from('mb_close_living_log cll')
            ->innerJoin('mb_living li','li.living_id=cll.living_id')
            ->innerJoin('mb_client cl','cl.client_id=li.living_master_id');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'log_id',
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

        if(!empty($this->close_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->close_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->close_time));
            $query->andFilterWhere(['between','close_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'log_id' => $this->log_id,
            'backend_user_id'=>$this->backend_user_id,
            'cll.living_before_id' => $this->living_before_id,
        ]);

        $query->andFilterWhere(['like', 'cl.nick_name', $this->living_master_name])
            ->andFilterWhere(['like' ,'cl.client_no',$this->living_master_no])
            ->andFilterWhere(['like', 'backend_user_name', $this->backend_user_name]);

        $query->orderBy('close_time desc');
        return $dataProvider;
    }
}