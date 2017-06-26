<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/13
 * Time: 11:05
 */

namespace frontend\zhiboapi\v3\niuniu;


use frontend\business\ApiCommon;
use frontend\business\GoldsAccountUtil;
use frontend\business\NiuNiuGameUtil;
use frontend\zhiboapi\IApiExcute;
use yii\log\Logger;

/**
 * 开始游戏协议 Hbh
 * Class ZhiBoStartNiuNiuGame
 * @package frontend\zhiboapi\v2\niuniu
 */
class ZhiBoStartNiuNiuGame implements IApiExcute
{
    function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        if(!$this->check_param_ok($dataProtocal,$error))
        {
            return false;
        }

        $unique_no = $dataProtocal['data']['unique_no'];
        if(!ApiCommon::GetLoginInfo($unique_no, $LoginInfo, $error))
        {
            return false;
        }
        \Yii::$app->cache->delete('niuniu_game_info_'.$dataProtocal['data']['living_id']);
        \Yii::$app->cache->delete('niuniu_game_'.$dataProtocal['data']['living_id']);
        $gold_balance = GoldsAccountUtil::GetGoldsAccountModleByUserId($LoginInfo['user_id']);
        if($gold_balance->gold_account_balance < 300)
        {
            $error = '游戏币余额不足，无法创建游戏';
            \Yii::getLogger()->log($error.': gold_money'.$gold_balance->gold_account_balance,Logger::LEVEL_ERROR);
            return false;
        }

        $data = [
            'game_id'=>$dataProtocal['data']['game_id'],
            'living_id'=>$dataProtocal['data']['living_id'],
        ];

        if(!NiuNiuGameUtil::CreateGameInfo($data, $outAll,$error))
        {
            return false;
        }

        $rstData['has_data'] = '1';
        $rstData['data_type'] = 'jsonarray';
        $rstData['data'] = $outAll;
        return true;
    }

    private function check_param_ok($dataProtocal,&$error='')
    {
        $fields = ['unique_no','game_id','living_id'];
        $fieldLabels = ['唯一号','游戏uuid','直播间id'];
        $len =count($fields);
        for($i = 0; $i <$len; $i ++)
        {
            if (!isset($dataProtocal['data'][$fields[$i]]) || empty($dataProtocal['data'][$fields[$i]])) {
                $error = $fieldLabels[$i] . '，不能为空';
                return false;
            }
        }

        return true;
    }
} 