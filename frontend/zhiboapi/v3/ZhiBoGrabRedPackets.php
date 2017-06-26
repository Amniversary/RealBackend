<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-06-23
 * Time: 15:36
 */

namespace frontend\zhiboapi\v3;

use frontend\business\RedPacketsUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 抢红包
 * @package frontend\zhiboapi\v3
 */
class ZhiBoGrabRedPackets implements IApiExcute
{
    private $new_red_packet_info = array();
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['red_packet_id']) || empty($dataProtocal['data']['red_packet_id']))
        {
            \Yii::getLogger()->log('红包ID不存在  action=ZhiBoGrabRedPackets;   data:'.var_export($dataProtocal,true),Logger::LEVEL_ERROR);
            $error = '红包已经抢完啦！';
            return false;
        }
        $red_packet_info = \Yii::$app->cache->get('red_packet_id_'.$dataProtocal['data']['red_packet_id']);
        if(!isset($red_packet_info) || empty($red_packet_info))
        {
            $error = '红包已经抢完啦！';
            return false;
        }
        $this->new_red_packet_info = json_decode($red_packet_info,true);
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {

        if(!$this->check_param_ok($dataProtocal,$error))
        {
            $rstData['data']['status'] = '1';
            $rstData['data']['money'] = '0';
            $rstData['data']['lucky'] = '2';
            //return false;
            return true;
        }
        $this->new_red_packet_info['device_type'] = (int)$dataProtocal['device_type'];
        if(!RedPacketsUtil::DoRedPacket($this->new_red_packet_info,$dataProtocal['data']['unique_no'],$outInfo,$error))
        {
            $rstData['data']['status'] = '1';
            $rstData['data']['money'] = '0';
            $rstData['data']['lucky'] = '2';
            //return false;
            return true;
        }

/*        $textcontent = '恭喜您，抢到'.$outInfo['op_value'].'元红包啦！';
        if(!TimRestApi::openim_send_custom_msg($outInfo['client_id'],$textcontent,$error))
        {
           \Yii::getLogger()->log('抢红包发送消息失败action = ZhiBoGrabRedPackets',Logger::LEVEL_ERROR);
        }*/


        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [
            'status'=>'0',
            'money' => $outInfo['op_value'],
            'lucky' => $outInfo['lucky'],
        ];
        //$rstData['money'] = $outInfo['op_value'];
        //$rstData['lucky'] = $outInfo['lucky'];
        return true;
    }
}