<?php
/**
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/23
 * Time: 10:43
 */

namespace backend\controllers\CommentmanageActions;


use backend\components\ExitUtil;
use frontend\business\BillUtil;
use frontend\business\RewardUtil;
use frontend\business\WishUtil;
use yii\base\Action;

/**
 * Class ForbidRewardCommentAction 设置打赏留言屏蔽
 * @package backend\controllers\GetCashActions
 */
class ForbidRewardCommentAction extends Action
{
    public function run($reward_id)
    {
        if(empty($reward_id))
        {
            ExitUtil::ExitWithMessage('打赏id不能为空');
        }
        $reward =RewardUtil::GetRewardInfoById($reward_id);
        if(!isset($reward))
        {
            ExitUtil::ExitWithMessage('打赏记录不存在');
        }
        $this->controller->getView()->title = '禁止评论';
        $this->controller->layout = 'main_empty';
        return $this->controller->render('financeshowreward',
            [
                'reward'=>$reward
            ]
        );
    }
} 