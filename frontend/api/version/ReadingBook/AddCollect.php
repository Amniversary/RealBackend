<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/14
 * Time: 下午2:56
 */

namespace frontend\api\version\ReadingBook;


use common\models\Collect;
use frontend\api\IApiExecute;
use frontend\business\ClientUtil;
use frontend\business\DynamicUtil;

class AddCollect implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($data, $error)) return false;
        $dynamic_id = $data['data']['dynamic_id'];
        $Dynamic = DynamicUtil::getDynamicById($dynamic_id);
        if (empty($Dynamic)) {
            $error = '收藏失败, 动态不存在或已删除';
            return false;
        }

        $user_id = $data['data']['user_id'];
        $collect = DynamicUtil::getCollect($user_id, $dynamic_id);
        if(!empty($collect)) {
            $error = '您已经收藏过了';
            return false;
        }
        $User = ClientUtil::getBookUserById($user_id);
        $model = new Collect();
        $model->user_id = $User->client_id;
        $model->dynamic_id = $dynamic_id;
        if (!$model->save()) {
            $error = '收藏失败';
            \Yii::error($error . ' ' . var_export($model->getErrors(), true));
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = '收藏成功';
        return true;
    }

    private function check_params($dataTotal, &$error)
    {
        $files = ['user_id', 'dynamic_id'];
        $filesLabel = ['用户id', '动态id'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataTotal['data'][$files[$i]]) || empty($dataTotal['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }

}