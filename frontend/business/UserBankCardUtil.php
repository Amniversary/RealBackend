<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-12-22
 * Time: 下午9:18
 */

namespace frontend\business;


use common\models\UserBankCard;
use yii\log\Logger;

class UserBankCardUtil
{

    /**
     * 获取个人银行卡数量
     * @param $user_id
     * @return int|string
     */
    public static function GetBankCardCount($user_id)
    {
        return UserBankCard::find()->where(['user_id'=>$user_id])->count();
    }
    /**
     * 新增银行卡
     * @param $attrs
     * @param $error
     * @return bool
     */
    public static function AddBankCard($attrs, &$error)
    {
        $model = new UserBankCard();
        $model->attributes = $attrs;
        if(!$model->save())
        {
            $error = '银行卡信息保存失败';
            \Yii::getLogger()->log(var_export($model->getErrors(), true),Logger::LEVEL_ERROR);
            return false;
        }
        $error = $model->user_bank_card_id;
        return true;
    }
    /**
     * 检测卡是否存在
     * @param $card_no
     */
    public static function ChcekCardExist($card_no)
    {
        $rc = self::GetCardInfoByCardNo($card_no);
        return isset($rc);
    }

    /**
     * 获取银行图片
     * @param $name 银行名称
     * @return string
     */
    public static function GetPicUrlByBankName($name)
    {
        $bankPicList = require(\Yii::$app->getBasePath().'/../common/config/BankCardPicInfo.php');
        if(isset($bankPicList[$name]))
        {
            return $bankPicList[$name];
        }
        //默认图片
        return 'http://oss.aliyuncs.com/meiyuan/wish_type/default.png';
    }

    /**
     * 根据卡号获取卡信息
     * @param $card_no
     * @return null|static
     */
    public static function GetCardInfoByCardNo($card_no)
    {
        return UserBankCard::findOne([
            'card_no' => $card_no
        ]);
    }

    /**
     * 删除银行卡信息
     * @param $user_bank_card_id
     * @param $error
     */
    public static function DelBankCard($user_bank_card_id,&$error)
    {
        $rc = self::GetCardInfoById($user_bank_card_id);
        if(!isset($rc))
        {
            $error = '银行卡信息不存在';
            return false;
        }
        if($rc->delete() === false)
        {
            $error = '系统错误，删除失败';
            return false;
        }
        return true;
    }
    /**
     * 根据id获取银行卡信息
     * @param $bank_id
     */
    public static function GetCardInfoById($bank_id)
    {
        return UserBankCard::findOne([
            'user_bank_card_id'=>$bank_id
        ]);
    }

    /**
     * 获取用户的一张银行卡
     * @param $user_id
     */
    public static function GetOneBankCardInfoByUserId($user_id)
    {
        return UserBankCard::findOne(['user_id'=>$user_id]);
    }

    /**
     * 获取银行卡记录
     */
    public static function GetBankCardList($user_id)
    {
        return UserBankCard::find()->where(['user_id'=>$user_id])->all();
    }

    /**
     * 格式化银行卡
     * @param $recordList
     */
    public static function GetFormateBankCardList($recordList)
    {
        $out = [];
        if(empty($recordList))
        {
            return $out;
        }
        foreach($recordList as $one)
        {
            $card_no_last4 = $one->card_no;
            $len = strlen($card_no_last4);
            $card_no_last4 = substr($card_no_last4, $len -4);
            $ary=[
                'user_bank_card_id'=>$one->user_bank_card_id,
                'card_type'=>$one->card_type,
                'bank_name'=>$one->bank_name,
                'card_no'=>$card_no_last4,
                'card_no_all'=>$one->card_no,
                'protocol_no' => $one->protocol_no,
                'pic'=>$one->pic,
                'user_name'=>$one->user_name,
                'identity_no'=>$one->identity_no,
            ];
            $out[] = $ary;
        }
        return $out;
    }

    /**
     * 获取连连支付需要的银行卡
     * @param $user_id
     */
    public static function GetOneHasProtocalNoBankCardByUserIdForLlPay($user_id)
    {
        return UserBankCard::findOne(['and',['user_id'=>$user_id],'protocol_no is not null','protocol_no <> \'\'']);
    }
} 