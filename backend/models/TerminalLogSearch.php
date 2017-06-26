<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10
 * Time: 17:02
 */

namespace backend\models;


use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\TerminalLog;

class TerminalLogSearch extends TerminalLog
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['device_type'], 'integer'],
            [['create_time'], 'safe'],
            //[['create_time'], 'validateDate'],
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
        $query = TerminalLog::find()->orderBy("log_id desc");
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
            'device_type' => $this->device_type,
            'DATE_FORMAT(create_time,\'%Y-%m-%d\')'=>$this->create_time,
        ]);

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