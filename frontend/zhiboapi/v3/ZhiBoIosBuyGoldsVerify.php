<?php
/**
 * Created by PhpStorm.
 * User: hlq
 * Date: 16-04-26
 * Time: 上午11:30
 */

namespace frontend\zhiboapi\v3;

use backend\business\GoodsUtil;
use common\components\IOSBuyUtil;
use common\components\IOSBuyGoldsUtil;
use common\components\SystemParamsUtil;
use common\components\UsualFunForStringHelper;
use common\models\GoldsGoods;

use frontend\business\GoldsGoodsUtil;
use frontend\business\BalanceUtil;
use frontend\business\ClientUtil;
use frontend\business\MultiUpdateContentUtil;

use frontend\business\RechargeListUtil;
use frontend\business\RewardUtil;

use \frontend\business\GoldsPrestoreUtil;
use common\models\GoldsAccount;

use frontend\business\SaveByTransUtil;
use frontend\business\SaveRecordByransactions\SaveByTransaction\CreateUserBalanceLogByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\ModifyBalanceByAddRealBean;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PrestoreRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeListRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\GoldsPrestoreRecordSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\RechargeSaveByTrans;
use frontend\business\SaveRecordByransactions\SaveByTransaction\PrestoreSaveByTrans;
use frontend\zhiboapi\IApiExcute;
use frontend\business\GoldsAccountUtil;
use yii\log\Logger;


/**
 * Class 苹果内购接口(仅用于ios)
 * @package frontend\zhiboapi\v3
 */
class ZhiBoIosBuyGoldsVerify implements IApiExcute
{

    /**
     * 苹果金币内购接口(仅用于ios)
     * @param string $error
     */
    public function excute_action($dataProtocal, &$rstData,&$error, $extendData= array())
    {
        $error = '';
        $rstData['has_data'] = 1;
        $rstData['data_type'] = 'json';
//        $error = '金币充值功能开发中，敬请期待！';
//        return false;
        $user = ClientUtil::GetUserByUniqueId($dataProtocal['data']['unique_no']);
        if(!$user)
        {
            $error = '用户不存在';
            return false;
        }

        //用户发来的参数
        $receipt_data = $dataProtocal['data']['receipt-data'];

        if($dataProtocal['data']['pay_type'] != '6'){
            $error = '支付类型错误';
            return false;
        }

        $GoldsGoodsModel = GoldsGoodsUtil::GetGoldGoodsModelOne($dataProtocal['data']['gold_goods_id']);
        if(empty($GoldsGoodsModel)){
            $error = '商品不存在';
            return false;
        }

        $insertTransActions[] = new PrestoreSaveByTrans($GoldsGoodsModel,'1',$user->client_id,1,'',$receipt_data);
        if (!GoldsPrestoreUtil::GoldPrestoreSaveByTransaction($insertTransActions, $outInfo, $error)) {
            return false;
        }

        $is_sandbox = MultiUpdateContentUtil::CheckVersionInCheck($dataProtocal['app_id'],'ios_version',$dataProtocal['app_version_inner']);
        \Yii::getLogger()->log("苹果金币充值版本号=====>".$is_sandbox,Logger::LEVEL_ERROR);
        if($is_sandbox)
        {
            $data = IOSBuyGoldsUtil::GetIosBuyVerify($receipt_data,true);  //调用苹果接口,沙盒
        }
        else
        {
            $data = IOSBuyGoldsUtil::GetIosBuyVerify($receipt_data,false);  //调用苹果接口，正式
        }

        if($data['status'] != 1){
            $error = '购买失败，'.'状态：'.$data['status'];
            \Yii::getLogger()->log($error,Logger::LEVEL_ERROR);
            return false;
        }


        
        $goldPrestoreModel = GoldsPrestoreUtil::GetGoldPrestoreModelById($outInfo['prestore_id']);
        $other_pay_bill = $data['trade_no']; //苹果返回的单号
        $transActions[] = new PrestoreRecordSaveByTrans($goldPrestoreModel, ['other_pay_bill'=>$other_pay_bill,'user_name'=>$user->nick_name]);
        if(!GoldsPrestoreUtil::GoldPrestoreSaveByTransaction($transActions, $outInfo, $error)){
                return false;
        }

        $GoldsAccountModel = GoldsAccount::findOne(['user_id'=>$user->client_id]);
        $returnVal = GoldsAccountUtil::UpdateGoldsAccountToAdd($GoldsAccountModel->gold_account_id ,$user->client_id,$dataProtocal['device_type'],1,$GoldsGoodsModel->gold_num,$error);
        if($returnVal)
        {
            $error = '购买成功';
            $rstData['errno']  = 0;
            $rstData['errmsg'] = $error;
            $rstData['data']['golds_num'] = $GoldsGoodsModel->gold_goods_price;
        }else
        {
            \Yii::getLogger()->log("苹果金币充值失败=====>".$error,Logger::LEVEL_ERROR);
            return false;
        }

        return true;

    }
}


