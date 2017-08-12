<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/8/11
 * Time: 下午3:10
 */

namespace frontend\api\version;


use common\models\Weekly;
use frontend\api\IApiExecute;
use frontend\business\WeeklyUtil;

class UpdateWeekly implements  IApiExecute
{
    function execute_action($data, &$rstData,&$error, $extendData = [])
    {
        if(!$this->check_params($data, $error)) {
           return false;
        }
        $id = $data['data']['id'];
        $weekly = WeeklyUtil::GetWeekly($id);
        if(empty($weekly)) {
            $error = '周刊记录不存在';
            return false;
        }

        $weekly->title = $data['data']['title'];
        $weekly->weeks = $data['data']['weeks'];
        $weekly->status = $data['data']['status'];
        $weekly->update_time = date('Y-m-d H:i:s');
        if(!WeeklyUtil::SaveWeekly($weekly, $error)) {
            return false;
        }

        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataProtocal, &$error) {
        $files = ['id', 'title', 'weeks', 'status'];
        $filedLabel = ['周刊id', '周刊标题', '周刊周数', '状态值'];
        $len = count($files);
        for($i = 0; $i < $len ; $i++) {
            if(!isset($dataProtocal[$files[$i]]) || empty($dataProtocal[$files[$i]])) {
                $error = $filedLabel[$i] . '不能为空';
                return false;
            }
        }
       return true;
    }
}