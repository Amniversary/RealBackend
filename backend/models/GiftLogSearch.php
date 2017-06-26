<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\log\Logger;


class GiftLogSearch extends GiftLogForm{

	public function rules(){
		return [
            [['create_time'], 'safe'],
		];
	}

	public function scenarios(){
		return Model::scenarios();
	}

	public function search($params){
        $query = (new Query())
                ->select(['br.reward_user_id','br.living_master_id','br.gift_name','br.create_time','c.client_no', 'c.nick_name'])
                ->from('mb.reward br')
                ->leftJoin('mb_client c','br.living_master_id = c.client_id');


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        // var_dump( $dataProvider );
        // var_dump( $query );exit();

		return $dataProvider;
	}
}