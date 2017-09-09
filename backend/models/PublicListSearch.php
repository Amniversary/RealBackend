<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/6/29
 * Time: 下午1:43
 */

namespace backend\models;


use common\models\AuthorizationList;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class PublicListSearch extends PublicListForm
{

    public function rules()
    {
        return [
            [['record_id' ,'service_type_info', 'verify_type_info'], 'integer'],
            [['nick_name','head_img'], 'safe'],
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
        $query = (new Query())
            ->select(['al.record_id','nick_name','service_type_info','verify_type_info','head_img', 'alarm_status','ifnull(new_user,0) as new_user','ifnull(net_user,0) as net_user','ifnull(count_user,0) as count_user'])
            ->from('wc_authorization_list al')
            ->innerJoin('wc_statistics_count sc','al.record_id = sc.app_id')
            ->leftJoin('wc_fans_statistics fs','al.record_id = fs.app_id and fs.statistics_date =:date',[':date'=>date('Y-m-d')]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'al.record_id'=>$this->record_id,
            'service_type_info'=>$this->service_type_info,
            'verify_type_info'=>$this->verify_type_info
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name]);

        return $dataProvider;
    }


    /**
     * 带标签参数请求
     * @param $params
     * @param $load
     * @return ActiveDataProvider
     */
    public function searchTag($params, $load)
    {
        $query = (new Query())
            ->select(['al.record_id','nick_name','service_type_info','verify_type_info','head_img', 'alarm_status','ifnull(new_user,0) as new_user','ifnull(net_user,0) as net_user','ifnull(count_user,0) as count_user'])
            ->from('wc_authorization_list al')
            ->innerJoin('wc_statistics_count sc','al.record_id = sc.app_id and al.record_id in ('.$params.')')
            ->leftJoin('wc_fans_statistics fs','al.record_id = fs.app_id and fs.statistics_date =:date',[':date'=>date('Y-m-d')]);

        $dataProvider = new ActiveDataProvider([
           'query' => $query
        ]);

        $this->load($load);

        if(!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'al.record_id' => $this->record_id,
            'service_type_info' => $this->service_type_info,
            'verify_type_info' => $this->verify_type_info
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name]);

        return  $dataProvider;
    }
}