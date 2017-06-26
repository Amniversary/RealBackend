<?php

namespace backend\models;

use common\models\Client;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\AccountInfo;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class ClientSearch extends Client
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id','client_no','status','is_inner','is_bind_weixin','is_bind_alipay','is_contract','is_centification','client_type'], 'integer'],
            [['nick_name','phone_no', 'create_time','sex','device_no','register_type','remark4'], 'safe'],
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
        $query = Client::find()->where('client_id > 0');
        //$count = $query->count('client_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'client_id',
            'query' => $query,
            //'totalCount'=>$count,
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

        //正常查询不包含被禁用的用户
        //除非根据 client_id, nick_name, client_no, phone_no 查询
        $status = $this->status;
        if (!is_numeric($this->status)) {
            !count(array_filter([
                $this->client_id,
                $this->nick_name,
                $this->client_no,
                $this->phone_no,
            ])) && $status = 1;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'status' => $status,
            'sex'=>$this->sex,
            'is_inner' => $this->is_inner,
            'is_bind_weixin' =>$this->is_bind_weixin,
            'is_bind_alipay' =>$this->is_bind_alipay,
            'is_contract'=>$this->is_contract,
            'register_type'=>$this->register_type,
            'is_centification'=>$this->is_centification,
            'cash_rite'=>$this->cash_rite,
            'client_type'=>$this->client_type,
            'device_no'=>$this->device_no,
            //'client_no'=>$this->client_no
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no])
            ->andFilterWhere(['like', 'phone_no', $this->phone_no]);

        $query->orderBy('create_time desc');
        
        return $dataProvider;
    }

    /**
     * todo: 主播列表
     * @param $params
     */
    public function searchWithLiving($params)
    {
        $this->load($params);
        $query = Client::find();
        // ->where(['IS NOT ', 'cash_rite', null])
        $dataProvider = new ActiveDataProvider([
            'key' => 'client_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $query->select([
            'client_id',
            'client_no',
            'nick_name',
            'is_contract',
            'cash_rite',
            'icon_pic',
            'mb_family.family_id as family_id',
            'mb_family.family_name as remark4',
        ]);
        $query->leftJoin('mb_family_member', 'mb_family_member.family_member_id = mb_client.client_id');
        $query->leftJoin('mb_family', 'mb_family.family_id = mb_family_member.family_id');

        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'is_contract' => $this->is_contract,
            'cash_rite' => $this->cash_rite,
        ]);
        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
              ->andFilterWhere(['like', 'client_no', $this->client_no])
              ->andFilterWhere(['like', 'family_name', $this->remark4]);
        return $dataProvider;
    }
}
