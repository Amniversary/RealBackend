<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/3
 * Time: 9:00
 */

namespace backend\models;

use common\models\Level;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class LevelManageSearch extends Level{

	public function rules(){
		return [
			[['level_name', 'order_no', 'level_max'], 'integer'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
		];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params){
		//$sql = 'SELECT * FROM mb_level LEFT JOIN mb_level_stage ON mb_level.level_max=mb_level_stage.level_stage';
		$query = Level::find();

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
			'level_name' => $this->level_name,
			'experience' => $this->experience,
			'level_max' => $this->level_max,

		]);
		//$query->orderBy('level_name asc');
		return $dataProvider;
	}
}