<?php

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ActivitySearch extends ScoreGift
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time'], 'safe',],
            [['activity_status', 'template_id','activity_id'], 'integer'],
            [['title','template_title'], 'string', 'max' => 100],
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
            ->select(['start_time','end_time','activity_status','mag.template_id','activity_id','title','mat.template_title'])
            ->from('mb_activity_giftscore mag')
            ->innerJoin('mb_activity_template mat','mag.template_id=mat.template_id')
            ->orderBy('activity_id desc');



        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'start_time', $this->start_time])
            ->andFilterWhere(['like','end_time', $this->end_time])
            ->andFilterWhere(['like','activity_status', $this->activity_status])
            ->andFilterWhere(['mag.template_id'=>$this->template_id])
            ->andFilterWhere(['like', 'activity_id', $this->activity_id])
            ->andFilterWhere(['like', 'title', $this->title]);


        return $dataProvider;
    }
} 