<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/3
 * Time: 10:00
 */

return [
    'userliving'=>'frontend\controllers\MblivingActions\MbUserLevelAction',
    'livingpay' => 'frontend\controllers\MblivingActions\MbLivingPayAction',
    'livingbind' =>'frontend\controllers\MblivingActions\MbLivingBindUserAction',
    'otherpay'=>'frontend\controllers\MblivingActions\GetOtherPayParamsAction',
    'cancelpay'=>'frontend\controllers\MblivingActions\CancelOtherPayAction',
    'livinggoods' => 'frontend\controllers\MblivingActions\MbLivingGoodsAction',
    'livingshare' => 'frontend\controllers\MblivingActions\MbWebShareAction',
    'livingnewst' => 'frontend\controllers\MblivingActions\MbWebNewstAction',
    'livingapprove' => 'frontend\controllers\MblivingActions\MbWebApproveAction',
    'livingsendverify' =>'frontend\controllers\MblivingActions\MblivingSendVerify',
    'wxwithdraw' => 'frontend\controllers\MblivingActions\WxWithdrawAction',
    'wxgoodslist' => 'frontend\controllers\MblivingActions\MbWxGetGoodsListAction',
    'wxdrawmoney' =>'frontend\controllers\MblivingActions\MbWxDrawMoneyAction',
    'rechargeresult'=>'frontend\controllers\MblivingActions\GetOtherPayRechargeResultAction',
    'recharge_login'=>'frontend\controllers\MblivingActions\PCRechargeLoginAction',
    'getlivingmasterinfo'=>'frontend\controllers\MblivingActions\MbWebGetLivingMasterInfoAction',
    'wechatshareinfo'=>'frontend\controllers\MblivingActions\MbWebWeChatShareInfoAction',
    'livingtime'=>'frontend\controllers\MblivingActions\MblivingTimeAction',
    'livingstatistics'=>'frontend\controllers\MblivingActions\MblivingStatisticsAction',
    'living_attention'=>'frontend\controllers\MblivingActions\MblivingAttentionAction',
    'piccode'=>[
        'class'=>'frontend\controllers\MblivingActions\MbCaptchaAction',
        'backColor'=>0xFFFFFF,
        'maxLength'=>'4',       // 最多生成几个字符
        'minLength'=>'4',       // 最少生成几个字符
        ],
    //'activityindex'=>'frontend\controllers\MblivingActions\ActivityIndexAction',
    'scoreboard'=>'frontend\controllers\MblivingActions\ActivityScoreBoardAction',
    'cut_img'=>'frontend\controllers\MblivingActions\MbGetImageAction',
    'getwechatshare' => 'frontend\controllers\MblivingActions\MbWebGetWeCachatSignAction',
    'add_enroll' => 'frontend\controllers\MblivingActions\MbWebAddEnrollAction',
    'get_lucky_draw_info'=>'frontend\controllers\MblivingActions\MbLuckyDrawInfoAction',
    'do_activity_prize'=>'frontend\controllers\MblivingActions\MbWebDoActivityPrizeAction',
    'open_living' => 'frontend\controllers\MblivingActions\MbWebOpenLivingAction',
    'wxgoldgoods'=>'frontend\controllers\MblivingActions\MbWeChatGoldGoodsAction',
    'wxgoldresult'=>'frontend\controllers\MblivingActions\GetOtherGoldPayRechargeResultAction',
    'get_downloadlink'=>'frontend\controllers\MblivingActions\GetDownloadLinkAction',
    'get_youdun_info'=>'frontend\controllers\MblivingActions\MblivingGetYouDunInfo',
    'withdraw_livingbind'=>'frontend\controllers\MblivingActions\MbWithdrawLivingBindUserAction',
    'activity_girls'=>'frontend\controllers\MblivingActions\MbActivityGirlAction',
    'activity_living'=>'frontend\controllers\MblivingActions\MbActivityLivingAction',
];