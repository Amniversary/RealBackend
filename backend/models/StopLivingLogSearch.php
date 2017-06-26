<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/3
 * Time: 16:17
 */

namespace backend\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StopLivingLog;

class StopLivingLogSearch extends StopLivingLog
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [

            [['log_id','operate_type','manage_type'], 'integer'],
            [['nick_name', 'remark4'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios(){
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
        // $query = StopLivingLog::find()->where(['living_id'=>$params['living_id']]);
        $query = StopLivingLog::find();

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
        $query->select('mb_stop_living_log.*, cl.client_no as remark4');
        $query->innerJoin('mb_client cl', 'cl.client_id = mb_stop_living_log.client_id');

        // grid filtering conditions

        $query->andFilterWhere([
            'manage_id'    =>$this->manage_id,
            'operate_type' =>$this->operate_type,
            'manage_type' =>$this->manage_type,
        ]);

        $query->andFilterWhere([
            'client_no' => $this->remark4
        ]);

        // var_dump($this);exit;
        $query->andFilterWhere(['like', 'mb_stop_living_log.nick_name', $this->nick_name]);

        $query->andFilterWhere(['like', 'manage_name', $this->manage_name]);

        if(!empty($this->create_date)){
            $create_time = explode('|', $this->create_date);
            $start_time = $create_time[0];
            $end_time = $create_time[1];
            $query->andFilterWhere(['between','create_date',$start_time, $end_time]);
        }

        $query->orderBy('create_date desc');
        return $dataProvider;
    }

    public function validateDate($attribute){
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9])?$/";
        if(!empty($this->create_date)){
            $create_time = explode('|',$this->create_date);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd hh:mm:ss|yyyy-mm-dd hh:mm:ss");
                return false;
            }
            $time= strtotime($create_time[1])-strtotime($create_time[0]);
            $day = floor($time/3600/24);

            if($day>15){
                $this->addError($attribute,"搜索时间间隔请不要超过15天");
                return false;
            }
        }
    }
}