<?php
/**
 * Created by PhpStorm.
 * User=> John
 * Date=> 2015/12/12
 * Time=> 16=>15
 */

namespace frontend\controllers\MybackActions;


use frontend\business\ApiLogUtil;
use frontend\business\WishUtil;
use yii\base\Action;
use yii\log\Logger;

class SetFinishStatusAction extends Action
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
        //处理过期
        $overList = WishUtil::GetOverTimeWish(100);
        foreach($overList as $overOne)
        {
            $error = '';
            if(!WishUtil::WishModify($overOne,'change_finish_status',$error,['finish_status'=>4]))
            {
                \Yii::getLogger()->log('设置愿望过期异常： '.$error.' '.strval($overOne->wish_id),Logger::LEVEL_ERROR);
                continue;
            }
        }
        //处理完成
        $finishList = WishUtil::GetFinishWish(100);
        foreach($finishList as $finishOne)
        {
            $error = '';
            if(!WishUtil::WishModify($finishOne,'change_finish_status',$error,['finish_status'=>2]))
            {
                \Yii::getLogger()->log('设置愿望完成异常： '.$error.' '.strval($finishOne->wish_id),Logger::LEVEL_ERROR);
                continue;
            }
        }
        $time2 = microtime(true);
        $disTime = round($time2 - $time1, 3);//单位秒
        $apiLog = ApiLogUtil::GetNewModel('dealfinishstatus',strval($disTime),'','',$this->className());
        ApiLogUtil::SaveApiLog($apiLog);
        //输出处理结果
        echo 'ok '.date('Y-m-d H:i:s');
    }
} 