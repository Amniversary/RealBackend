<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/11
 * Time: 17:04
 */
namespace backend\models;


use common\models\ChangeRecord;
use common\models\IntegralMall;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * Class GoodsSearch
 * @package backend\models
 */
class ChangeRecordSearch extends ChangeRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'change_state','client_no'], 'integer'],
            [['change_time'], 'safe'],
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
    public function SearchWhere($query,$params)
    {
        // add conditions that should always apply here

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


        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like','change_time', $this->change_time])
            ->andFilterWhere(['like','change_state', $this->change_state])
            ->andFilterWhere(['like','client_no', $this->client_no]);

        return $dataProvider;
    }

    /**
     * 已审核
     * @param $params
     * @return ActiveDataProvider
     */
    public function examineSearch($params){
        $query = (new Query())
            ->select(['mcr.user_id','mcr.user_name','mcr.gift_name','mcr.change_time','mcr.change_state','mcr.address','mcr.record_id','mc.client_no','mc.nick_name'])
            ->from('mb_change_record mcr')
            ->innerJoin('mb_client mc','mc.client_id=mcr.user_id')
            ->where('change_state!=0')
            ->orderBy('change_time desc');

        return $this->SearchWhere($query,$params);
    }

    /**
     * 未审核
     * @param $params
     * @return ActiveDataProvider
     */
    public function noexamineSearch($params){
        $query = (new Query())
            ->select(['mcr.user_id','mcr.user_name','mcr.gift_name','mcr.change_time','mcr.change_state','mcr.address','mcr.record_id','mc.client_no','mc.nick_name'])
            ->from('mb_change_record mcr')
            ->innerJoin('mb_client mc','mc.client_id=mcr.user_id')
            ->where('change_state=0')
            ->orderBy('change_time desc');

        return $this->SearchWhere($query,$params);

    }
}