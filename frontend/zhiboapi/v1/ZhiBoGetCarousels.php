<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/4/28
 * Time: 9:04
 */

namespace frontend\zhiboapi\v1;


use common\components\SystemParamsUtil;
use frontend\business\ActivityUtil;
use frontend\business\ApiCommon;
use frontend\business\CarouselUtil;
use frontend\business\MultiUpdateContentUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 获取轮播图列表   hbh
 * Class ZhiBoGetCarousels
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGetCarousels implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $device_type = $dataProtocal['device_type'];
        $app_id = $dataProtocal['app_id'];
        $version = $dataProtocal['app_version_inner'];
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $status = 2;
        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }
        $user_id = $LoginInfo['user_id'];
        //\Yii::getLogger()->log('轮播图:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
        $isMultiVersionStr = SystemParamsUtil::GetSystemParam('mb_mulit_version_module',true,'value1');
        $multiVersions = json_decode($isMultiVersionStr,true);
        if($device_type == 1) {
            $module_id = $multiVersions[0];  //安卓
        } else {
            $module_id = $multiVersions[1];  //苹果
        }
        if(!MultiUpdateContentUtil::CheckVersionInCheck($app_id,$module_id,$version)){
            $status = 1;
        }

        //action_content
        $carouselInfo = CarouselUtil::GetCarouselInfo($status,true);
        $replace_params = [
            '@unique_no'=>[$uniqueNo,0],
            '@unique_new'=>[$uniqueNo,1]
            ];
        $rst = [];
        $i = 0;
        foreach($carouselInfo as $carouse)
        {
            $str = $this->FormateParam($carouse['action_content'],$replace_params);
            $carouse['action_content'] = $str;
            $rst[] = $carouse;
            $i ++;
        }

        $rstData['has_data'] = count($carouselInfo) > 0 ? '1' : '0';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $rst;
        return true;
    }

    /**
     * 重新签名
     * @param $url_str
     */
    private function Resign($url_str)
    {
        $items = explode('?',$url_str);
        if(!is_array($items) || count($items) < 2)
        {
            return $url_str;
        }
        $params = explode('&',$items[1]);
        if(count($params) < 2)
        {
            return $url_str;
        }
        $signParams = [];
        foreach($params as $oneParam)
        {
            $subItems = explode('=',$oneParam);
            if(!is_array($subItems) || count($subItems) !== 2)
            {
                continue;
            }
            $signParams[$subItems[0]] = $subItems[1];
        }
        unset($signParams['sign']);
        $signStr = ActivityUtil::GetActivitySign($signParams);
        $signParams['sign']=$signStr;
        $tempStr = '';
        foreach($signParams as $key => $v)
        {
            $tempStr .= sprintf('%s=%s&',$key,$v);
        }
        if(!empty($tempStr))
        {
            $tempStr = substr($tempStr,0,strlen($tempStr)-1);
        }
        $url_str = sprintf('%s?%s',$items[0],$tempStr);
        return $url_str;
    }

    /**
     * 格式化参数
     * @param $source_str
     * @param $replace_params
     */
    private function FormateParam($source_str,$replace_params)
    {
        foreach($replace_params as $key=>$v)
        {
            if(strpos($source_str,$key)!== false && $v[1] === 1)
            {
                $source_str = str_replace($key,$v[0],$source_str);
                $source_str = $this->Resign($source_str);
            }
            else
            {
                $source_str = str_replace($key,$v[0],$source_str);
            }
        }
        return $source_str;
    }
} 