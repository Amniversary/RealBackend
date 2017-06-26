<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/14
 * Time: 16:53
 */

namespace backend\controllers\ClientActions;


use common\models\Client;
use frontend\business\BalanceUtil;
use frontend\business\ClientUtil;
use yii\base\Action;
use yii\log\Logger;

class DeleteTicketAction extends Action
{
    public function run($client_id)
    {
        $rst=['code'=>'1','msg'=>''];

        if(empty($client_id))
        {
            $rst['msg']='用户id不能为空';
            echo json_encode($rst);
            exit;
        }

        $Balance = BalanceUtil::DeleteUserTicketCount($client_id);

        if(empty($Balance))
        {
            $rst['msg']='清除用户剩余提现数量失败';
            echo json_encode($rst);
            exit;
        }

        $rst=['code'=>'0','msg'=>'删除成功'];
        echo json_encode($rst);
        exit;

//        return $this->controller->redirect(['client_finance_index']);
    }
}
?>