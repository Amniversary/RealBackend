<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/9/14
 * Time: 下午1:40
 */

namespace frontend\api\version\ReadingBook;


use frontend\api\IApiExecute;
use frontend\business\DynamicUtil;

class DelDynamic implements IApiExecute
{
    function execute_action($data, &$rstData, &$error, $extendData = [])
    {
        if(!isset($data['data']['dynamic_id']) || empty($data['data']['dynamic_id'])) {
            $error = '动态id , 不能为空';
            return false;
        }
        $Dynamic = DynamicUtil::getDynamicById($data['data']['dynamic_id']);
        if(empty($Dynamic)) {
            $error = '删除失败, 动态记录不存在或已删除';
            return false;
        }
        if(!$Dynamic->delete()) {
            $error = '删除失败';
            \Yii::error($error . ' ' .var_export($Dynamic->getErrors(),true));
            return false;
        }
        $rstData['code'] = 0;
        return true;
    }
}