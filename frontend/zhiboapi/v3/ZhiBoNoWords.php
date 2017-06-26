<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\AttentionUtil;
use frontend\business\ChatGroupUtil;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;

/**
 * Class 禁言管理
 * @package frontend\zhiboapi\v2
 */
class ZhiBoNoWords implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['user_id','living_id','op_type'];//'wish_type_id',
        $fieldLabels = ['被禁言用户id','直播id','操作类型'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        if(!in_array($dataProtocal['data']['op_type'],['1','2']))
        {
            $error = '操作类型错误';
            return false;
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
//\Yii::getLogger()->log(var_export($dataProtocal, true),Logger::LEVEL_ERROR);
        $error = '';
        if(!$this->check_param_ok($dataProtocal, $error))
        {
            return false;
        }
        $deviceNo = '';
        $uniqueNo= '';
        $registerType='';
        $deviceType='';
        if(!ApiCommon::GetBaseInfoFromProtocol($dataProtocal, $deviceNo, $uniqueNo,$registerType,$deviceType,$error))
        {
            return false;
        }
        $loginInfo = null;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$loginInfo, $error))
        {
            return false;
        }
        $is_police = ($loginInfo['client_type'] == '2'? 1 : 0);
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $living_id = $passParams['living_id'];
        $shutup_user_id = $passParams['user_id'];
        $op_type = $passParams['op_type'];
        $client_info = ClientUtil::GetClientById($shutup_user_id);
        if($client_info->client_type == '2')
        {
            //超管屏蔽不做任何处理，返回成功
            $rstData['data']['message'] = $client_info->nick_name."已被禁言";
            return true;
        }
        if(!ChatGroupUtil::ShutupForGrooupMember($living_id,$user_id,$shutup_user_id,$op_type,$error,$is_police))
        {
            return false;
        }
        //\Yii::getLogger()->log(var_export($out, true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息
        $rstData['data']['message'] = $client_info->nick_name."已被禁言";
        return true;
    }
} 