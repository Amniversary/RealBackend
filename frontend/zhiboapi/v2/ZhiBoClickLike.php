<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v2;

use frontend\business\JobUtil;
use frontend\business\RewardUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateClickLikeSaveForReward;
use frontend\zhiboapi\IApiExcute;
use yii\db\Query;
use yii\log\Logger;


/**
 * Class 点赞
 * @package frontend\zhiboapi\v2
 */
class ZhiBoClickLike implements IApiExcute
{

    /**
     * 点赞
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';

        $query = new Query();
        $living_info = $query->from('mb_living')->select(['status'])->where(['living_id'=>$dataProtocal['data']['living_id']])->one();

        switch($living_info['status'])
        {
            case 0:
                $error = [
                    'errno'=>'1105',
                    'errmsg' =>'直播已结束'
                ];
                return false;
            case 1:
                $error = [
                    'errno'=>'1103',
                    'errmsg' =>'直播已暂停'
                ];
                return false;
            case 3:
                $error = [
                    'errno'=>'1104',
                    'errmsg' =>'直播已禁止'
                ];
                return false;
        }

        /***添加banstalk***/
        if(!JobUtil::AddClickLikeJob('click_like',['living_id'=>$dataProtocal['data']['living_id']],$error)){
            return false;
        }

        $rstData['has_data'] = '0';
        $rstData['data_type'] = 'string';
        $rstData['data'] = [];

        return true;
    }
}


