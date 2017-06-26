<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:30
 */

namespace backend\models;

use common\models\ActivityTemplate;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class ActivityTemplateSearch extends ActivityTemplate{

	public function rules(){
		return [
			[['template_id', 'template_type'], 'integer'],
			[['template_title', 'file_name'], 'string'],
            [['remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
		];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params){
		//$sql = 'SELECT * FROM mb_level LEFT JOIN mb_level_stage ON mb_level.level_max=mb_level_stage.level_stage';
		$query = ActivityTemplate::find();

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
			'template_id' => $this->template_id,
			'template_title' => $this->template_title,
            'template_type' => $this->template_type,
			'file_name' => $this->file_name,

		]);
		return $dataProvider;
	}
}