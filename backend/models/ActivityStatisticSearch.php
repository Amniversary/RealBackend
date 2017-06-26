<?php
namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class ActivityStatisticSearch extends ActivityStatisticForm
{

	public function rules()
    {
        return [
            //[['activity_id','user_number','record_number'], 'integer'],
        ];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params)
    {
        //SELECT title,
//        MAX(CASE field_name WHEN 'record_id' THEN number ELSE 0 END ) record_number,
//MAX(CASE field_name WHEN 'user_id' THEN number ELSE 0 END )  user_number
//
//FROM mb_activity_info as ainfo
//LEFT JOIN mb_activity_statistic as ast ON ainfo.activity_id=ast.activity_id

		$query = (new Query())
            ->from('mb_activity_info ai')
            ->select(['title','ai.activity_id',' MAX(CASE field_name WHEN \'record_id\' THEN number ELSE 0 END ) record_number','MAX(CASE field_name WHEN \'user_id\' THEN number ELSE 0 END )  user_number'])
            ->leftJoin('mb_activity_statistic ast','ast.activity_id=ai.activity_id')
            ->orderBy(['ai.create_time' => SORT_DESC]);
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
//
//        $query->andFilterWhere([
//            'activity_id' => $this->activity_id,
//        ]);
//
//        $query->andFilterWhere(['like','user_number',$this->user_number])
//            ->andFilterWhere(['like','title',$this->title]);

        return $dataProvider;
	}


}