<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\Report;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class CheckReportSearch extends Report
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'scene', 'report_type', 'living_id', 'report_user_id', 'status'], 'integer'],
            [['client_no','report_client_no','report_content'], 'string'],
            [['create_time', 'check_time'], 'safe'],
            [['nick_name','report_user_name', 'remark1', 'remark2', 'remark3', 'remark4'], 'string', 'max' => 100],
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

    public function SearchWhere($query,$params){
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
            'report_id' => $this->report_id,
            'user_id'=>$this->user_id,
            'living_id' => $this->living_id,
            'report_type' => $this->report_type,
            'scene' => $this->scene,
            'bc.status'=>$this->status,
        ]);

        if(!empty($this->create_time) && strtotime($this->create_time) !== false)
        {
            $startTime = date('Y-m-d 00:00:00', strtotime($this->create_time));
            $endTime = date('Y-m-d 23:59:59', strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$startTime,$endTime]);
        }

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'report_user_name', $this->report_user_name]);
        return $dataProvider;
    }

    /**
     * 未审核
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = (new Query())
            ->select(['report_id','br.client_no','user_id','scene','br.nick_name','report_type','report_client_no','living_id','report_user_id','report_user_name','bc.status','br.create_time','check_time'])
            ->from('mb_report br')
            ->innerJoin('mb_client bc','br.user_id = bc.client_id')
            ->where('br.status = 1');
        $dataProvider = $this->SearchWhere($query,$params);
        return  $dataProvider;

    }

    //已审核
    public function AuditeSearch($params){
        $query = Report::find()->where('status=2');

        $dataProvider = $this->SearchWhere($query,$params);
        return  $dataProvider;
    }

}