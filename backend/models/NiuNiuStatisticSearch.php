<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class NiuNiuStatisticSearch extends NiuNiuStatisticForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_id','chip_player_num'], 'integer'],
            [['create_time'], 'safe'],
            [['client_no','nick_name'], 'string'],
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
        $query->select(['nick_name','client_no','ng.record_id as game_id','sum(distinct gcm.chip_player_num) as chip_player_num','count(gs.win_num>0 or null) as win_num','count(gs.win_num<0 or null) as lose_num','ng.create_time'])
            ->from('mb_niuniu_game ng')
            ->innerJoin('mb_game_chip_money gcm','gcm.game_id=ng.record_id')
            ->innerJoin('mb_game_seat gs','gs.game_id=ng.record_id')
            ->innerJoin('mb_client cl','cl.client_id=ng.living_master_id')
            ->groupBy('ng.record_id')
            ->orderBy(['ng.record_id'=>SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'ng.record_id',
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
                    $query->andFilterWhere(['between','ng.create_time',date('Y-m-d 00:00:00',strtotime($this->create_time)),date('Y-m-d 23:59:59',strtotime($this->create_time))]);
                }
                else
                {
                    $query->andFilterWhere(['between','ng.create_time',date('Y-m-d 00:00:00'),date('Y-m-d H:i:s')]);  //输入日期有误，搜索默认数据
                }
            }
            else
            {
                $query->andFilterWhere(['between','ng.create_time',date('Y-m-d H:i:s',strtotime($create_time[0])),date('Y-m-d H:i:s',strtotime($create_time[1]))]);
            }
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ng.record_id' => $this->game_id,
        ]);

        $query->andFilterWhere(['like', 'chip_player_num', $this->chip_player_num])
            ->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'client_no', $this->client_no]);

        return $dataProvider;
    }
}
