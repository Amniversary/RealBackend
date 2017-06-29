<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/16
 * Time: 11:11
 */

namespace backend\models;


use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class StatisticFamilyTicketSearch extends StatisticFamilyTicketForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id','family_id'], 'integer'],
            [['income_ticket','ticket_to_cash','family_name'], 'safe'],
            [['create_time'],'CheckCreateTime']
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
        $query = (new Query())
            ->select(['fm.family_name','fm.family_id','ft.income_ticket','ft.ticket_to_cash','ft.create_time','ft.record_id'])
            ->from('mb_statistic_family_ticket ft')
            ->innerJoin('mb_family fm','fm.family_id=ft.family_id')
            ->orderBy(['ft.create_time' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->andFilterWhere([
                'ft.record_id' => -100,   //验证错误时设置错误数据
            ]);
            return $dataProvider;
        }

        $date = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
        if(!empty($this->create_time))
        {
            $create_time = explode('|',$this->create_time);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $time = preg_match ($date,$this->create_time);
                if($time)   //只输入了一个日期
                {
                    $query->andFilterWhere(['between','ft.create_time',date('Y-m-d',strtotime($this->create_time)),date('Y-m-d',strtotime($this->create_time))]);
                }
                else
                {
                    $query->andFilterWhere(['between','ft.create_time',date('Y-m-d'),date('Y-m-d')]);  //输入日期有误，搜索默认数据
                }
            }
            else
            {
                $query->andFilterWhere(['between','ft.create_time',date('Y-m-d',strtotime($create_time[0])),date('Y-m-d',strtotime($create_time[1]))]);
            }
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'ft.record_id' => $this->record_id,
            'fm.family_id'=>$this->family_id,
        ]);

        $query->andFilterWhere(['like', 'fm.family_name', $this->family_name])
            ->andFilterWhere(['like','ft.income_ticket', $this->income_ticket])
            ->andFilterWhere(['like', 'ft.ticket_to_cash', $this->ticket_to_cash]);

        return $dataProvider;
    }

    public function CheckCreateTime($attribute)
    {
        $date = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";

        if(preg_match ($date,$this->create_time))
        {
            //只输入了一个日期   验证成功
            return true;
        }
        else
        {
            $create_time = explode('|',$this->create_time);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd|yyyy-mm-dd");
                //验证失败
                return false;
            }
        }

        return true;
    }
} 