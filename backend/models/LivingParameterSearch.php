<?php

namespace backend\models;

use common\models\LivingParameters;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class LivingParameterSearch extends LivingParameters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fps', 'profilelevel', 'video_bit_rate','width','height'], 'integer'],
            [['quality', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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
        $query->select(['quality_id','quality','fps','video_bit_rate','profilelevel','width','height'])
            ->from('mb_living_parameters')
            ->orderBy(['quality_id'=>SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'quality_id',
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

        // grid filtering conditions

        $query->andFilterWhere(['like', 'quality', $this->quality])
            ->andFilterWhere(['like', 'fps', $this->fps])
            ->andFilterWhere(['like', 'video_bit_rate', $this->video_bit_rate])
            ->andFilterWhere(['like', 'profilelevel', $this->profilelevel]);

        return $dataProvider;
    }
}
