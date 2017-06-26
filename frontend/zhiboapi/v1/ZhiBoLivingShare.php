<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v1;

use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 获取直播分享信息
 * @package frontend\zhiboapi\v3
 */
class ZhiBoLivingShare implements IApiExcute
{

    /**
     * 获取直播分享信息
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $uniqueNo = $dataProtocal['data']['unique_no'];
        $user = ClientUtil::GetUserByUniqueId($uniqueNo);
        if(!$user)
        {
            $error = '用户不存在';
            return false;
        }
        if(empty($dataProtocal['data']['living_id']))
        {
            $error = '直播id不能为空';
            return false;
        }
        $result = LivingUtil::GetLivingClientInfo($dataProtocal['data']['living_id']);
        if($result == false){
            $result = [];
        }

        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$rand_str.=$strPol[rand(0,$max)],$i++);
        $params= [
            'unique_no' => $uniqueNo,
            'living_id' => $dataProtocal['data']['living_id'],
            'rand_str' => $rand_str,
            'time' => time()
        ];
        $sign = ClientUtil::GetClientSign($params);

        if(empty($result['title'])){
            $result['title'] = '陪朋友吃饭，不如上蜜播扯淡！';
        }

        $rstData['data']['link'] = 'https://'.$_SERVER['HTTP_HOST'].'/mibo2/Mobile/weixin/share/livingshare.html?unique_no='.$params['unique_no'].'&rand_str='.$rand_str.'&time='.$params['time'].'&p_sign='.$sign.'&living_id='.$params['living_id'];
        $rstData['data']['title'] = $result['title'];
        $rstData['data']['pic'] = $result['pic'];
        $rstData['data']['disc'] = '我在蜜播和帅哥美女聊天，就等你来！';
        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        return true;
    }
}


