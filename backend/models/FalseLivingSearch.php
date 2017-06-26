<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 15:23
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class FalseLivingSearch extends FalseLivingForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['living_id','flower_num','status','ticket_num','system_num'], 'integer'],
            [['room_no','create_time'], 'safe'],
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
            ->select(['li.living_id','ifnull(flower_num,0) as flower_num','ifnull(ticket_num,0) as ticket_num','ifnull(system_num,0) as system_num','status','li.room_no','create_time'])
            ->from('mb_living li')
            ->leftJoin('mb_statistic_guess_num sgn','sgn.living_id=li.living_id and sgn.room_no=li.room_no')
            ->where('li.living_type=5')
            ->orderBy('li.create_time',SORT_DESC);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'living_id',
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
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        $query->andFilterWhere([
            'li.living_id' => $this->living_id,
            'status' => $this->status,
        ]);
        $query->andFilterWhere(['like','room_no', $this->room_no])
            ->andFilterWhere(['like', 'flower_num', $this->flower_num])
            ->andFilterWhere(['like', 'ticket_num', $this->ticket_num])
            ->andFilterWhere(['like', 'system_num', $this->system_num]);

        return $dataProvider;
    }
} 