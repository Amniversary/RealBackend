<?php

namespace backend\models;

use common\models\Client;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class MasterProfitSearch extends MasterProfitForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id','client_id','client_no','is_contract','real_ticket_profit','sum_ticket_profit'], 'integer'],
            [['nick_name'], 'safe'],
            [['date'], 'string', 'min' => 10,'max'=>21,
                'tooShort'=> '{attribute} 长度不能小于{min}字符，且格式为yyyy-mm-dd',
                'tooLong' => '{attribute} 长度不能大于{max}个字符,且格式为yyyy-mm-dd_yyyy-mm-dd'],
            [['date'], 'requiredByASpecial'],
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
        $query = new Query();
        $query->select(['lm.record_id','ct.client_id','ct.client_no','ct.nick_name','is_contract','lm.statistic_time as date','statistic_num as sum_ticket_profit','real_tickets_num as real_ticket_profit'])
            ->from('mb_statistic_living_master lm')
            ->innerJoin('mb_client ct','lm.user_id = ct.client_id')
            ->where(['statistic_type'=>2])
            ->orderBy(['lm.statistic_time'=>SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'record_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        if(!empty($this->date))
        {
            $patten = "/^((((1[6-9]|[2-9]\d)\d{2})-(0?[13578]|1[02])-(0?[1-9]|[12]\d|3[01]))|(((1[6-9]|[2-9]\d)\d{2})-(0?[13456789]|1[012])-(0?[1-9]|[12]\d|30))|(((1[6-9]|[2-9]\d)\d{2})-0?2-(0?[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))-0?2-29-))$/";
            if(preg_match($patten,$this->date))
            {
                $query->andFilterWhere(['like', 'statistic_time', $this->date]);
            }else
            {
                $patten1 = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))_[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
                if(preg_match($patten1,$this->date)){
                    if(stripos($this->date,'_') == 10 && strlen($this->date) == 21)
                    {
                        $date = explode('_',$this->date);
                        $date[0] = trim($date[0]);
                        $date[1] = trim($date[1]);
                        $query->andFilterWhere(['between','statistic_time',$date[0],$date[1]]);
                    }
                }

            }

        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'statistic_time' => $this->date,
            'is_contract'=>$this->is_contract
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no]);

        return $dataProvider;
    }

    public function requiredByASpecial($attribute)
    {
        $query = new Query();
        $query->select(['lm.record_id', 'ct.client_id', 'ct.client_no', 'ct.nick_name', 'is_contract', 'lm.statistic_time as date', 'statistic_num as sum_ticket_profit', 'real_tickets_num as real_ticket_profit'])
            ->from('mb_statistic_living_master lm')
            ->innerJoin('mb_client ct', 'lm.user_id = ct.client_id')
            ->where(['statistic_type' => 2])
            ->orderBy(['lm.statistic_time' => SORT_DESC]);

        if (!empty($this->date)) {
            //判断查找一个日期
            $patten = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
            if (preg_match($patten, $this->date)) {
                $query->andFilterWhere(['like', 'statistic_time', $this->date]);
            } else {
                //判断查找某一段时间内的日期，开始日期和结束日期用'_'隔开
                $patten1 = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))_[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
                if (preg_match($patten1, $this->date)) {
                    if (stripos($this->date, '_') == 10 && strlen($this->date) == 21) {
                        $date = explode('_', $this->date);
                        $date[0] = trim($date[0]);
                        $date[1] = trim($date[1]);
                        $query->andFilterWhere(['between', 'statistic_time', $date[0], $date[1]]);
                    } else {
                        $this->addError($attribute, "请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd_yyyy-mm-dd.");
                    }
                } else {
                    $this->addError($attribute, "请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd_yyyy-mm-dd.");
                }
            }

        }
    }
}
