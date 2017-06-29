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

class PublicListSearch extends AuthorizationList
{

    public function rules()
    {
        return [
            [['status', 'user_id', 'service_type_info', 'verify_type_info'], 'integer'],
            [['authorizer_appid', 'authorizer_access_token', 'authorizer_refresh_token','nick_name','user_name',
                'alias','qrcode_url','principal_name','create_time','head_img', 'signature', 'update_time'], 'safe'],
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
        $query = AuthorizationList::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'record_id'=>$this->record_id,
            'head_img'=>$this->head_img,
            'service_type_info'=>$this->service_type_info,
            'verify_type_info'=>$this->verify_type_info
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name]);

        return $dataProvider;
    }
}