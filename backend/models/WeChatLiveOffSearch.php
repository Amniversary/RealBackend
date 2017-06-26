<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/2
 * Time: 19:39
 */

namespace backend\models;


use common\models\WechatLivingOff;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class WeChatLiveOffSearch extends WechatLivingOff
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['log_id', 'client_no','operate_type'], 'integer'],
            [['user_name','create_time'], 'safe'],
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
        $query = WechatLivingOff::find();

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


        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }


        $query->andFilterWhere([
            'log_id'=>$this->log_id,
            'client_no'=>$this->client_no,
            'operate_type'=>$this->operate_type,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name]);
        $query->orderBy('create_time desc');

        return $dataProvider;
    }
} 