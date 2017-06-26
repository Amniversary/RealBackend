<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\MybackActions;


use common\components\PhpLock;
use frontend\business\ApiLogUtil;
use frontend\business\PersonalUserUtil;
use frontend\business\ReportUtil;
use frontend\business\RewardUtil;
use frontend\business\UserAccountInfoUtil;
use frontend\business\WishUtil;
use yii\base\Action;
use yii\log\Logger;

class BackWishMoneyAction extends Action
{
    /**
     * 检查参数
     * @param $error
     * @return bool
     */
    private function check_post_params(&$error)
    {
        $error = '';
        //$rand_str, $time, $token,$data,$token_other
        if(!isset($_GET['backvelidatekey']) || empty($_GET['backvelidatekey']))
        {
            $error = '参数缺少';
            \Yii::getLogger()->log('lost param:'.var_export($_GET,true),Logger::LEVEL_ERROR);
            return false;
        }
        return true;
    }

    public function run()
    {
        set_time_limit(0);
        $time1 = microtime(true);
        $rst = [

            "errno"=>"0",
            "errmsg"=>"提示信息",
        ];
        $error = '';
        if(!$this->check_post_params($error))
        {
            $rst['errno'] = '1';
            $rst['errmsg'] =$error;
            \Yii::getLogger()->log($rst['errmsg'],Logger::LEVEL_ERROR);
            echo json_encode($rst);
            exit;
        }
        $key = $_GET['backvelidatekey'];
        if($key !== \Yii::$app->params['backvelidatekey'])
        {
            \Yii::getLogger()->log('验证key不正确',Logger::LEVEL_ERROR);
            return;
        }

        $phpLock = new PhpLock('wish_back_money_backend_dealwith');
        $phpLock->lock();
        //处理过期
        $overList = WishUtil::GetBackMoneyWish(100);
        $len = count($overList);
        foreach($overList as $wish)
        {
            $rewardList = RewardUtil::GetBackRewardList($wish->wish_id);
            $hasError = false;
            while(count($rewardList) > 0)
            {
                foreach($rewardList as $rewardOne)
                {
                    if(!RewardUtil::BackMoneyByRewardList($wish, $rewardOne,$error))
                    {
                        var_dump($error);
                        $hasError =true;
                        $rewardOne->is_back = 3;
                        if(!$rewardOne->save())//退款失败
                        {
                            var_dump($rewardOne->getErrors());
                            \Yii::getLogger()->log(var_export($rewardOne->getErrors(),true),Logger::LEVEL_ERROR);
                            $phpLock->unlock();
                            exit;
                        }
                        break;
                    }
                }
                $rewardList = RewardUtil::GetBackRewardList($wish->wish_id);
            }
            $back_status = $hasError?4:3;
            if(!WishUtil::WishModify($wish,'change_back_status',$error,['back_status'=>$back_status]))
            {
                continue;
            }
        }

        $time2 = microtime(true);
        $disTime = round($time2 - $time1, 3);//单位秒
        $apiLog = ApiLogUtil::GetNewModel('dealbackwishmoney',strval($disTime),'deal_record_count:'.strval($len),'',$this->className());
        ApiLogUtil::SaveApiLog($apiLog);
        $phpLock->unlock();
        //输出处理结果
        echo 'ok record count:'.strval($len).' time:'.date('Y-m-d H:i:s');
    }
} 