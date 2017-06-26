<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v2;

use frontend\business\ChatGroupUtil;
use frontend\business\ClientUtil;
use frontend\business\EnterRoomNoteUtil;
use frontend\business\LivingUtil;
use frontend\business\RobotUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class 七牛机器人进入房间
 * @package frontend\zhiboapi\v2
 */
class ZhiBoQiNiuRobotEnterRoom implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['living_id'];//'wish_type_id',
        $fieldLabels = ['直播id'];//'愿望类别id',
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
            $clientInfo = ClientUtil::GetClientByUniqueNo($uniqueNo);
            if(!isset($clientInfo))
            {
                $error = '找不到用户信息，加入机器人失败';
                return false;
            }
            $loginInfo['user_id']=$clientInfo->client_id;
        }
        $user_id  = $loginInfo['user_id'];
        $passParams = $dataProtocal['data'];
        unset($passParams['unique_no']);
        unset($passParams['register_type']);
        $living_id = $passParams['living_id'];
        $living_info = LivingUtil::GetLivingById($living_id);
        $owner = 3;
        $is_police = ($loginInfo['client_type'] == '2'? '1' : '0');
        $error['attention'] = 0;
        if(!in_array($living_info->living_type,[3,4,5]))
        {
            //\Yii::getLogger()->log('enter room,user_id:'.$user_id.' time:'.strval(time()),Logger::LEVEL_ERROR);
            //开始计算经验或处理上次的经验
            if(!ChatGroupUtil::QiNiuEnterRoom($living_id,$user_id,$deviceType,$owner,$error,0,$is_police))
            {
                return false;
            }
        }
        //$sysMsg = EnterRoomNoteUtil::GetSystemMsgToArray();
        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data']=[
            'owner'=>$owner,
            'is_attention'=>$error['attention'],
            'is_police'=>$is_police
        ];
        //\Yii::getLogger()->log(var_export($out, true),Logger::LEVEL_ERROR);
        //根据经度、纬度获取地理信息
        return true;
    }
}