<?php
namespace backend\models;

use common\models\ActivityPrize;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class ActivityPrizeSearch extends ActivityPrize
{

	public function rules()
    {
        return [
            [['activity_id', 'grade', 'number', 'total_number', 'last_number', 'type','order_no'], 'integer'],
            [['rate'], 'number'],
            [['gift_name', 'remark1', 'remark2', 'remark3', 'remark4','pic'], 'string', 'max' => 100],
            [['unit'], 'string', 'max' => 10],
        ];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params)
    {
		//$sql = 'SELECT * FROM mb_level LEFT JOIN mb_level_stage ON mb_level.level_max=mb_level_stage.level_stage';
		$query = ActivityPrize::find()->orderBy('grade asc');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 15
			]
		]);

		$this->load($params);

		if(!$this->validate()){
			return $dataProvider;
		}

        $query->andFilterWhere([
            'prize_id' => $this->prize_id,
            'activity_id' => $this->activity_id,
            'grade' => $this->grade,
            'type' => $this->type,
        ]);

        $query->andFilterWhere(['like','total_number',$this->total_number])
            ->andFilterWhere(['like','unit',$this->unit])
            ->andFilterWhere(['like','rate',$this->rate])
            ->andFilterWhere(['like','order_no',$this->order_no])
            ->andFilterWhere(['like','last_number',$this->last_number]);

        return $dataProvider;
	}


}