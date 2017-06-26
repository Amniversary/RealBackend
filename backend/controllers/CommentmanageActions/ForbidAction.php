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
use frontend\business\WishUtil;
use yii\base\Action;
use yii\log\Logger;

/**
 * Class MyBillMarkBadAction 设置坏账
 * @package backend\controllers\GetCashActions
 */
class ForbidAction extends Action
{
    public function run($comment_id)
    {
        $rst=['code'=>'1','msg'=>''];
        if(empty($comment_id))
        {
           $rst['msg'] = '评论id不能为空';
            echo json_encode($rst);
            exit;
        }
        $remark = \Yii::$app->request->post('remark');
        $comment = WishUtil::GetWishCommentById($comment_id);
        if(!isset($comment))
        {
            $rst['msg'] = '评论记录不存在';
            \Yii::getLogger()->log($rst['msg']. ' comment_id:'.$comment_id,Logger::LEVEL_ERROR );
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
        $comment->status = 0;
        $comment->remark2 = $remark;
        $comment->remark3 =strval($user->backend_user_id);
        $comment->remark4 = $user->username;
        $error = '';
        if(!WishUtil::ForbidComment($comment,$error))
        {
            $rst['msg'] = $error;
            echo json_encode($rst);
            exit;
        }
        $rst['code']='0';
        echo json_encode($rst);
    }
} 