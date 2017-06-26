<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/13
 * Time: 15:48
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class StatisticQuizSearch extends Quiz
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['living_id','room_no','living_master_id','user_id','is_ok','living_type','guess_type'], 'integer']
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
            ->select(['mgr.record_id','mgr.living_id','mgr.room_no','ml.living_master_id','mgr.user_id','mgr.is_ok','mgr.living_type','mgr.guess_type','mgr.guess_money','mgr.create_time'])
            ->from('mb_guess_record mgr')
            ->innerJoin('mb_living ml','ml.living_id=mgr.living_id')
            ->orderBy('mgr.record_id desc');
        $count = $query->count('*');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalCount'=>$count,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'mgr.living_id', $this->living_id])
            ->andFilterWhere(['like','mgr.room_no', $this->room_no])
            ->andFilterWhere(['like', 'living_master_id', $this->living_master_id])
            ->andFilterWhere(['like', 'mgr.user_id', $this->user_id])
            ->andFilterWhere(['like','is_ok',$this->is_ok])
            ->andFilterWhere(['like', 'guess_type', $this->guess_type])
            ->andFilterWhere(['like','guess_money',$this->guess_money])
            ->andFilterWhere(['like','mgr.living_type',$this->living_type]);

        return $dataProvider;
    }
}