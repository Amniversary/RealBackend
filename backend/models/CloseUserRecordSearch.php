<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/1
 * Time: 14:47
 */

namespace backend\models;


use common\models\CloseIdLog;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CloseUserRecordSearch extends CloseIdLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'client_no', 'manage_id','operate_type'], 'integer'],
            [['nick_name', 'manage_name','create_time','remark1'], 'safe'],
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
        $query = CloseIdLog::find()->where(['management_type'=>1]);

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


        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }


        $query->andFilterWhere([
            'log_id'=>$this->log_id,
            'client_no'=>$this->client_no,
            'manage_id'=>$this->manage_id,
            'operate_type'=>$this->operate_type,
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
              ->andFilterWhere(['like', 'manage_name',$this->manage_name]);

        $query->orderBy('create_time desc');

        return $dataProvider;
    }
} 