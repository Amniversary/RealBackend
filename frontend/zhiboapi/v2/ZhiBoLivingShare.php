<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v2;

use frontend\business\ClientUtil;
use frontend\business\LivingUtil;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 获取直播分享信息
 * @package frontend\zhiboapi\v2
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

        $user = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
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
//        $configAry = \Yii::$app->params['default_param_api'];
//        $key = $configAry['token'];


        $length = 40;
        $rand_str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $rand_str.=$strPol[rand(0,$max)];
        }

//        $sign = sha1('living_id='.$dataProtocal['data']['living_id'].'&rand_str='.$rand_str.'&time='.time().'&key='.$key);
//
//        $result['desc'] = $result['title'];
        //$result['link'] = 'http://'.$_SERVER['HTTP_HOST'].'/fuck/test2?living_id='.$dataProtocal['data']['living_id'].'&rand_str='.$rand_str.'&time='.time().'&sign='.$sign;

        $params= [
            'unique_no' => $dataProtocal['data']['unique_no'],
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
        //\Yii::getLogger()->log('living_share_rsdata======'.var_export($rstData,true),Logger::LEVEL_ERROR);
        return true;
    }
}


