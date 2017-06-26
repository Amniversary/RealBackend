<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/19
 * Time: 17:18
 */

namespace backend\controllers\ChangeRecordActions;


use common\models\ChangeRecord;
use common\models\IntegralMall;
use frontend\business\BalanceUtil;
use frontend\business\ClientActiveUtil;
use frontend\business\GoldsAccountUtil;
use frontend\business\IntegralAccountUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ExperienceModifyByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use yii\base\Action;

class AllCheckAction extends Action
{
    public function run()
    {
        $ids = \Yii::$app->request->post('record_id');
        $change_state = \Yii::$app->request->post('change_state');
        $ids = explode('-',$ids);
        if(empty($ids))
        {
            $rst['msg'] = '审核id不能空';
            echo json_encode($rst);
            exit;
        }

        if($change_state == 1)
        {
            foreach($ids as $id)
            {
                $user_info = ChangeRecord::findOne(['record_id'=>$id]);
                $gift_type =IntegralMall::findOne(['gift_name'=>$user_info['gift_name']]);

//                if(!($gift_type['gift_type']<200))
//                {
//                    $rst['code']='1';
//                    $rst['msg']='该礼物不是官方虚拟礼物';
//                    echo json_encode($rst);
//                    exit;
//                }
                //1是游戏币
                if($gift_type['gift_type'] == 1)
                {
                    //获取账户ID
                    $gold = GoldsAccountUtil::GetGoldsAccountInfoByUserId($user_info['user_id']);
                    //调用新增金币的方法
                    if(!GoldsAccountUtil::UpdateGoldsAccountToAdd($gold['gold_account_id'],$user_info['user_id'],3,1,$gift_type['gift_send_num'],$error))
                    {
                        $rst['msg']= $error;
                        $rst['code']='1';
                        echo json_encode($rst);
                        exit;
                    };
                }

                //2是鲜花
                if($gift_type['gift_type'] == 2)
                {
                    //得到用户剩下的鲜花数量
                    $balance = BalanceUtil::GetUserBalanceByUserId($user_info['user_id']);
                    $transActions[] = new ModifyBalanceByAddRealBean($balance,['bean_num'=>$gift_type['gift_send_num']]);

                    if(!RewardUtil::RewardSaveByTransaction($transActions, $out, $error))
                    {
                        $rst['code']='1';
                        $rst['msg']=$error;
                        echo json_encode($rst);
                        exit;
                    }
                }

                //3是经验值
                if($gift_type['gift_type'] == 3)
                {
                    $active_info = ClientActiveUtil::GetClientActiveInfoByUserId($user_info['user_id']);
                    $transAction[] = new ExperienceModifyByTrans($active_info,['experience_num'=>$gift_type['gift_send_num']]);
                    if(!RewardUtil::RewardSaveByTransaction($transAction, $out, $error))
                    {
                        $rst['code']='1';
                        $rst['msg']=$error;
                        echo json_encode($rst);
                        exit;
                    }
                }

                $sql = 'update mb_change_record set change_state = :cs WHERE record_id = :rid';
                \yii::$app->db->createCommand($sql,[':cs'=>$change_state,':rid'=>$id])->execute();

            }

            $rst['code']='0';
            $rst['msg']='通过审核成功';
            echo json_encode($rst);
            exit;
        }
        else
        {
            foreach($ids as $id)
            {
                $user_info = ChangeRecord::findOne(['record_id'=>$id]);
                $gift_type =IntegralMall::findOne(['gift_name'=>$user_info['gift_name']]);

                //获取账户ID
                $u_int = IntegralAccountUtil::GetIntegralAccountModle($user_info['user_id']);
                //调用新增积分方法
                if(!IntegralAccountUtil::RollBackUserIntegral($u_int['integral_account_id'],$user_info['user_id'],3,1,$gift_type['gift_integral'],$user_info['change_time'],$error))
                {
                    $rst['msg']= $error;
                    $rst['code']='1';
                    echo json_encode($rst);
                    exit;
                };

                //修改用户状态
                $sql = 'update mb_change_record set change_state = :cs WHERE record_id = :rid';
                \yii::$app->db->createCommand($sql,[':cs'=>$change_state,':rid'=>$id])->execute();

            }

            $rst['code']='0';
            $rst['msg']='拒绝成功'.$id;
            echo json_encode($rst);
            exit;
        }

        exit;

    }
}