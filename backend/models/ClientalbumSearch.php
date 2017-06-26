<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/8
 * Time: 16:35
 */

namespace backend\models;

use common\models\Client;
use common\models\FriendsCircle;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * AccountInfoSearch represents the model behind the search form about `common\models\AccountInfo`.
 */
class ClientalbumSearch extends FriendsCircle
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dynamic_id','dynamic_type', 'status','client_no'], 'integer'],
            [['create_time'], 'safe'],
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
        $query = (new Query() )
            ->select(['mfc.dynamic_id','mfc.click_num','mfc.comment_num','mfc.check_num','mfc.dynamic_type','mfc.red_pic_money','mfc.status','mc.client_no','mfc.content','mc.nick_name','mfc.create_time','mfc.pic','mfc.user_id'])
            ->from('mb_friends_circle mfc')
            ->innerJoin('mb_client mc','mc.client_id=mfc.user_id')
            ->where('mfc.status=1')
            ->orderBy('mfc.create_time desc');


        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'key'=>'client_id',
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],

        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }


        // grid filtering conditions
        $query->andFilterWhere([
            'client_no' => $this->client_no,
            'click_num' => $this->click_num,
            'comment_num'=>$this->comment_num,
            'dynamic_type' => $this->dynamic_type,
            'red_pic_money' =>$this->red_pic_money,
            'content' =>$this->content,
            'create_time'=>$this->create_time,
            'pic'=>$this->pic,
            'dynamic_id'=>$this->dynamic_id,
            'nick_name'=>$this->nick_name
        ]);

        $query->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like' ,'client_no',$this->client_no])
            ->andFilterWhere(['like', 'dynamic_type', $this->dynamic_type])
            ->andFilterWhere(['like', 'dynamic_id', $this->dynamic_id])
            ->andFilterWhere(['like', 'create_time', $this->create_time]);

        return $dataProvider;
    }
}
