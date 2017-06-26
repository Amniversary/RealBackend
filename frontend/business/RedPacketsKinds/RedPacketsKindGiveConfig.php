<?php
/**
 * 红包领取配置表
 * Created by PhpStorm.
 * User: John
 * Date: 2015/12/25
 * Time: 17:35
 */
return [
    '1'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForAll',//能打赏所有愿望的红包，无限制
    '2'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForAllWithCondition',//能打赏所有愿望的红包，满多少才能用
    '4'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForWishType',//只能打赏某个类别愿望的红包
    '8'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForWishTypeWithCondition',//只能打赏某个类别愿望的红包，满多少才能用
    '16'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForWish',//只能打赏某个愿望
    '32'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForWishWithCondition',//只能专项打赏某个愿望，并且满多少才能用
    '64'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveDirectRewardRedPackets',//直接奖励红包 1比3，10元封顶，加入账户余额
    '256'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRewardRedPacketsBaseCertification',//奖励愿望红包 1:2 5元封顶，打赏人初级认证就能用
    '257'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsRewardSelf',//种子红包的检查
    '258'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsRewardSelfWithCondition',//种子红包满多少才能用
    '259'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsRandWithCondition',//发送随机红包
    '260'=>'frontend\business\RedPacketsKinds\RedPacketsGives\GiveRedPacketsForSign',//签到红包
];