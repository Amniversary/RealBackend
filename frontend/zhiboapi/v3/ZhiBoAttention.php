<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-13
 * Time: 下午9:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ChatUtil;
use frontend\business\JobUtil;
use frontend\zhiboapi\IApiExcute;
use frontend\business\ApiCommon;
use yii\log\Logger;

/**
 * Class 关注
 * @package frontend\zhiboapi\v3
 */
class ZhiBoAttention implements IApiExcute
{

    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type','attention_id'];
        $fieldLabels = ['唯一id','登录类型','关注用户的id'];
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

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        //\Yii::getLogger()->log('请求信息:'.var_export($dataProtocal, true),Logger::LEVEL_ERROR);
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
        unset($passParams['device_no']);
        unset($passParams['phone_no']);
        $attention_id=$passParams['attention_id'];
        //相互加为好友，单向关注
        if(!ChatUtil::Attention($user_id,$attention_id,$error))
        {
            return false;
        }

        //加入异步任务处理
        $data=[
            'user_id'=>$user_id,
            'attention_id'=>$attention_id,
            'op_type'=>'attention'
        ];
        if(!JobUtil::AddAttentionJob('user_attention',$data,$error))
        {
            \Yii::getLogger()->log('job save error:'.$error,Logger::LEVEL_ERROR);
        }
        //根据经度、纬度获取地理信息
        return true;
    }
} 