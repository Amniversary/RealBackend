<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/23
 * Time: 上午11:27
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\AuthSign;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SignUserSearch extends SignUserFrom
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id', 'user_id', 'sign_num'], 'integer'],
            [['update_time', 'user_name'], 'safe'],
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
        $query = (new Query())
            ->select(['id', 'was.app_id','was.user_id','al.nick_name','c.nick_name as user_name','sign_num','was.update_time'])
            ->from('wc_auth_sign was')
            ->leftJoin('wc_authorization_list al','was.app_id=al.record_id')
            ->leftJoin('wc_client c', 'was.user_id=c.client_id')
            ->where(['was.app_id'=>$cache['record_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if(!empty($this->update_time)) {
            $start_time = date('Y-m-d 00:00:00',strtotime($this->update_time));
            $end_time = date('Y-m-d 23:00:00',strtotime($this->update_time));
            $query->andFilterWhere(['between' , 'was.update_time', $start_time, $end_time]);
        }

        $query->andFilterWhere(['like', 'c.nick_name', $this->user_name]);

        return $dataProvider;
    }
}