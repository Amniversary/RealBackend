<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\CommentmanageActions;


use backend\business\UserUtil;
use frontend\business\BillUtil;
use frontend\business\RewardUtil;
use frontend\business\WishUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * Class ForbidRewardAction 禁止打赏留言
 * @package backend\controllers\GetCashActions
 */
class ForbidRewardAction extends Action
{
    public function run($reward_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($reward_id))
        {
           $rst['msg'] = '打赏id不能为空';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $reward = RewardUtil::GetRewardInfoById($reward_id);
        if(!isset($reward))
        {
            $rst['msg'] = '打赏记录不存在';
            \Yii::getLogger()->log($rst['msg']. ' reward_id:'.$reward_id,Logger::LEVEL_ERROR );
            echo json_encode($rst);
            exit;
        }
        $user_id = \Yii::$app->user->id;
        $user = UserUtil::GetUserByUserId($user_id);
        if(!isset($user))
        {
            $rst['msg']= '后台用户信息不存在';
            echo json_encode($rst);
            exit;
        }
        $reward->remark4 = $reward->remark2.' 原因:'.$remark.' 屏蔽人id：'.strval($user_id).' 屏蔽人:'.$user->username;
        $reward->remark2 = '被屏蔽，请文明用语';

        $error = '';
        if(!RewardUtil::SaveRewardInfo($reward,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 