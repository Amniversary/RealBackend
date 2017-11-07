<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/11/6
 * Time: 下午5:03
 */

namespace backend\controllers\CashAuditActions;

use common\components\UsualFunForNetWorkHelper;
use yii\base\Action;

class UpdateAction extends Action
{
    public function run()
    {
        $rst = ['code'=>1, 'msg'=>''];
        $post = \Yii::$app->request->post('id');
        if(empty($post)) {
            $rst['msg'] = '请求参数为空';
            echo json_encode($rst); exit;
        }
        $url = "https://16075509.ririyuedu.com/socket/response.do";
        $json = '{"action_name":"up_cash_audit", "data":{"id":'.$post.'}}';
        $header = ["servername:wedding"];
        $result = json_decode(UsualFunForNetWorkHelper::HttpsPost($url, $json, $header), true);
        if ($result['code'] !== 0) {
            \Yii::error($result['code']. ' :'.$result['msg']);
            $rst['msg'] = $result['code'] . ' : '. $result['msg'];
            echo json_encode($rst['msg']); exit;
        }
        $rst['code'] = 0;
        echo json_encode($rst);
    }
}