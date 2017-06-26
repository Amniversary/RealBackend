<?php
namespace frontend\zhiboapi\v1;

use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;


/**
 * Class 续播
 * @package frontend\zhiboapi\v3
 */
class ZhiBoLivingContinue implements IApiExcute
{
    /**
     * 续播接口
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
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
        $room_no = $dataProtocal['data']['room_no'];
        $living_id = $dataProtocal['data']['living_id'];
        if(!isset($room_no) || empty($room_no))
        {
            $error = '房间号不能为空';
            return false;
        }
        if(!isset($living_id) || empty($living_id))
        {
            $error = '直播ID不能为空';
            return false;
        }
        $living_info = LivingUtil::GetLivingById($living_id);
        if(empty($living_info))
        {
            $error = '直播不存在';
            return false;
        }
        if($living_info->status == 2)
        {
            $rstData['has_data'] = '1';
            $rstData['data_type'] = 'json';
            $rstData['data'] = '';
            return true;
        }

        if(!LivingUtil::SetLivingContinue($living_id,$error))
        {
            return false;
        }
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = '';

        return true;
    }
}