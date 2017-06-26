<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v1;

use frontend\business\AttentionUtil;
use frontend\business\ChatGroupUtil;
use frontend\business\ClientUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class 群管理员设置
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGroupManager implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['user_id','living_id','op_type'];//'wish_type_id',
        $fieldLabels = ['被设置管理员用户id','直播id','操作类型'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                //\Yii::getLogger()->log('empty:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
                return false;
            }
        }
        if(!in_array($dataProtocal['data']['op_type'],['1','2']))
        {
            \Yii::getLogger()->log('op_type:'.$dataProtocal['data']['op_type'],Logger::LEVEL_ERROR);
            $error = '操作类型错误';
            return false;
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
//\Yii::getLogger()->log('datadata=:'.var_export($dataProtocal, true),Logger::LEVEL_ERROR);
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
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $living_id = $passParams['living_id'];
        $shutup_user_id = $passParams['user_id'];
        $op_type = $passParams['op_type'];
        $client_info = ClientUtil::GetClientById($shutup_user_id);
        if(!ChatGroupUtil::SetGroupManager($living_id,$user_id,$shutup_user_id,$op_type,$error))
        {
            return false;
        }
        //\Yii::getLogger()->log(var_export($out, true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息

        if($op_type == 1){
            $rstData['data']['message'] = $client_info->nick_name."成为管理员";
        }else{
            $rstData['data']['message'] = $client_info->nick_name."被取消管理员";
        }

        $messageHelper = new \frontend\business\RongCloud\ChatroomMessageUtil();

        $extra = [
            'type'    => $op_type,
            'user_id' => $client_info->client_id,
            'user_name' => $client_info->nick_name,
        ];

        $rst = $messageHelper->sendChatroomSuperMsg(
            $living_id, $extra,
            $messageHelper::MSG_MANAGER_TAG,
            [],
            $rstData['data']['message']
        );
        if ($rst !== true) {
            $error = $rst;
            return false;
        }
        return true;
    }
} 