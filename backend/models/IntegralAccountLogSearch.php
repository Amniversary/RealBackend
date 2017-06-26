<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\GoldsAccountLog;
use common\models\IntegralAccountLog;
use common\models\Client;
/**
 * IntegralAccountLogSearch represents the model behind the search form about `common\models\IntegralAccountLog`.
 */
class IntegralAccountLogSearch extends IntegralAccountLog
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['device_type', 'operate_type'], 'integer'],
            [['operate_value', 'before_balance', 'after_balance'], 'number'],
            [['create_time'], 'safe'],
            [['create_time'], 'validateDate'],
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
    public function search($params){

        $query = IntegralAccountLog::find()
            ->where('operate_type in(1,2) and integral_account_id ='.$params['integral_account_id'])->orderBy('create_time desc')->orderBy('log_id desc');

        $dataProvider = new ActiveDataProvider([
             'query' => $query,
             'pagination' => [
                'pageSize' => 15,
             ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'device_type' => $this->device_type,
            'operate_type' =>$this->operate_type,
        ]);

        $query->andFilterWhere(['like', 'operate_value', $this->operate_value])
            ->andFilterWhere(['like', 'before_balance', $this->before_balance])
            ->andFilterWhere(['like', 'after_balance', $this->after_balance]);

        if(!empty($this->create_time)){
            $create_time = explode('|', $this->create_time);
            $start_time = $create_time[0];
            $end_time = $create_time[1];
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        return $dataProvider;
    }

    public function validateDate($attribute){
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9])?$/";
        if(!empty($this->create_time)){
            $create_time = explode('|',$this->create_time);
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
