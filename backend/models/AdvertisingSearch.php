<?php

namespace backend\models;

use common\models\AdImages;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CarouselSearch represents the model behind the search form about `common\models\Carousel`.
 */
class AdvertisingSearch extends AdImages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start_time', 'end_time', 'create_time'], 'safe'],
            [['weights'], 'integer'],
            [['description', 'link_url', 'image_url'], 'string', 'max' => 300],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query = AdImages::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ad_id' => $this->ad_id,
            'weights' => $this->weights,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }

        if(!empty($this->start_time) && strtotime($this->start_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->start_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->start_time));
            $query->andFilterWhere(['between','start_time',$startTime,$endTime]);
        }

        if(!empty($this->end_time) && strtotime($this->end_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->end_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->end_time));
            $query->andFilterWhere(['between','end_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
