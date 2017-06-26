<?php

namespace backend\models;

use common\models\Client;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class LivingTimeDetailSearch extends LivingTimeDetailForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','living_before_id'], 'integer'],
            [['nick_name','client_no','create_time','finish_time','living_time'], 'safe'],
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
        $query->select(['ct.client_id','ct.client_no','ct.nick_name','ls.living_before_id','ls.create_time','ls.finish_time','REPLACE(FORMAT(ifnull(living_second_time,0)/3600,2),\',\',\'\') as living_time'])
            ->from('mb_living_statistics ls')
            ->innerJoin('mb_client ct','ls.living_master_id = ct.client_id')
            ->where('ls.finish_time is not null')
            ->orderBy(['ls.create_time'=>SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'client_id',
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
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/"; //验证规则

//        $date = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
        if(!empty($this->create_time))
        {
            $create_time = explode('|',$this->create_time);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $time = preg_match ($date,$this->create_time);
                if($time)   //只输入了一个日期
                {
                    $query->andFilterWhere(['between','ls.create_time',date('Y-m-d 00:00:00',strtotime($this->create_time)),date('Y-m-d 23:59:59',strtotime($this->create_time))]);
                }
                else
                {
                    $query->andFilterWhere(['between','ls.create_time',date('Y-m-d 00:00:00'),date('Y-m-d H:i:s')]);  //输入日期有误，搜索默认数据
                }
            }
            else
            {
                $query->andFilterWhere(['between','ls.create_time',date('Y-m-d H:i:s',strtotime($create_time[0])),date('Y-m-d H:i:s',strtotime($create_time[1]))]);
            }
        }



        if(!empty($this->finish_time))
        {
            $finish_time = explode('|',$this->finish_time);
            if (!preg_match ( $date,$finish_time[0]) || !preg_match ( $date,$finish_time[1]))
            {
                $time = preg_match ($date,$this->finish_time);
                if($time)   //只输入了一个日期
                {
                    $query->andFilterWhere(['between','ls.finish_time',date('Y-m-d 00:00:00',strtotime($this->finish_time)),date('Y-m-d 23:59:59',strtotime($this->finish_time))]);
                }
                else
                {
                    $query->andFilterWhere(['between','ls.finish_time',date('Y-m-d 00:00:00'),date('Y-m-d H:i:s')]);  //输入日期有误，搜索默认数据
                }
            }
            else
            {
              $query->andFilterWhere(['between','ls.finish_time',date('Y-m-d H:i:s',strtotime($finish_time[0])),date('Y-m-d H:i:s',strtotime($finish_time[1]))]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'living_before_id' => $this->living_before_id,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no]);

        return $dataProvider;
    }
}
