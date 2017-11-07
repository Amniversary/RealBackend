<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/6
 * Time: 下午5:14
 */

namespace backend\controllers\CashAuditActions;


use backend\components\ExitUtil;
use common\components\UsualFunForNetWorkHelper;
use yii\base\Action;

class SelectAction extends Action
{
    public function run($id)
    {
        $url = "https://16075509.ririyuedu.com/socket/response.do";
        $json = '{"action_name":"get_cash_data", "data":{"id":'.$id.'}}';
        $header = ["servername:wedding"];
        $rst = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json, $header), true);
        if ($rst['code'] !== 0) {
            \Yii::error($rst['code']. ' :'.$rst['msg']);
            ExitUtil::ExitWithMessage($rst['msg']);
        }
        $this->controller->layout = 'main_empty';
        return $this->controller->render('select', [
            'model' => $rst['data']
        ]);
    }
}