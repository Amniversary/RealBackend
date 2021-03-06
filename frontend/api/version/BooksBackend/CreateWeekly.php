<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午3:04
 */

namespace frontend\api\version\BooksBackend;


use common\models\Weekly;
use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class CreateWeekly implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $model = new Weekly();
        $model->title = $dataProtocol['data']['title'];
        $model->weeks = date('YW', $dataProtocol['data']['weeks']);
        $model->status = 1;
        $model->create_time = date('Y-m-d H:i:s');
        $model->update_time = date('Y-m-d H:i:s');
        if (!WeeklyUtil::SaveWeekly($model, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $fields = ['title', 'weeks'];
        $fieldLabels = ['周刊标题', '周刊周数'];
        $len = count($fields);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$fields[$i]]) || empty($dataProtocol['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '不能为空';
                return false;
            }
        }
        return true;
    }
}