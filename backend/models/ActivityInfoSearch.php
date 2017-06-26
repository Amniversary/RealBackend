<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/17
 * Time: 14:30
 */

namespace backend\models;

use common\models\ActivityInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class ActivityInfoSearch extends ActivityInfo{

	public function rules()
    {
		return [
            [['start_time', 'end_time', 'create_time'], 'safe'],
            [['status', 'type', 'template_id'], 'integer'],
            [['title', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],

		];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params)
    {
		//$sql = 'SELECT * FROM mb_level LEFT JOIN mb_level_stage ON mb_level.level_max=mb_level_stage.level_stage';
//		$query = ActivityInfo::find()->orderBy('status desc, create_time desc');

        $query = (new Query())
            ->select(['mai.activity_id','mai.start_time','mai.end_time','mai.title','mai.create_time','mai.status','mai.type','mai.template_id','mat.template_title'])
            ->from('mb_activity_info mai')
            ->innerJoin('mb_activity_template mat','mai.template_id = mat.template_id')
            ->orderBy('mai.status desc,mai.create_time desc');

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
			'title' => $this->title,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => $this->status,
            'type' => $this->type,
            'create_time' => $this->create_time,
            'mai.template_id' => $this->template_id,
		]);
		return $dataProvider;
	}


}