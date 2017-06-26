<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-24
 * Time: 下午10:09
 */

return [
    '1'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForAll',//能打赏所有愿望的红包，无限制
    '2'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForAllWithCondition',//能打赏所有愿望的红包，满多少才能用
    '4'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForWishType',//只能打赏某个类别愿望的红包
    '8'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForWishTypeWithCondition',//只能打赏某个类别愿望的红包，满多少才能用
    '16'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForWish',//只能打赏某个愿望
    '32'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsForWishWithCondition',//只能专项打赏某个愿望，并且满多少才能用
    '64'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckDirectRewardRedPackets',//直接奖励红包 1比3，10元封顶，加入账户余额
    '256'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRewardRedPacketsBaseCertification',//奖励愿望红包 1:2 5元封顶，打赏人初级认证就能用
    '257'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsRewardSelf',//种子红包的检查
    '258'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsRewardSelfWithCondition',//种子红包满多少才能用
    '260'=>'frontend\business\RedPacketsKinds\RedPacketsChecks\CheckRedPacketsSign',//签到的红包的检测
];