<?php
/**
 * Created by PhpStorm.
 * User: a123
 * Date: 17/10/9
 * Time: 下午2:55
 */

namespace frontend\api\version;


use frontend\api\IApiExecute;
use frontend\business\ClientUtil;

class WxSetVip implements IApiExecute
{
    function execute_action($dataProtocol, &$rstData, &$error, $extendData = [])
    {
        if(!$this->check_params($dataProtocol, $error)) return false;
        $open_id = $dataProtocol['data']['openid'];
        $appid = $dataProtocol['data']['appid'];
        $auth = ClientUtil::getAuthOne($appid);
        $User = ClientUtil::getUserForOpenId($open_id, $auth->record_id);
        if(empty($User)) {
            return true;
        }
        if($User->is_vip == 1) {
            return true;
        }
        $User->is_vip = 1;
        $User->remark1 = date('Y-m-d H:i:s');
        if(!$User->save()) {
            $error = 'Vip状态更新失败';
            \Yii::error($error . '  ' . var_export($User->getErrors(),true));
            return false;
        }
        $rstData['code'] = 0;
        $rstData['data'] = '';
        return true;
    }

    private function check_params($dataTotal, &$error)
    {
        $files = ['appid', 'openid'];
        $filesLabel = ['AppId', 'OpenId'];
        $count = count($files);
        for ($i = 0; $i < $count; $i++) {
            if (!isset($dataTotal['data'][$files[$i]]) || empty($dataTotal['data'][$files[$i]])) {
                $error .= $filesLabel[$i] . ' 不能为空';
                return false;
            }
        }
        return true;
    }
}