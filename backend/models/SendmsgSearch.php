<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/10
 * Time: 14:00
 */

namespace backend\models;


use common\models\SendMsg;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class SendmsgSearch extends SendMsg
{
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function rules()
    {
        return [
            [['send_status','send_type'], 'integer'],
            [[ 'title','content'], 'string', 'max' => 100],
            [['send_time'], 'string', 'max' => 200]
        ];
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
        $query = Sendmsg::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);
        // grid filtering conditions
        $query->andFilterWhere([
            'like', 'title', $this->title
        ]);
        $query->andFilterWhere([
            'like', 'content', $this->content
        ]);

        $query->andFilterWhere([
            'send_type'   => $this->send_type,
            'send_status' => $this->send_status
        ]);

        if (isset($this->send_time)) {
            $send_time = $this->send_time;
            !empty($send_time['to']) && $send_time['to'] .= ' 23:59:59';
            $query->andFilterWhere([
                '>=', 'send_time', $send_time['from']
            ]);
            $query->andFilterWhere([
                '<=', 'send_time', $send_time['to']
            ]);
            // 防止数组转字符串报错
            $this->send_time = json_encode($this->send_time);
        }
        return $dataProvider;
    }
}