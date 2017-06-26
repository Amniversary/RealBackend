<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-06-23
 * Time: 15:36
 */

namespace frontend\zhiboapi\v1;

use frontend\business\RedPacketsUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 抢红包
 * @package frontend\zhiboapi\v1
 */
class ZhiBoGrabRedPackets implements IApiExcute
{
    private $packetInfo = [];


    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        \Yii::error('抢红包接口:'.var_export($dataProtocal,true));
        $uniqueNo = $dataProtocal['data']['unique_no'];
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            $rstData['data']['status'] = '1';
            $rstData['data']['money'] = '0';
            $rstData['data']['lucky'] = '1';
            \Yii::error('抢红包接口返回:1'.var_export($rstData,true));
            //return false;
            return true;
        }
        $this->packetInfo['device_type'] = (int)$dataProtocal['device_type'];
        \Yii::error('PacketInfo: '.var_export($this->packetInfo,true));
        if(!RedPacketsUtil::DoRedPacket($this->packetInfo,$uniqueNo,$outInfo,$error))
        {
            $rstData['data']['status'] = '1';
            $rstData['data']['money'] = '0';
            $rstData['data']['lucky'] = '1';
            \Yii::error('抢红包接口返回:2'.var_export($rstData,true));
            //return false;
            return true;
        }


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [
            'status'=>'0',
            'money' => $outInfo['op_value'],
            'lucky' => $outInfo['lucky'],
        ];
        \Yii::error('抢红包接口返回:3'.var_export($rstData,true));
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','register_type','red_packet_id'];
        $fieldLabels = ['唯一id','登录类型'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        $red_packet_info = \Yii::$app->cache->get('red_packet_id_'.$dataProtocal['data']['red_packet_id']);
        if(empty($red_packet_info))
        {
            $error = '红包已经抢完啦！';
            return false;
        }
        $this->packetInfo = json_decode($red_packet_info,true);
        return true;
    }
}