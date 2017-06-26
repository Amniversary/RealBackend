<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2
 * Time: 18:40
 */
namespace backend\models;


use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use common\models\StopLiving;

class StopLivingSearch extends StopLiving
{
    /**
     * @inheritdoc
     */
    public function rules(){
        return [
            [['stop_id', 'client_id', 'client_no','living_id', 'status'], 'integer'],
            [['phone_no'], 'number'],
            [['nick_name'], 'safe'],
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
        $query = (new Query())
            ->select(['ms.stop_id','mc.client_id','mc.client_no','mc.phone_no','ml.living_id' ,'mc.nick_name','ms.status', 'ms.create_date'])
            ->from('mb_stop_living ms')
            ->innerJoin('mb_living ml','ml.living_id = ms.living_id')
            ->innerJoin('mb_client mc','mc.client_id = ml.living_master_id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        /*echo "<pre>";
        var_dump($dataProvider);exit;*/
        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions

        $query->andFilterWhere([
            'mc.client_id' => $this->client_id,
            'mc.client_no' =>$this->client_no,
            'ms.living_id'=>$this->living_id,
            'ms.status'   =>$this->status,
        ]);

        // 模糊查找
        $query->andFilterWhere(['like', 'nick_name', $this->nick_name]);
        $query->andFilterWhere(['like', 'phone_no', $this->phone_no]);

        $query->orderBy('create_date desc');
        
        return $dataProvider;
    }
}