<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:49
 */

namespace backend\models;

use common\models\Client;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class ApporveClientSearch extends Client{

    public function rules(){
        return [
            [['client_id','client_no','status','is_inner','is_bind_weixin','is_bind_alipay','is_contract','is_centification','client_type'], 'integer'],
            [['nick_name','phone_no', 'create_time','sex','register_type'], 'safe'],
        ];
    }

    public function scenarios(){
        return Model::scenarios();
    }

    public function search($params){
        $query = (new Query())
            ->select(['mc.client_id','mc.client_no','mc.status','sex','is_inner','is_bind_weixin','is_bind_alipay','is_contract','register_type','is_centification','cash_rite','client_type','actual_name','id_card'])
            ->from('mb_client mc')
            ->leftJoin('mb_approve mp','mc.client_id = mp.client_id')
            ->where('mc.client_id > 0');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15
            ]
        ]);

        $this->load($params);

        if(!$this->validate()){
            return $dataProvider;
        }

        $query->andFilterWhere([
            'mc.client_id' => $this->client_id,
            'mc.client_no' => $this->client_no,
            'is_centification' => $this->is_centification,
        ]);
        return $dataProvider;
    }
}