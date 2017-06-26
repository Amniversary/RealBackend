<?php
/**
 * 愿望修改配置文件
 * Created by PhpStorm.
 * User: John
 * Date: 2016/1/3
 * Time: 16:00
 */
return [
    'reward'=>'frontend\business\WishModifyActions\WishModifyByReward',//打赏
    'view'=>'frontend\business\WishModifyActions\WishModifyByView',//浏览
    'collect'=>'frontend\business\WishModifyActions\WishModifyByCollect',//收藏
    'comment' =>'frontend\business\WishModifyActions\WishModifyByComment',//评论
    'to_balance'=>'frontend\business\WishModifyActions\WishModifyToBalance',//转愿望金额到余额
    'change_finish_status'=>'frontend\business\WishModifyActions\WishModifyChangeFinishStatus',//修改结束状态 过期或结束
    'change_back_status'=>'frontend\business\WishModifyActions\WishModifyChangeBackStatus',
    'change_back_count_and_money'=>'frontend\business\WishModifyActions\WishModifyChangeBackCountAndMoney',
    'cancel_wish'=>'frontend\business\WishModifyActions\WishModifyChangeCancelStatus',
    'red_packet_money'=>'frontend\business\WishModifyActions\WishModifyRedPacketMoney',
    'attrs'=>'frontend\business\WishModifyActions\WishModifySomeAttrubutes',
    'to_balance_for_check'=>'frontend\business\WishModifyActions\WishModifyToBalanceForCheck',
    'refuse_to_wish_money_to_balance'=>'frontend\business\WishModifyActions\WishModifyChangeRefusedForWishMoneyToBalance',
    'accept_to_wish_money_to_balance'=>'frontend\business\WishModifyActions\WishModifyChangeAcceptForWishMoneyToBalance',
];