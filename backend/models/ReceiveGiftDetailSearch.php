<?php
/*
 * Created By SublimeText3
 * User: zff
 * Date: 2016/8/3
 * Time: 9:00
 */

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\log\Logger;


class ReceiveGiftDetailSearch extends GiftDetailForm{

	public function rules(){
		return [
            [['client_no'], 'integer'],
            [['gift_name'], 'string'],
            [['create_time'], 'safe'],
		];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params){
        $user_id = $params['user_id'];
        $query = (new Query())
            ->select(['r.multiple','r.total_gift_value','r.receive_rate','r.gift_type','r.gift_value','r.gift_name','r.living_master_id', 'c.client_no', 'c.nick_name', 'cb.before_balance', 'cb.after_balance', 'cb.create_time', 'cb.device_type'])
            ->from('mb_reward r')
            ->innerJoin('mb_client c','r.reward_user_id = c.client_id')
            ->innerJoin('mb_client_balance_log cb','r.reward_id = cb.relate_id and cb.operate_type = 7')
            ->orderBy(['r.create_time' => SORT_DESC ,'r.reward_id' => SORT_DESC])
            ->where('r.living_master_id=:id', [':id' => $user_id]);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10
			]
		]);

		$this->load($params);

        if(!$this->validate()){
            $query->andFilterWhere([
                'client_no' => 'aaa',
            ]);
            return $dataProvider;
        }
        //var_dump($this->create_time);
        \Yii::getLogger()->log('验证通过',Logger::LEVEL_ERROR);
        \Yii::getLogger()->log('CREATE:'.$this->create_time,Logger::LEVEL_ERROR);
//        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9]))?$/";
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])?$/";
        if(!empty($this->create_time))
        {
            $create_time = explode('|',$this->create_time);
            if (preg_match ( $date,$create_time[0]) && preg_match ( $date,$create_time[1]))
            {
                $query->andFilterWhere(['between','cb.create_time',date('Y-m-d',strtotime($create_time[0])).' 00:00:00',date('Y-m-d',strtotime($create_time[1])).' 23:59:59']);
            }
        }

		$query->andFilterWhere([
			'gift_name' => $this->gift_name,
            'client_no' => $this->client_no,
			//'create_time' => $this->create_time,


		]);
		return $dataProvider;
	}
    public function validateDate($attribute){
//        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9])\:(0?[0-9]|[1-5][0-9])?$/";
        $date = "/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])?$/";
        if(!empty($this->create_time))
        {
            $create_time = explode('|',$this->create_time);
            if (!preg_match ( $date,$create_time[0]) || !preg_match ( $date,$create_time[1]))
            {
                $this->addError($attribute,"请按正确格式填写，正确格式为yyyy-mm-dd|yyyy-mm-dd");
                return false;
            }
            $time= strtotime($create_time[1])-strtotime($create_time[0]);
            $day = floor($time/3600/24);

            if($day>30){
                $this->addError($attribute,"搜索时间间隔请不要超过30天");
                return false;
            }
        }
    }
}