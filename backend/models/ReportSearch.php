<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ReportList;

/**
 * ReportSearch represents the model behind the search form about `common\models\ReportList`.
 */
class ReportSearch extends ReportList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['my_report_id','scene', 'user_id', 'report_type', 'wish_id', 'report_user_id', 'status'], 'integer'],
            [['nick_name', 'wish_name', 'report_user_name', 'report_content', 'create_time', 'check_time', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = ReportList::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        if(!isset($params['scene']))
        {
            $params['scene']='1';
        }
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
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'my_report_id' => $this->my_report_id,
            'user_id' => $this->user_id,
            'scene'=>$this->scene,
            'report_type' => $this->report_type,
            'wish_id' => $this->wish_id,
            'report_user_id' => $this->report_user_id,
            'status' => $this->status,
            'check_time' => $this->check_time,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'wish_name', $this->wish_name])
            ->andFilterWhere(['like', 'report_user_name', $this->report_user_name])
            ->andFilterWhere(['like', 'report_content', $this->report_content]);

        $query->orderBy('status asc,my_report_id desc');

        return $dataProvider;
    }
}
