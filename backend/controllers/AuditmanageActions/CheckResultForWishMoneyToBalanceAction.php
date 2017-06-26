<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/29
 * Time: 19:17
 */

namespace backend\controllers\AuditmanageActions;


use backend\business\BackendBusinessCheckUtil;
use frontend\business\BusinessCheckUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\UserActiveUtil;
use yii\base\Action;
use backend\components\ExitUtil;
use yii\data\ArrayDataProvider;
use yii\log\Logger;

/**
 * 审核愿望金额提现
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class CheckResultForWishMoneyToBalanceAction extends Action
{
    public function run()
    {
        $check_id = \Yii::$app->request->post('check_id');
        $check_rst = \Yii::$app->request->post('check_rst');
        $cancel_wish = \Yii::$app->request->post('cancel_wish');
        $back_money = \Yii::$app->request->post('back_money');
        $refuse_reason = \Yii::$app->request->post('refused_reason');
        $rst = ["code"=>"1","msg"=>""];
        if(empty($check_id))
        {
            $rst["msg"]='审核记录id为空，数据异常';
            echo json_encode($rst);
            exit;
        }
        if(!isset($check_rst) || !in_array($check_rst,['0','1']))
        {
            $rst['msg']='审核结果值异常';
            echo json_encode($rst);
            exit;
        }
        $checkRecord = BusinessCheckUtil::GetBusinessCheckById($check_id);
        if(!isset($checkRecord))
        {
            $rst["msg"]='审核记录不存在，数据异常';
            \Yii::getLogger()->log('审核记录不存在，数据异常,check_id:'.$check_id, Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        if($checkRecord->status === 1)
        {
            $rst['msg']='该记录已经审核';
            echo json_encode($rst);
            exit;
        }
        $error = '';
        $params = [
            'check_result'=>$check_rst,
            'business_check_id'=>$check_id,
            'user_id'=>\Yii::$app->user->id,
            'refused_reason'=>$refuse_reason,
            'cancel_wish'=>$cancel_wish,
            'back_money'=>$back_money
        ];
        if(!BackendBusinessCheckUtil::DealBusinessCheck($params,$error))
        {
            \Yii::getLogger()->log('审核愿望金额提现失败：'.$error,Logger::LEVEL_ERROR);
            $rst['msg'] = '审核愿望金额提现失败';
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }

    /**
     * 获取审核相关数据
     * @param $relate_id
     * @param $check_type
     */
    private function GetRelateData($relate_id, $check_type)
    {

    }
} 