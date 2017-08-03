<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/1
 * Time: 下午3:13
 */

namespace backend\models;


use backend\business\WeChatUserUtil;
use common\models\Template;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class TemplateSearch extends Template
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['app_id'], 'integer'],
            [['template_id', 'content', 'example','title', 'primary_industry', 'deputy_industry', 'remark1', 'remark2'], 'safe'],
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
        $query = Template::find()->where(['app_id'=>$cacheInfo['record_id']]);

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

}