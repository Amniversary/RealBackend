<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 19:56
 */

namespace backend\models;


use common\models\StatisticBalance;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatisticBalanceSearch extends StatisticBalance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['statistic_time'], 'CheckCreateTime'],

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
        $query = StatisticBalance::find()->orderBy(['statistic_time'=>SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            $query->andFilterWhere([
                'statistic_time' => -100,   //验证错误时设置错误数据
            ]);
            return $dataProvider;
        }
        $date = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";
        if(!empty($this->statistic_time))
        {
            $statistic_time = explode('|',$this->statistic_time);
            if (!preg_match ( $date,$statistic_time[0]) || !preg_match ( $date,$statistic_time[1]))
            {
                $time = preg_match ($date,$this->statistic_time);
                if($time)   //只输入了一个日期
                {
                    $query->andFilterWhere(['between','statistic_time',date('Y-m-d',strtotime($this->statistic_time)),date('Y-m-d',strtotime($this->statistic_time))]);
                }
                else
                {
                    $query->andFilterWhere(['between','statistic_time',date('Y-m-d'),date('Y-m-d')]);  //输入日期有误，搜索默认数据
                }
            }
            else
            {
                $query->andFilterWhere(['between','statistic_time',date('Y-m-d',strtotime($statistic_time[0])),date('Y-m-d',strtotime($statistic_time[1]))]);
            }
        }
        $query->andFilterWhere([
            'record_id' => $this->record_id,
            'daily_recharge' => $this->daily_recharge,
            'wx_recharge' =>$this->wx_recharge,
            'alipay_recharge' =>$this->alipay_recharge,
            'ios_recharge'=>$this->ios_recharge,
            'withdraw' => $this->withdraw,
        ]);

        return $dataProvider;
    }


    public function CheckCreateTime($attribute)
    {
        $date = "/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/";

        if(preg_match ($date,$this->statistic_time))
        {
            //只输入了一个日期   验证成功
            return true;
        }
        else
        {
            $statistic_time = explode('|',$this->statistic_time);
            if (!preg_match ( $date,$statistic_time[0]) || !preg_match ( $date,$statistic_time[1]))
            {
                $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd或者yyyy-mm-dd|yyyy-mm-dd");
                //验证失败
                return false;
            }
        }

        return true;
    }
} 