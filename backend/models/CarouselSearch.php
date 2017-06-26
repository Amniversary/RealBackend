<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Carousel;

/**
 * CarouselSearch represents the model behind the search form about `common\models\Carousel`.
 */
class CarouselSearch extends Carousel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carousel_id', 'status','activity_type'], 'integer'],
            [['title', 'discribtion', 'pic_url', 'action_type', 'action_content', 'order_no', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = Carousel::find();

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
            'carousel_id' => $this->carousel_id,
            'status' => $this->status,
            'action_type'=> $this->action_type,
            'remark1' => $this->remark1,
            'remark2' => $this->remark2,
            'remark3' => $this->remark3,
            'remark4' => $this->remark4,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'activity_type', $this->activity_type])
            ->andFilterWhere(['like', 'discribtion', $this->discribtion]);

        return $dataProvider;
    }
}
