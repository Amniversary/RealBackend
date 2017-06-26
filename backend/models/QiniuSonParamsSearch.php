<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/28
 * Time: 14:38
 */

namespace backend\models;

use common\models\SonLivingParameters;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class QiniuSonParamsSearch extends SonLivingParameters
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            /*[['', ], 'integer'],
            [['',], 'safe'],*/
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
        $query = SonLivingParameters::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'profile_id' => $this->profile_id,
            'profilelevel'=>$this->profilelevel,
            'max_video_size' => $this->max_video_size,
            'max_fps' => $this->max_fps,
            'max_video_bit_rate' => $this->max_video_bit_rate,
        ]);

        $query->andFilterWhere(['like', 'profilelevel', $this->profilelevel]);


        return $dataProvider;
    }
} 