<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/9
 * Time: 16:52
 */

namespace frontend\zhiboapi\v2;


use frontend\business\ApiCommon;
use frontend\business\DynamicUtil;
use frontend\business\JobUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\DynamicSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 蜜圈发布动态接口
 * Class ZhiBoAddDynamic
 * @package frontend\zhiboapi\v2
 */
class ZhiBoAddDynamic implements IApiExcute
{

    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {

        //\Yii::getLogger()->log('dafadadada:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $dynamic_type = $dataProtocal['data']['dynamic_type'];
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no,$LoginInfo,$error))
        {
            return false;
        }
        $content = '';
        if(!empty($dataProtocal['data']['content']))
        {
            $content = $dataProtocal['data']['content'];
        }

        if($dynamic_type == 1)
        {
            $data = [
                'user_id'=>$LoginInfo['user_id'],
                'dynamic_type'=>$dynamic_type,
                'content'=>$content,
                'city'=>$dataProtocal['data']['city'],
                'pic'=>$dataProtocal['data']['pic'],
                'dim_pic'=>'',
                'red_pic_money'=>'0',
            ];
        }
        else
        {
            $data = [
                'user_id'=>$LoginInfo['user_id'],
                'dynamic_type'=>$dynamic_type,
                'content'=>$content,
                'city'=>$dataProtocal['data']['city'],
                'pic'=>$dataProtocal['data']['pic'],
                'dim_pic'=>$dataProtocal['data']['dim_pic'],
                'red_pic_money'=>intval($dataProtocal['data']['red_money'])
            ];
        }

        $model = DynamicUtil::GetDynamicModel($data);
        /*if(!JobUtil::AddDynamicJob('add_dynamic',$data,$error))
        {
            return false;
        }*/
        $transActions = new DynamicSaveByTrans($model);
        if(!$transActions->SaveRecordForTransaction($error,$out))
        {
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = '';
        return true;
    }


    private function check_param_ok($dataProtocal,&$error='')
    {
        $dynamic_type = $dataProtocal['data']['dynamic_type'];
        if(!isset($dynamic_type) || empty($dynamic_type))
        {
            $error = '动态类型不能为空';
            return false;
        }
        if($dynamic_type == 1)
        {
            $fields = ['unique_no','pic'];
            $fieldLabels = ['唯一号','图片'];
            $len =count($fields);
            for($i = 0; $i <$len; $i ++)
            {
                if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                    $error = $fieldLabels[$i] . '，不能为空';
                    return false;
                }
            }
        }
        else
        {
            $fields = ['unique_no','pic','dim_pic','red_money'];
            $fieldLabels = ['唯一号','图片','模糊图片','红包金额'];
            $len =count($fields);
            for($i = 0; $i <$len; $i ++)
            {
                if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                    $error = $fieldLabels[$i] . '，不能为空';
                    return false;
                }
            }
        }

        return true;
    }
} 