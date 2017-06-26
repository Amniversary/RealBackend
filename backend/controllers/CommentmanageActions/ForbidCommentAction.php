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
use frontend\business\WishUtil;
use yii\base\Action;

/**
 * Class MyBillMarkBadShowAction 坏账设置
 * @package backend\controllers\GetCashActions
 */
class ForbidCommentAction extends Action
{
    public function run($comment_id)
    {
        if(empty($comment_id))
        {
            ExitUtil::ExitWithMessage('评论id不能为空');
        }
        $comment =WishUtil::GetWishCommentById($comment_id);
        if(!isset($comment))
        {
            ExitUtil::ExitWithMessage('评论记录不存在');
        }
        $this->controller->getView()->title = '禁止评论';
        $this->controller->layout = 'main_empty';
        return $this->controller->render('financeshow',
            [
                'comment'=>$comment
            ]
        );
    }
} 