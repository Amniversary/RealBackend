<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v3;

use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;


/**
 * Class 获取直播结束信息
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetFinishLivingInfo implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type','living_id'];//'wish_type_id',
        $fieldLabels = ['唯一id','登录类型','直播id'];//'愿望类别id',
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]]))
            {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }
        return true;
    }
    /**
     * 获取直播分享信息
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
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
        $data = LivingUtil::GetFinishLivingInfo($living_id);
        if(!isset($data))
        {
            $error ='直播记录不存在';
            return false;
        }
        //$heart_count = $data['heart_count'];
        //$to_heart_dis_time = SystemParamsUtil::GetSystemParam('heart_dis_time',false,'value1'); //心跳间隔时间
        $s = (empty($data['finish_time'])?time():strtotime($data['finish_time']))- strtotime($data['create_time']);
        $timeStr = UsualFunForStringHelper::GetHHMMSSBySeconds($s);
        $rstData['has_data']='1';
        $rstData['data_type']='json';
        $rstData['data']=[
            'attend_user_count'=>$data['person_count_total'],
            'tickets_num'=>sprintf('%d',$data['tickets_num']),//转为整数
            'living_time'=>$timeStr
        ];

        return true;
    }
}


