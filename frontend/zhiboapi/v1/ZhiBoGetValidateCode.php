<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/23
 * Time: 11:01
 */

namespace frontend\zhiboapi\v1;


use common\components\ValidateCodeUtil;
use frontend\business\ApiCommon;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取验证码协议
 * Class ZhiBoGetValidateCode
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetValidateCode implements IApiExcute
{
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['phone_no','code_type'];
        $fieldLabels = ['手机号','验证码类型'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $deviceNo = $dataProtocal['device_no'];
        $phoneNo = $dataProtocal['data']['phone_no'];
        $code_type = $dataProtocal['data']['code_type'];
        if(!in_array($code_type,\Yii::$app->params['verify_code_type_list']))
        {
            $error = '验证码类型错误';
            return false;
        }
        if(!ValidateCodeUtil::SendValidate($phoneNo,$code_type,$error,$deviceNo))
        {
            return false;
        }
        $rstData['has_data']='1';
        $rstData['data_type']='string';
        $rstData['data']  = [];
        //未注册
        /*$hasRegister = false;
        $user = ClientUtil::GetClientByPhoneNo($phoneNo);
        if(isset($user))//有数据 已注册
        {
            $hasRegister = true;
        }

        //$rstData['data'] = ['status'=>($hasRegister? '2':'1')];*/
        return true;
    }
} 