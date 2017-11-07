<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/3
 * Time: 下午4:04
 */

namespace backend\models;


use common\components\UsualFunForNetWorkHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class CashAuditSearch extends Model
{
    public $id;
    public $user_id;
    public $nick_name;
    public $open_id;
    public $money;
    public $result_money;
    public $status;
    public $name;
    public $create_at;
    public $cash_rate;
    public $err_reason;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'user_id'], 'integer'],
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
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->getCashList();
        if ($this->load($params)) {
            foreach ($params['CashAuditSearch'] as $key => $value) {
                if (!isset($value) || $value == '') continue;
//                var_dump($params['CashAuditSearch']);
//                var_dump($key);
//                var_dump($value);
                switch ($key) {
                    case 'id':
                        $query = $this->searchId($query);
                        break;
                    case 'status':
                        $query = $this->searchStatus($query);
                        break;
                    case 'user_id':
                        $query = $this->searchUserId($query);
                        break;
                }
                break;
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $query,
            'key' => 'id',
            'sort' => [
                'attributes' => ['id', 'create_at'],
            ],
        ]);

        if (!$this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

    public function getCashList()
    {
        $url = "https://16075509.ririyuedu.com/socket/response.do";
        $json = '{"action_name":"get_cash_audit", "data":"get_cash_audit"}';
        $header = ["servername:wedding"];
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json, $header), true);
        if ($rst['code'] !== 0) {
            \Yii::error($rst['code'] . ' :'.  $rst['msg']);
            return false;
        }
        return $rst['data'];
    }

    private function searchId($query)
    {
        $id = $this->id;
        $query = array_filter($query, function ($role) use ($id) {
            return (empty($id) || strpos((strtolower(is_object($role) ? $role->id : $role['id'])), $id) !== false);
        });
        return $query;
    }

    private function searchStatus($query)
    {
        $status = $this->status;
        $query = array_filter($query, function($rule) use ($status) {
            return (!isset($status) || $status == '' || strpos((strtolower(is_object($rule) ? $rule->status : $rule['status'])), $status) !== false);
        });
        return $query;
    }

    private function searchUserId($query) {
        $user_id = $this->user_id;
        $query = array_filter($query, function($rule) use ($user_id) {
            return (!isset($user_id) || $user_id == '' || strpos((strtolower(is_object($rule) ? $rule->user_id : $rule['user_id'])), $user_id) !== false);
        });
        return $query;
    }

}