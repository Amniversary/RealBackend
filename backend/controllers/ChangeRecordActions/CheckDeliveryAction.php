<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/18
 * Time: 17:37
 */

namespace backend\controllers\ChangeRecordActions;


use common\models\ChangeRecord;
use common\models\GoldsAccount;
use frontend\business\BalanceUtil;
use frontend\business\ClientActiveUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use yii\base\Action;
use yii\db\Query;

/**
 * 审核拒绝、通过
 * Class CreateAction
 * @package backend\controllers\UpdateAction
 */
class CheckDeliveryAction extends Action
{
    public function run()
    {
        $error = '';
        $pos = \Yii::$app->request->post();
        $r_id = $pos['record_id'];
        $r_state = $pos['change_state'];
        $g_name = $pos['gift_name'];
        $u_id = $pos['user_id'];


        //用户的积分返回用户并且修改用户的状态为2
        //根据用户的id拿到用户的积分
//        $u_integral = (new Query())
//            ->select('integral_account_balance as user_integral')
//            ->from('mb_integral_account')
//            ->where("user_id = :uid",[':uid'=>$u_id])
//            ->one();

        //根据礼物名称拿到礼物积分、类型、虚拟数量
        $g_integral = (new Query())
            ->select(['gift_integral','gift_type','gift_send_num'])
            ->from('mb_integral_mall')
            ->where("gift_name = :go",[':go'=>$g_name])
            ->one();


        //根据修改的状态判断用户是否通过审核
        //当修改的状态时1是设置为通过审核，其他都是为拒绝
        if($r_state == 1)
        {
            //1是游戏币，2是鲜花，3是经验值
//            if(!($g_integral['gift_type']<200))
//            {
//                $rst['code']='1';
//                $rst['msg']='该礼物不是官方虚拟礼物';
//                echo json_encode($rst);
//                exit;
//            }
            //1是游戏币
            if($g_integral['gift_type'] == 1)
            {
                //获取账户ID
                $gold = GoldsAccountUtil::GetGoldsAccountInfoByUserId($u_id);
                //调用新增金币的方法
                if(!GoldsAccountUtil::UpdateGoldsAccountToAdd($gold['gold_account_id'],$u_id,3,1,$g_integral['gift_send_num'],$error))
                {
                    $rst['msg']= $error;
                    $rst['code']='1';
                    echo json_encode($rst);
                    exit;
                };
            }
            //2是鲜花
            if($g_integral['gift_type'] == 2)
            {
                //得到用户剩下的鲜花数量
                $balance = BalanceUtil::GetUserBalanceByUserId($u_id);
                $transActions[] = new ModifyBalanceByAddRealBean($balance,['bean_num'=>$g_integral['gift_send_num']]);

                if(!RewardUtil::RewardSaveByTransaction($transActions, $out, $error))
                {
                    $rst['code']='1';
                    $rst['msg']=$error;
                    echo json_encode($rst);
                    exit;
                }
            }
            //3是经验值
            if($g_integral['gift_type'] == 3)
            {
                $active_info = ClientActiveUtil::GetClientActiveInfoByUserId($u_id);
                $transAction[] = new ExperienceModifyByTrans($active_info,['experience_num'=>$g_integral['gift_send_num']]);
                if(!RewardUtil::RewardSaveByTransaction($transAction, $out, $error))
                {
                    $rst['code']='1';
                    $rst['msg']=$error;
                    echo json_encode($rst);
                    exit;
                }
            }

            $sql = 'update mb_change_record set change_state = :cs WHERE record_id = :rid';
            \yii::$app->db->createCommand($sql,[':cs'=>$r_state,':rid'=>$r_id])->execute();

            $rst['code']='0';
            $rst['msg']='通过审核成功';
            echo json_encode($rst);
            exit;
        }
        else
        {
//            $Statistical_integral = $u_integral['user_integral'] + $g_integral['gift_integral'];

            //获取账户ID
            $u_int = IntegralAccountUtil::GetIntegralAccountModle($u_id);
            $user_info = ChangeRecord::findOne(['record_id'=>$r_id]);
            //调用新增积分方法
            if(!IntegralAccountUtil::RollBackUserIntegral($u_int['integral_account_id'],$u_id,3,1,$g_integral['gift_integral'],$user_info['change_time'],$error))
            {
                $rst['msg']= $error;
                $rst['code']='1';
                echo json_encode($rst);
                exit;
            };

            //            修改用户状态
            $sql = 'update mb_change_record set change_state = :cs WHERE record_id = :rid';
            \yii::$app->db->createCommand($sql,[':cs'=>2,':rid'=>$r_id])->execute();

            $rst['code']='0';
            $rst['msg']='通过审核成功';
            echo json_encode($rst);
            exit;
        }

    }
}