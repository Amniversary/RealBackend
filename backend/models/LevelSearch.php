<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/23
 * Time: 19:36
 */

namespace backend\models;


use common\models\LevelStage;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class LevelSearch extends LevelStage
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level_no', 'level_stage', 'font_size'], 'integer'],
            [['color', 'level_pic','level_bg','remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = LevelStage::find();
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
            'level_no' => $this->level_no,
            'level_stage' =>$this->level_stage,
            'font_size'=>$this->font_size,
            'color' =>$this->color,
            'level_pic' =>$this->level_pic,
            'level_bg' =>$this->level_bg
        ]);

        return $dataProvider;
    }
} 