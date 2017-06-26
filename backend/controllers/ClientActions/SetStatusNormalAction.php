<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/29
 * Time: 14:42
 */

namespace backend\controllers\ClientActions;

use frontend\business\CarouselUtil;
use frontend\business\ClientUtil;
use frontend\business\HotWordsUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\ReportUtil;
use frontend\business\UserAccountInfoUtil;
use yii\base\Action;

class SetStatusNormalAction extends Action
{
    public function run($client_id)
    {
        $rst =['message'=>'','output'=>''];
        if(empty($client_id))
        {
            $rst['message'] = '用户id不能为空';
            echo json_encode($rst);
            exit;
        }
        $client = ClientUtil::GetClientById($client_id);
        if(!isset($client))
        {
            $rst['message'] = '用户记录不存在';
            echo json_encode($rst);
            exit;
        }
        $client->status = 1;

        $seal_reason = '客户信息处解禁';
        if(!ClientUtil::SetBanUser($client,$seal_reason,$error))
        {
            $rst['message'] = $error;
            echo json_encode($rst);
            exit;
        }

        return $this->controller->redirect(['index']);
    }
}