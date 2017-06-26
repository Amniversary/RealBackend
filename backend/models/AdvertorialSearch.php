<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/31
 * Time: 13:49
 */

namespace backend\models;

use common\models\Advertorial;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class AdvertorialSearch extends Advertorial{

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
            ->select(['advertorial_title','advertorial_content','create_time','record_id'])
            ->from('mb_advertorial');

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
            'create_time' => $this->create_time,
        ]);
        return $dataProvider;
    }
}