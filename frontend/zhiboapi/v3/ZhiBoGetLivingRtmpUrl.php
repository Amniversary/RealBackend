<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 下午5:30
 */

namespace frontend\zhiboapi\v3;

use frontend\business\ApiCommon;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use common\components\CdnAntiLeech;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 获取直播拉流地址
 * @package frontend\zhiboapi\v2
 */
class ZhiBoGetLivingRtmpUrl implements IApiExcute
{
    // 地址是否使用签名
    private $useSign = true;

    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';

        if (!isset($dataProtocal['data']['unique_no'])) {
            $error = '缺少参数 unique_no';
            return false;
        }

        if (!isset($dataProtocal['data']['living_id'])) {
            $error = '缺少参数 living_id';
            return false;
        }

        $uniqueNo = $dataProtocal['data']['unique_no'];
        $livingId = $dataProtocal['data']['living_id'];

        if(!ApiCommon::GetLoginInfo($uniqueNo,$LoginInfo,$error))
        {
            return false;
        }

        $data = LivingUtil::GetLivingById($livingId);
        $result = [];

        if (!empty($data)) {
            $result['pull_rtmp_url'] = $this->useSign ?
                CdnAntiLeech::signOnTimestamp($data['pull_rtmp_url']) : $data['pull_rtmp_url'];
        }

        $rstData['has_data']  = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data']      = $result;
        return true;
    }
}
