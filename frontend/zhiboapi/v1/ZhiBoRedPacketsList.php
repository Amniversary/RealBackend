<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-06-23
 * Time: 15:36
 */

namespace frontend\zhiboapi\v1;

use frontend\business\RedPacketMainUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * Class 抢到红包的用户信息列表接口
 * @package frontend\zhiboapi\v3
 */
class ZhiBoRedPacketsList implements IApiExcute
{
    /**
     * 检查参数合法性
     * @param string $error
     */
    private function check_param_ok($dataProtocal,&$error='')
    {
        if(!isset($dataProtocal['data']['red_packet_id']) || empty($dataProtocal['data']['red_packet_id']))
        {
            \Yii::getLogger()->log('红包ID不存在  action=ZhiBoRedPacketsList',Logger::LEVEL_ERROR);
            $error = '红包ID不存在！';
            return false;
        }
        return true;
    }

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }
        $redPacketListInfo = RedPacketMainUtil::getRedPacketSonLiset($dataProtocal['data']['red_packet_id']);
        if(!$redPacketListInfo)
        {
            $redPacketListInfo = [];
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'json';
        $rstData['data'] = [
            'red_user_list'=>$redPacketListInfo,
        ];
        //$rstData['red_user_list'] =$redPacketListInfo;
        return true;
    }
}