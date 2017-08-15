<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/7/7
 * Time: 下午4:40
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\AuthorizationMenu;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CustomSearch extends AuthorizationMenu
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id','is_list'], 'integer'],
            [['name', 'type', 'key_type', 'remark1', 'remark2', 'remark3', 'remark4'], 'safe'],
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
        $query = AuthorizationMenu::find()->where(['app_id'=>$cacheInfo['record_id'],'parent_id'=>0]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([

        ]);

        return $dataProvider;
    }

    public function searchMenu($params)
    {
        $query = AuthorizationMenu::find()->where(['global'=>$params['id'],'parent_id'=>0]);

        $dataProvider = new ActiveDataProvider([
            'query'=> $query,
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }
}