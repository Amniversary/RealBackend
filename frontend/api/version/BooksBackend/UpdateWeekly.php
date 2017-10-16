<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午3:10
 */

namespace frontend\api\version\BooksBackend;


use common\models\Weekly;
use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class UpdateWeekly implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if (!$this->check_params($dataProtocol, $error)) {
            return false;
        }
        $id = $dataProtocol['data']['id'];
        $weekly = WeeklyUtil::GetWeekly($id);
        if (empty($weekly)) {
            $error = '周刊记录不存在';
            return false;
        }

        $weekly->title = $dataProtocol['data']['title'];
        $weekly->weeks = date('YW', $dataProtocol['data']['weeks']);
        $weekly->status = $dataProtocol['data']['status'];
        $weekly->update_time = date('Y-m-d H:i:s');
        if (!WeeklyUtil::SaveWeekly($weekly, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocol, &$error)
    {
        $files = ['id', 'title', 'weeks'];
        $filedLabel = ['周刊id', '周刊标题', '周刊周数'];
        $len = count($files);
        for ($i = 0; $i < $len; $i++) {
            if (!isset($dataProtocol['data'][$files[$i]]) || empty($dataProtocol['data'][$files[$i]])) {
                $error = $filedLabel[$i] . '不能为空';
                return false;
            }
        }
        if (!isset($dataProtocol['data']['status'])) {
            $error = '状态值不能为空';
            return false;
        }
        return true;
    }
}