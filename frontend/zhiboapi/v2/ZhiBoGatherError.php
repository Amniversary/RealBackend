<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/10
 * Time: 14:21
 */

namespace frontend\zhiboapi\v2;

use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 收集前端日志信息  hbh
 * Class ZhiBoGatherError
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGatherError implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('dataProtoca;l:::'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        if(!$this->check_param_ok($dataProtocal, $error, $out))
        {
            return false;
        }

        $data = [
            'key_word' => 'gather_error',
            'device_type' => $dataProtocal['device_type'],
            'phone_model' => $out['phone_model'],
            'action_name' => (!isset($out['action_name']) ? '' : $out['action_name']),
            'os_version' => $out['os_version'],
            'error_after_data' => (!isset($out['error_after_data'])) ? '' : $out['error_after_data'],
            'encrypt_data' => (!isset($out['encrypt_data']))? '': $out['encrypt_data'],
            'encrypt_key' => (!isset($out['encrypt_key']))? '': $out['encrypt_key'],
            'token' => (!isset($out['token']))? '':$out['token'],
            'result' => (!isset($out['result']))? '':$out['result'],
            'package_name' => $out['package_name'],
            'error_log' => $out['error_log'],
        ];
        //\Yii::getLogger()->log('gather_data:'.var_export($data,true),Logger::LEVEL_ERROR);
        $jobSever = 'gatherBeanstalk';
        if(!JobUtil::AddCustomJob($jobSever,'gather_error',$data,$error))
        {
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }


    private function check_param_ok($dataProtocal,&$error='', &$out)
    {
        $data = explode('[-]',$dataProtocal['data']);
        $len = count($data);
        $out = [];
        for($i = 0; $i < $len; $i++)
        {
            if(($i+1) % 2 != 0)
            {
                $out[$data[$i]] = $data[$i+1];
            }
        }
        //\Yii::getLogger()->log('test:::'.var_export($out,true),Logger::LEVEL_ERROR);
        $fields = ['phone_model', 'os_version', 'package_name','error_log'];
        $fieldLabels = ['机型信息', '操作系统版本号', '包名', '日志信息'];
        $len = count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($out[$fields[$i]]) || empty($out[$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }
} 