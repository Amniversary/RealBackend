<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 14:25
 */

namespace backend\models;

use common\models\Advertise;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class AdvertiseSearch extends Advertise
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'string', 'max' => 100],
            [['status'], 'integer'],
            [['effe_time', 'end_time'], 'safe'],
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
        $query = Advertise::find()
                            ->orderBy('ordering DESC');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
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

        $query->andFilterWhere([
            'app_id' => $this->app_id,
            'effe_time' => $this->effe_time,
            'end_time' => $this->end_time,
            'status' => $this->status
        ]);

        if(!empty($this->effe_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->effe_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->effe_time));
            $query->andFilterWhere(['between','effe_time',$start_time, $end_time]);
        }


        if(!empty($this->end_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->end_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->end_time));
            $query->andFilterWhere(['between','end_time',$start_time, $end_time]);
        }

        return $dataProvider;
    }
}