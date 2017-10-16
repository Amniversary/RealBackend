<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/13
 * Time: 下午12:18
 */

namespace frontend\api\version\ReadingBook;


use common\models\Comments;
use frontend\api\IApiExecute;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByTransactions\SaveByTransaction\AddCommentSaveByTrans;

class AddComment implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) return false;
        $type = $dataProtocol['data']['type'];
        $dynamic_id = $dataProtocol['data']['dynamic_id'];
        $Dynamic = DynamicUtil::getDynamicById($dynamic_id);
        if (empty($Dynamic)) {
            $error = '动态记录不存在或已删除';
            return false;
        }
        if ($type == 2) {
            if (!isset($dataProtocol['data']['parent_id'])) {
                $error = '评论id  不能为空';
                return false;
            }
        }
        $user_id = '';
        if (!empty($dataProtocol['data']['user_id'])) {
            $user_id = $dataProtocol['data']['user_id'];
            $User = ClientUtil::getBookUserById($user_id);
            $user_id = $User->client_id;
            if (empty($User))
                $user_id = '';
            unset($User);
        }
        $model = new Comments();
        $model->dynamic_id = $Dynamic->dynamic_id;
        $model->user_id = $user_id;
        $model->parent_id = $type == 2 ? intval($dataProtocol['data']['parent_id']) : 0;
        $model->content = $dataProtocol['data']['content'];
        $model->status = 1;
        $model->create_at = time();
        $transAction[] = new AddCommentSaveByTrans($model);
        if (!SaveByTransUtil::SaveByTransaction($transAction, $error, $out)) {
            return false;
        }

        $rstData['code'] = 0;
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['dynamic_id', 'content', 'type'];
        $filesLabel = ['动态id', '评论内容', '评论类型'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }

}