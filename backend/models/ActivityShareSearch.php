<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/28
 * Time: 14:13
 */

namespace backend\models;


use common\models\ActivityShareInfo;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class ActivityShareSearch extends ActivityShareInfo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['activity_id', 'client_no', 'prize_type',  'is_send', 'is_direct_send','express_num'], 'integer'],
            //[['prize_user_name','reward_name','prize_user_site','create_time','nick_name'], 'safe'],
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
        $query = ActivityShareInfo::find();
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

        if(!empty($this->create_time))
        {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:59:59',strtotime($this->create_time));
            $query->andFilterWhere(['between','create_time',$start_time, $end_time]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'share_id'=>$this->share_id,
            'type'=>$this->type,
            'title'=>$this->title,
            'content'=>$this->content,
            'pic'=>$this->pic,
        ]);


        return $dataProvider;
    }
}