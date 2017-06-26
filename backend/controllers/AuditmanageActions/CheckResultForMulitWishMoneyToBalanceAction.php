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
 * 批量审核愿望金额提现
 * Class IndexAction
 * @package backend\controllers\RedPacketsActions
 */
class CheckResultForMulitWishMoneyToBalanceAction extends Action
{
    public function run()
    {
        $check_id_ary = \Yii::$app->request->post('BusinessCheckIds');
        $check_rst = \Yii::$app->request->post('check_rst');
        $cancel_wish = \Yii::$app->request->post('cancel_wish');
        $back_money = \Yii::$app->request->post('back_money');
        $refuse_reason = \Yii::$app->request->post('refused_reason');
        $rst = ["code"=>"1","msg"=>""];
        if(empty($check_id_ary))
        {
            $rst["msg"]='请选择审核记录';
            echo json_encode($rst);
            exit;
        }
        if(!isset($check_rst) || !in_array($check_rst,['0','1']))
        {
            $rst['msg']='审核结果值异常';
            echo json_encode($rst);
            exit;
        }
        if(!isset($cancel_wish) || !in_array($cancel_wish,['0','1']))
        {
            $rst['msg']='取消愿望状态值异常';
            echo json_encode($rst);
            exit;
        }
        if(!isset($back_money) || !in_array($back_money,['0','1']))
        {
            $rst['msg']='取消愿望后退款状态值异常';
            echo json_encode($rst);
            exit;
        }
        $okCount = 0;
        $failCount = 0;
        foreach($check_id_ary as $check_id)
        {
            $checkRecord = BusinessCheckUtil::GetBusinessCheckById($check_id);
            if(!isset($checkRecord))
            {
                $failCount ++;
                \Yii::getLogger()->log('审核记录不存在，数据异常,check_id:'.$check_id, Logger::LEVEL_ERROR);
                continue;
            }
            if($checkRecord->status === 1)
            {
                $failCount ++;
                continue;
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
                $failCount ++;
                $rst['msg'] = $error;
                \Yii::getLogger()->log('愿望金额提现审核异常：'.' check_id:'.$check_id.$error,Logger::LEVEL_ERROR);
                //echo json_encode($rst);
                continue;
            }
            $okCount ++;
        }
        if($failCount === 0)
        {
            $rst['msg']='所有数据提交成功';
        }
        else
        {
            $rst['msg']=sprintf('失败了【%s】条，成功了【%s】条',$failCount,$okCount);
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