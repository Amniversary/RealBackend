<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/2
 * Time: 下午10:03
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\AttentionEvent;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AttentionMsgSearch extends AttentionEvent
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'msg_type', 'flag'], 'integer'],
            [['event_id','create_time','content' ,'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $cacheInfo = WeChatUserUtil::getCacheInfo();
        $query = AttentionEvent::find()->where(['app_id'=>$cacheInfo['record_id'],'flag'=>0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if(!empty($this->create_time)){
            $start_time = date('Y-m-d 00:00:00',strtotime($this->create_time));
            $end_time = date('Y-m-d 23:00:00',strtotime($this->create_time));
            $query->andFilterWhere(['between' , 'create_time', $start_time, $end_time]);
        }
        $query->andFilterWhere([
            'msg_type' => $this->msg_type,
            'event_id' =>$this->event_id,
        ]);

        $query->orderBy('order_no asc');
        return $dataProvider;
    }
}