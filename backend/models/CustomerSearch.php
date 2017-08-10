<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/9
 * Time: 上午11:58
 */

namespace backend\models;



use backend\business\WeChatUserUtil;
use common\models\Client;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CustomerSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','app_id','subscribe', 'sex',  'subscribe_time','is_vip','invitation'], 'integer'],
            [['create_time','groupid', 'update_time','unionid','open_id', 'nick_name', 'language', 'remark','headimgurl'], 'safe']
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
        $cache = WeChatUserUtil::getCacheInfo();
        $query = Client::find()->where(
            ['and',['app_id'=>$cache['record_id']],
            ['between','update_time', date('Y-m-d H:i:s', strtotime('-2 day')),date('Y-m-d H:i:s') ],
            ['subscribe'=>1]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'sex'=>$this->sex,

        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id])
              ->andFilterWhere(['like', 'nick_name', $this->nick_name]);
        return $dataProvider;
    }
}