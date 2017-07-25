<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\FuckActions;


use backend\business\GoodsUtil;
use backend\business\StatisticActiveUserUtil;
use backend\business\StatisticLivingProfitAndTime;
use common\components\Des3Crypt;
use common\components\getui\GeTuiUtil;
use common\components\IOSBuyUtil;
use common\components\OssUtil;
use common\components\tenxunlivingsdk\TimRestApi;
use common\components\UsualFunForNetWorkHelper;
use common\components\UsualFunForStringHelper;
use common\components\WaterNumUtil;
use common\components\Yii2ValidateCode;
use frontend\business\AttentionUtil;
use frontend\business\BalanceUtil;
use frontend\business\ChatFriendsUtil;
use frontend\business\ChatGroupUtil;
use frontend\business\ChatPersonGroupUtil;
use frontend\business\ChatUtil;
use frontend\business\ClientInfoUtil;
use frontend\business\ClientUtil;
use frontend\business\ImageUtil;
use frontend\business\JobUtil;
use frontend\business\LivingUtil;
use frontend\business\RedPacketsUtil;
use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateLivingSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddTicket;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddVirtualBean;
use frontend\business\TicketToCashUtil;
use OSS\OssClient;
use yii\base\Action;
use yii\base\Exception;
use yii\caching\MemCache;
use yii\log\Logger;
use common\components\AESCrypt;

class TestLxy2Action extends Action
{

    public function run()
    {
        $outImgPath='';
        $error='';
        $text =[
            '因为老师从小就教我',
            '幸福要靠自己的双手',
            '幸福要靠自己的双手',
        ];
        $title = '我是网红';
        $start = microtime(true);
        ImageUtil::imagecropper('./mibo/wswh/a.png','./mibo/wswh/bg.png',$text,$title,$outImgPath,$error,$out_img_width = 752,$out_img_height=1184,$cut_dst_x = 1290,$cut_dst_y = 888,$scale = 14 );
        $end = microtime(true);
        var_dump($end - $start);
        echo $outImgPath;
        var_dump(checkdnsrr('www.baidu.cwem'));
        exit;
        $p = \Yii::$app->request->getQueryParam('unique_no');
        var_dump($p);
        exit;

    }

} 